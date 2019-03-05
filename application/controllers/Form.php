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
        $data['typeForm'] = $this->system->TypeM_selectMes(0);
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
        $formId = $this->uri->segment(3);
        $TableName = $this->uri->segment(4);
        $FormName = $this->input->post('FormName');
        $FormType = $this->input->post('FormType');
        $DLtime = $this->input->post('DLtime');
        $TabEls = $this->input->post('TabEls');
        
//      $formId = '9839bff1-d004-4bd3-b27b-bee994fe4da3';
//      $TableName = 'table_mes_cache';
//      $FormName = 'ceshi';
//      $FormType = '建筑设计文档';
//      $DLtime = '2018-08-16';
//      $TabEls = 'ko';
        
        $ChangeMes = array( 'TabNam'=>$FormName,'TabEls'=>$TabEls,'TabDTm'=>$DLtime );
        //保存信息
        $data = $this->form->FormMesBaseSave($formId,$ChangeMes,$FormType,$TableName);
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
    
}
