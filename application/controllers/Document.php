<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Document extends CI_Controller{
    /*
     * 公用函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Document_model','doc');
    }
    /*
     * 显示页面
     * 
     */
        /*
         * API:
         * FileType->文档类型{
         *      TypNam->类型名
         * }
         * 
         */
    //草稿文件显示
    public function docShow_Draf()
    {
        $data['FileType'] = $this->doc->File_SelType();
        $this->load->view('doc_draf.html',$data);
    }
    //签批文件显示
    public function docShow_Sign()
    {
        $this->load->view('doc_sign.html');
    }
    //废止文件显示
    public function docShow_Rejt()
    {
        $this->load->view('doc_rejt.html');
    }
    //逾期文件显示
    public function docShow_Over()
    {
        $this->load->view('doc_over.html');
    }
    //归集文件显示
    public function docShow_Pack()
    {
        $this->load->view('doc_pack.html');
    }
    /*
     * 功能实现
     */
    //新建文档
    public function Doc_AddNew()
    {
         $FleNam=$this->input->post('FleNam');
         $FleCDa=date('Y-m-d H:i:s');
        $data=array(            
            'FleNam' => $FleNam,
            'FleCDa' =>  $FleCDa,
            'FleCPe' => $_SESSION['UsePeo'],
            'FleSta'=> 0,
            'Theme' => $this->input->post('Theme'),
            'SelType'=>$this->input->post('SelType'),
            'TargetObj'=>$this->input->post('TargetObj'),
            'CopyObj'=>$this->input->post('CopyObj'),
            'SendPart'=>$this->input->post('SendPart'),
            'SendPeo'=>$this->input->post('SendPeo'),
            'CheckPart'=>$this->input->post('CheckPart'),
            'CheckPeo'=>$this->input->post('CheckPeo'),
            'PassPart'=>$this->input->post('PassPart'),
            'PassPeo'=>$this->input->post('PassPeo'),
            'ObjElse'=>$this->input->post('ObjElse')
            );
        $this->doc->File_AddNew($data);
        $data = array(
            'HisEls'=>$FleNam,
            'HisNam'=>$HisNam='文档创建',
            'HisTme'=>$FleCDa,
            'HisPeo'=>$_SESSION['UsePeo'],
            'HisSig'=>$HisSig='0',
        );
        $this->doc->CreatHis($data);
        success('Document/docShow_Draf','添加成功');
    }
    
    /*
     * 文档列表显示
     */
    //按模板查询
    public function DocList()
    {
        $MId = $this->uri->segment(3);
        $Type = $this->uri->segment(4);
        $MesSta = 4;
        if($Type == 'draft'){
            $MesSta = 0;
        }
        $data['aaData'] = $this->doc->TreeShowSelect_doc($MId,$MesSta);
        $i=1;
        foreach($data['aaData'] as &$v)
        {
            $v['rowNum'] = $i;
            $v['checkBox'] = "<label class='pos-rel'><input type='checkbox' class='ace'/><span class='lbl'></span></label>";
            $i++;
        }
        $json = json_encode($data);
        echo $json;
//      echo $data['formMes'];
    }
    //按状态查询
    public function DocSta()
    {
        $Type = $this->uri->segment(4);
        switch($Type)
        {
            case 'sign':$MesSta = 1;break;
            case 'rejt':$MesSta = 2;break;
            case 'over':$MesSta = 3;break;
            default:break;
        }
        $data['aaData'] = $this->form->docDraf_Sel($MesSta);
        $i=1;
        foreach($data['aaData'] as &$v)
        {
            $v['rowNum'] = $i;
            $v['checkBox'] = "<label class='pos-rel'><input type='checkbox' class='ace'/><span class='lbl'></span></label>";
            $i++;
        }
        $json = json_encode($data);
        echo $json;
//      echo $data['formMes'];
    }
    //操作记录按模板查询
    public function HisList()
    {
        $data['aaData'] = $this->doc->TreeShowSelect_his();
        $i=1;
        foreach($data['aaData'] as &$v)
        {
            $v['rowNum'] = $i;
            $v['checkBox'] = "<label class='pos-rel'><input type='checkbox' class='ace'/><span class='lbl'></span></label>";
            $i++;
        }
        $json = json_encode($data);
        echo $json;
//      echo $data['formMes'];
    }
    //修改基本属性
    public function DocEdit()
    {
        $FleNam=$this->input->post('FleNam');
        $FleCDa=date('Y-m-d H:i:s');
        $Type = $this->input->post('Type');
//      echo $Type;die;
        $TypeId = $this->input->post('TypeId');
        $data=array(
            'Theme' => $this->input->post('Theme'),
//          'SelType'=>$this->input->post('SelType'),
            'TargetObj'=>$this->input->post('TargetObj'),
            'CopyObj'=>$this->input->post('CopyObj'),
            'SendPart'=>$this->input->post('SendPart'),
            'SendPeo'=>$this->input->post('SendPeo'),
            'CheckPart'=>$this->input->post('CheckPart'),
            'CheckPeo'=>$this->input->post('CheckPeo'),
            'PassPart'=>$this->input->post('PassPart'),
            'PassPeo'=>$this->input->post('PassPeo'),
            'ObjElse'=>$this->input->post('ObjElse')
        );
        if($Type == 'docShow_Draf') {
            $this->doc->update_doc('file_mes',$data,$TypeId);
            $data =array(
                'HisEls'=>$FleNam,
                'HisNam'=>$HisNam='文档属性修改',
                'HisTme'=>$FleCDa,
                'HisPeo'=>$_SESSION['UsePeo'],
                'HisSig'=>$HisSig='0',      
            );
            $this->doc->CreatHis($data);
            success('Document/'.$Type,'修改成功');
        }
    }
    
}
