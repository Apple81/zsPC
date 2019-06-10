<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Affair extends CI_Controller{
    /*
     * 公用函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Affair_model','affair');
    }
    //我的工程显示
    public function affairShow_Proj()
    {
        $this->load->view('affair_proj.html');
    }
    //待办事项显示
    public function affairShow_Want()
    {
        $this->load->view('affair_want.html');
    }
    //重要文件显示
    public function affairShow_Impt()
    {
        $this->load->view('affair_impt.html');
    }
    //紧急文件显示
    public function affairShow_Cras()
    {
        $this->load->view('affair_cras.html');
    }
    //归档文件显示
    public function affairShow_Pack()
    {
        $this->load->view('affair_pack.html');
    }
    //签名记录显示
    public function affairShow_sign()
    {
        $this->load->view('affair_sign.html');
    }
    
    /*
     * 信息查询
     */
    //信息查询[0/待办事项,1/重要文件,2/紧急文件]
        //表单查询
    public function affair_MesShow(){
        $mesType = $this->uri->segment(3);
        $projectId = $this->uri->segment(4);
        //查询待办、重要或者紧急
        $data = $this->affair->affairShow_table($mesType,$projectId);
        $json = json_encode($data);
        echo $json;
    }
    public function affair_Signshow(){
        $useacc = $this->uri->segment(3);
        $ProNam=$this->uri->segment(4);
        $TabMna=$this->uri->segment(5);
        $ProNam=urldecode($ProNam);
        $TabMna=urldecode($TabMna);
        //查询签名记录 
        $data = $this->affair->affair_Signshow($useacc,$ProNam,$TabMna);
        $json = json_encode($data);
        echo $json;
    }
     public function affair_Signshow1(){
        $useacc = $this->uri->segment(3);
        $ProNam=$this->uri->segment(4);
        $ProNam=urldecode($ProNam);
        //查询签名记录 
        $data = $this->affair->affair_Signshow1($useacc,$ProNam);
        $json = json_encode($data);
        echo $json;
    }
    
    //获取选中的文件的信息
    public function MesSel()
    {
        $id = $this->uri->segment(3);
        $type = $this->uri->segment(4);
        echo $id.'/'.$type;
    }
    /*
     * 我的工程
     */
    //关于权限-》查询
    public function accountPach()
    {
//      $projectId = $this->uri->segment(3);
        $projectId = 1;
        $this->load->model('MesContro_model','mescon');
        $data = $this->mescon->getProAccMes($projectId);
        
        $json = json_encode($data);
        echo $json;
    }
    //获取账号手机号码
    public function getMobile()
    {
        echo $_SESSION['UsePho'];
    }
    /*
     * 个人事务
     */
//  public function
    public function update_proname(){
    	$proid=$_POST["proid"];
		$proname=$_POST["proname"];
		$data = $this->affair->update_proname($proid,$proname);
        $json = json_encode($data);
        echo $json;
    }
    //签名记录的显示
    public function getTree(){
    	$name = $this->uri->segment(3);
//  	$name='admin';
    	$sql_id="select id from user where UseAcc='".$name."'";
    	$result=$this->db->query($sql_id)->result_array();
    	$sql="select DISTINCT ProNam from sign_detail where userid='".$result[0]['id']."' and SignPa is not null";
    	$data1=$this->db->query($sql)->result_array();
    	$n=count($data1);
    	for($i=0;$i<$n;$i++){
    		$sql_1="select DISTINCT TabMNa from sign_detail where ProNam='".$data1[$i]['ProNam']."'and userid='".$result[0]['id']."'and SignPa is not null";
    	    $data2=$this->db->query($sql_1)->result_array();
    	    $key = $data1[$i]['ProNam'];
    	    	$tree_data[$i]=array(
    	    		"type"=>'folder',
		    		"text"=>$data1[$i]['ProNam'],
		    		"flag"=>'1'
    	    	);
    	    for($j=0;$j<count($data2);$j++){
    	    	$sql_2="select TabNam,IntIdA from sign_detail where ProNam='".$data1[$i]['ProNam']."'and TabMNa='".$data2[$j]['TabMNa']."' and userid='".$result[0]['id']."'and SignPa is not null";
    	    	$data3=$this->db->query($sql_2)->result_array();
    	    		$tree_data[$i]["additionalParameters"]["children"][$j]= $tree_data1=array(
    	    			"text"=>$data2[$j]['TabMNa'],
						"type"=>'item',
						"flag"=>'2',
						"proname"=>$data1[$i]['ProNam']
    	    		);
//  	    	for($k=0;$k<count($data3);$k++){
//  	    		$tree_data[$i]["additionalParameters"]["children"][$j]["additionalParameters"]["children"][$k]=$tree_data2=array(
//  	    			"text"=>$data3[$k]['TabNam'],
//						"type"=>'item',
//						"id"=>$data3[$k]['IntIdA']
//  	    	);
//	    	}
	    }
	}
    $json = json_encode($tree_data);
    echo $json;
	}
}
