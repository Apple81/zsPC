<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class MesContro extends CI_Controller{
    /*
     * 公用函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MesContro_model','MesCon');
        $this->load->model('Form_model','form');
        $this->load->model('System_model','system');
    }
    /*
     * 功能函数-树节点
     */
    //显示树节点【接口版本（草稿）】
    public function GetTreeNode()
    {
        $projectId = $this->uri->segment(3);
        
        /*
         * 根据工程id查接口树节点
         * */
        $url = 'http://112.74.34.150:8080/TongXinweb/Tree/getTreeByProjectId?projectId='.$projectId;
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
        curl_setopt ( $ch, CURLOPT_POST, 1 ); //启用POST提交
        $file_contents = curl_exec ( $ch );
        //json解码
        $data = json_decode($file_contents,true);
        $array = array();
        $arrayNodeId = array();
        for ($i=0;$i<count($data['data']);$i++)
        {
            //如果这个表单模板已经存在
            if (! in_array($data['data'][$i]['nodeId'],$arrayNodeId))
            {
                $array[] = $data['data'][$i];
                $arrayNodeId[] = $data['data'][$i]['nodeId'];
            }
        }
        $json = json_encode($array);
        echo $json;
        
        curl_close ( $ch );
    }
    //显示树节点【系统版本（归集）】
    public function GetTreeNodePack()
    {
        $projectId = $this->uri->segment(3);
//      $projectId = '0b5c5b47-0927-48ec-a336-9b925881ec54';
        
        /*
         * 根据工程id查数据库树节点
         * */
        $data = $this->MesCon->GetTreeNodePack($projectId);
        $array = array();
        $arrayNodeId = array();
        for ($i=0;$i<count($data['data']);$i++)
        {
            //如果这个表单模板已经存在
            if (! in_array($data['data'][$i]['nodeId'],$arrayNodeId))
            {
                $array[] = $data['data'][$i];
                $arrayNodeId[] = $data['data'][$i]['nodeId'];
            }
        }
        $json = json_encode($array);
        echo $json;
    }
    //显示表单信息【用于草稿显示】
    public function ShowFormMes()
    {
        $formId = $this->uri->segment(3);
        //检测此表但是不是已经存在于缓存表中
        $FormSta = $this->MesCon->ShowFormMes($formId);
        
        /*
         * 根据表单id查询：此表单是否在数据库的非缓存表（table_mes_cache）中【如果不在则将信息保存进缓存表】
         * 查询缓存表，显示表单信息
         * */
        //首先将接口中的信息保存进数据库的缓存表中
        if(!$FormSta)
        {
            $url = 'http://112.74.34.150:8080/TongXinweb/form/getFormByFid?formId='.$formId;
            $ch = curl_init ();
            curl_setopt ( $ch, CURLOPT_URL, $url );
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
            curl_setopt ( $ch, CURLOPT_POST, 1 ); //启用POST提交
            $file_contents = curl_exec ( $ch );
            //json解码
            $data = json_decode($file_contents,true);
            curl_close ( $ch );
            //将信息保存进缓存表
            $this->MesCon->SaveFormMes($data['data']);
        }
        //返回缓存表中的信息
        $data = $this->form->FormMesLoad($formId,'table_mes_cache');
        $json = json_encode($data);
        echo $json;
    }
    //刷新树节点信息
    public function GetTreeNodeReload()
    {
        
    }
    
    /*
     * 功能函数-改变状态
     */
    //检测表单是不是包含表单类别
    public function FromTypeCheck()
    {
        $fromID = $this->input->post('formId');
        $TabName = $this->input->post('TabName');
//      $fromID = 'ec20151e-d586-440c-a87f-c16283b1175a,dd201023-bd20-4179-a3e8-45ccabe71a1e,47c8fe34-b634-4909-9525-bacb26ef1796';
//      $TabName = 'table_mes_cache';
		$Fid=array();
        $Fid=explode(',',$fromID);
        $num = count($Fid);
        for($i = 0;$i < $num;$i++){
        	$fromId= $Fid[$i];
        	$data['TypSta'] = $this->MesCon->FromTypeCheck($fromId,$TabName);
        }
        /*
         * 根据表单id和数据表名称判断是不是
         * */
        $json = json_encode($data);
        echo $json;
        
    }
    
    //改变状态【提交，归集，驳回，重新提交，撤回归集】
    public function StaChange()
    {
        //url用于删除接口数据
        $urlDel = 'http://112.74.34.150:8080/TongXinweb/form/deleteForm?formId=';
        $urlTree = 'http://112.74.34.150:8080/TongXinweb/Tree/getNodeByFid?formId=';
        
        $MesId = $this->input->post('formId');
        $ActTy = $this->input->post('ActTy');
        $proName=$this->input->post('proName');
        $Type = $this->uri->segment(3);
        $PageType = $this->uri->segment(4);
        
//      $ActTy = "draf";
//      $MesId = "ec20151e-d586-440c-a87f-c16283b1175a,dd201023-bd20-4179-a3e8-45ccabe71a1e,47c8fe34-b634-4909-9525-bacb26ef1796";
//      $PageType ='draf';
//      $Type = 'UpLoad';
//      $MesId = 'd540428c-6cad-4879-9892-edffad144fff';
//      echo $Type;
        /*
         * 判断用户进行的是什么操作【分成删除和非删除】
         *      如果进行的是删除操作=》则判断是提交前还是提交后的删除【提交前删除接口数据，提交后删除数据库数据】
         *      否则执行改变状态操作
         * */
        //判断是进行什么操作
        switch ($Type)
        {
            case 'UpLoad':$action = '提交';break;
            case 'Reject':$action = '驳回';break;
            case 'PackUp':$action = '归集';break;
            case 'CallBk':$action = '撤回归集';break;
            case 'ReUpld':$action = '重新提交';break;
            case 'Delete':$action = '删除';break;
            default:break;
        }
        
        //如果数据中包含','
        	$MesIdArr = explode(',',$MesId);
        
        //设置返回值与实际操作执行
        $data['status'] = 'fail';
        $data['rows'] = 0;
        $data['Name'] = $action;
            //如果是删除操作
        if($Type == 'Delete')
        
        {	
            $this->MesCon->StaChangeDel($MesIdArr,$urlDel);
            $data['status'] = 'success';
            $data['rows'] = $rows;
        }
        else if($Type == 'UpLoad'){
	            $dataRe = $this->MesCon->StaChange($MesIdArr,$Type,$PageType,$urlDel,$urlTree,$proName);
	            if($dataRe['rows'] > 0)
	            {
	                $data['status'] = 'success';
	                $data['rows'] = $dataRe['rows'];
	            }
         
        }
            //不是删除操作
        else{
        	$MesIdArr = explode(',',$MesId);
            $dataRe = $this->MesCon->StaChange($MesIdArr,$Type,$PageType,$urlDel,$urlTree,$proName);
            if($dataRe['rows'] > 0)
            {
                $data['status'] = 'success';
                $data['rows'] = $dataRe['rows'];
            }
        }
        
        //如果含有树结构
        if($ActTy == 'draf'){
            
        }
        if($ActTy == 'pack'){
            
        }
         
        $json = json_encode($data);
        echo $json;
    }
    /*
     * 打印
     *  */
    //打印页面跳转
    public function PrintOut()
    {
        $FormIdA = $this->uri->segment(3);
        $Type = $this->uri->segment(4);
        if($FormIdA == 'null')
        {
            $data['typeForm'] = $this->system->TypeM_selectMes(0);
            $this->load->view('form_rejt.html',$data);
            exit;
        }
        if(isset($Type))
        {
//          echo $Type;
            $data = $this->MesCon->PrintOutGetMes($FormIdA,'table_mes_cache');
            $this->load->view('printOutDraf.html',$data);
        }else{
            $data = $this->MesCon->PrintOutGetMes($FormIdA,'table_mes');
            $this->load->view('printOut.html',$data);
        }
    }
    //获取签名信息
    public function PrintOutGetSign()
    {
        $FormId = $this->input->post('formId');
//      $FormId = '12d9d360-8e6b-49d1-8884-8320c14f014e';
        $data = $this->MesCon->PrintOutGetSign($FormId);
//      print_r($data);
        $json = json_encode($data);
        echo $json;
    }
    
    /*
     * 功能函数-流转属性设置
     * 【放到此文件夹：】增加针对表单的审批流程的修改功能时可复用
     */
    //新建流转属性
    public function CirSave_New()
    {
        $TypeId = $this->input->post('TypeId');
        $CirMes = $this->input->post('data');
        
//      $TypeId = 24;
//      $CirMes[0] = '施工部门-专业工长';
//      $CirMes[1] = '施工部门-项目经理';
        
        /*
         * 
         * */
        $data['mes'] = $this->MesCon->CirSave_New($TypeId,$CirMes);
        if($data['mes'])
        {
            $data['success'] = 'ok';
        }
        unset($data['mes']);
        $json = json_encode($data['success']);
        echo $json;
    }
    //显示流转属性
    public function CirMes_Show()
    {
        $TypeId = $this->input->post('TypeId');
        
        /*
         * 
         * */
        $data['mes'] = $this->MesCon->CirMes_Show($TypeId);
        $json = json_encode($data);
        echo $json;
    }
    //修改流转属性
    public function CirSave_Change()
    {
//      $TypeId = $this->input->post('TypeId');
//      $CirMes = $this->input->post('data');
        /*
         * 
         * */
        $data['success'] = 'ok';
//      $data['sql'] = $this->MesCon->CirSave_New($TypeId,$CirMes);
        $json = json_encode($data);
        echo $json;
    }
    //获得当前工程的归集表单
    public function GetPackMes()
    {
        $projectId = $this->uri->segment(3);
        $array = $this->MesCon->GetPackMes($projectId);
        $json = json_encode($array);
        echo $json;
    }
    //工作组比对成功归集表单改变状态
    public function WgFinish(){
    	$TabMId=$_POST['TabMId'];
    	$proid = $this->uri->segment(3);
        $data = $this->MesCon->WgFinish($TabMId,$proid);
        $json = json_encode($data);
        echo $json;
    }
}