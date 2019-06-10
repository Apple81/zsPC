<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form extends CI_Controller{
    /*
     * 公用函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Form_model','form');
        $this->load->model('System_model','system');
    }
    /*
     * 页面显示
     */
        /*
         * API:
         *    table_mes->表单信息{
         *        id
         *        TabNam->表单名
         * }
         */
        //0草稿,1提交(签批),2驳回,3逾期,4归集,5撤回归集,6重新提交,9删除
    //草稿文件显示
    public function formShow_Draf()
    {
        $data['typeForm'] = $this->system->TypeM_selectMes(0);
        $this->load->view('form_draf.html',$data);
    }
    //签批文件显示
    public function formShow_Sign()
    {
        $data['typeForm'] = $this->system->TypeM_selectMes(0);
        $this->load->view('form_sign.html',$data);
    }
    //驳回文件显示
    public function formShow_Rejt()
    {
        $data['typeForm'] = $this->system->TypeM_selectMes(0);
        $this->load->view('form_rejt.html',$data);
    }
    //逾期文件显示
    public function formShow_Over()
    {
        $data['typeForm'] = $this->system->TypeM_selectMes(0);
        $this->load->view('form_over.html',$data);
    }
    //归集文件显示
    public function formShow_Pack()
    {
        $data['workg'] = $this->system->WG_selectMes();
        $this->load->view('form_pack.html',$data);
    }
    /*
     * 表单列表显示【不包括草稿部分的信息查询】
     */
    //按状态查询
    public function FormSta()
    {
        $Type = $this->uri->segment(3);
        $ProId = $this->uri->segment(4);
//      $Type = 'sign';
        $data['aaData'] = $this->form->FormSta($Type,$ProId);
        $i=1;
        foreach($data['aaData'] as &$v)
        {
            //查询表单的类型
            $v['typeName'] = $this->form->CheckTypeName($v['TabTyp'],0);
            
            //数量&选择框
            $v['rowNum'] = $i;
            $v['checkBox'] = "<label class='pos-rel'><input type='checkbox' class='ace'/><span class='lbl'></span></label>";
            $i++;
        }
        $json = json_encode($data);
        echo $json;
    }
    //表单信息显示【显示表单详情页面】
    public function FormMesLoad()
    {
        $data['typeForm'] = $this->system->TypeM_selectMes(0);
        
        $this->load->view('form_info.html',$data);
    }
    //获取表单的历史信息
    public function FormgetHis()
    {
        $FormId = $this->uri->segment(3);
//      $FormId = '12d9d360-8e6b-49d1-8884-8320c14f014e';
        
        $data['aaData'] = $this->form->FormgetHis($FormId);
        $json = json_encode($data);
        echo $json;
    }
    //获取表单基本信息和流转属性
    public function FormgetBC()
    {
    //如果选中的是接口中的数据，则应该先将数据保存到表单的数据表中
        //获取参数
        $FormId = $this->uri->segment(3);
//      $FormId = '12d9d360-8e6b-49d1-8884-8320c14f014e';
        //获取信息
        $data = $this->form->FormMesLoad($FormId,'table_mes');
        $json = json_encode($data);
        echo $json;
    }
    //保存表单基本信息的修改
    public function FormMesBaseSave()
    {
//      $formId = $this->uri->segment(3);
        $TableName = $this->uri->segment(3);
        $flag = $this->input->post('flag');
        $FormName = $this->input->post('FormName');
        $FormType = $this->input->post('FormType');
        $DLtime = $this->input->post('DLtime');
        $TabEls = $this->input->post('TabEls');
        $formId = $this->input->post('formId');
        $Fid=array();
        $Fid=explode(',',$formId);
        $num = count($Fid);
        for($i = 0;$i < $num;$i++){
        	$FormId= $Fid[$i];
	//      $formId = '9839bff1-d004-4bd3-b27b-bee994fe4da3';
	//      $TableName = 'table_mes_cache';
	//      $FormName = 'ceshi';
	//      $FormType = '建筑设计文档';
	//      $DLtime = '2018-08-16';
	//      $TabEls = 'ko';
	        if($flag=='1'){
	        	//批量提交不修改名字
	        	$ChangeMes = array('TabEls'=>$TabEls,'TabDTm'=>$DLtime);
	        }
	        else{
	        	$ChangeMes = array( 'TabNam'=>$FormName,'TabEls'=>$TabEls,'TabDTm'=>$DLtime );
	        }
	        //保存信息
	        $data = $this->form->FormMesBaseSave($FormId,$ChangeMes,$FormType,$TableName);
        }
        $json = json_encode($data);
        echo $json;
    }
    
    //保存表单流转属性的修改
    public function FormMesCirSave()
    {
        //
    }
	
	//表单管理的操作记录显示
	public function ShowOperatingData()
	{
		$formId = $this->uri->segment(3);
		$data = $this->form->FormgetHis($formId);
		
		$ret_data = array(
			"state"=>"success",
			"msg"=>"",
			"data" => array()
		);
		
		if(count($data) > 0){
			$ret_data["data"] = $data;
		}else{
			$ret_data["state"] = "failure";
			$ret_data["msg"] = "没有数据";
		}
		
		$json = json_encode($ret_data);
		echo $json;
	}
	public function img_upload()
	{		
		$formId=$_POST['Fid'];
		foreach ($_FILES['file']['name'] as $key => $image) {
			$config['upload_path']='./formUpload/';
	        $config['allowed_types']= 'gif|jpg|png|jpeg|bmp';
//	        $config['max_size']=1000000000;//设置为无限制大小
	//      $config['max_width']= 1024;
	//      $config['max_height'] = 768;
	//防止名字重复
//	        $config['file_name']=time().mt_rand(1000,9999);
	        $this->load->library('upload', $config);
	        $this->upload->initialize($config);
			//set $_FILES value
			$fileKey = "file";
			$fileKeyNew = "file_{$key}";
			$_FILES[$fileKeyNew] = [
			    'name' => $_FILES[$fileKey]['name'][$key],
				'type' => $_FILES[$fileKey]['type'][$key],
				'tmp_name' => $_FILES[$fileKey]['tmp_name'][$key],
				'error' => $_FILES[$fileKey]['error'][$key],
				'size' => $_FILES[$fileKey]['size'][$key],
			];
			if ($this->upload->do_upload($fileKeyNew)) {
			    $uploadData = $this->upload->data();
			    $pathurl=$uploadData['file_name'];
			    $type=$uploadData['file_ext'];
			    $data = $this->form->save_fileurl($formId,$pathurl,$type);
//			    $imageWidth = intval($uploadData['image_width']);
//			    $imageHeight = intval($uploadData['image_height']);
			//do something here
			} 
			else{
			    error($this->upload->display_errors());
			    $data['status']='error';
			}
		}
		$json = json_encode($data);
		echo $json;
	}
	//附件记录的显示
	public function ShowuploadData()
	{
		$formId = $this->uri->segment(3);
		$data = $this->form->Formgetupload($formId);
		$ret_data = array(
			"state"=>"success",
			"msg"=>"",
			"data" => array()
		);
		if(count($data) > 0){
			$ret_data["data"] = $data;
		}else{
			$ret_data["state"] = "failure";
			$ret_data["msg"] = "没有数据";
		}
		$json = json_encode($ret_data);
		echo $json;
	}
	public function Delupload(){
		$mes=$_POST['mes'];
		$sql="DELETE FROM table_img WHERE path='".$mes."'";
		$this->db->query($sql);
    	$data['row'] = $this->db->affected_rows();
    	$data['status'] = 'error';
    	if($data['row']){
    		$path=iconv('utf-8','gbk',$mes);
	    	if(unlink("./formUpload/".$path)){
	    		$data['status'] = 'success';
	    	}
	    }
	    $json = json_encode($data);
		echo $json;
	}
}
