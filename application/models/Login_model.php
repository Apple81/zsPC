<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Login_model extends CI_Model
	{
    	//检测输入的账号是否存在，是否正常
    	//如果信息没有问题则进入系统
    	public function checkAccount($UseAcc)
    	{
        	$data = $this->db->get_where('user',array('UseAcc'=>$UseAcc,'UseSta'=>1))->result_array();
			return $data;
		}
		//向数据库写入账号信息
		public function register($data)
		{
			$this->db->insert('user',$data);
		}
		//重置账号信息
		public function rset($UsePho,$data)
		{
			$this->db->update('user',$data,array('UsePho'=>$UsePho));
		}
		//检索手机号码
		public function checkphone($UsePho)
    	{
        	$data = $this->db->get_where('user',array('UsePho'=>$UsePho))->result_array();
			return $data;
		}
		//记录登录历史
		public function hisMark($data){
              $this->db->insert('user_login',$data);
		}
		//查找账号信息
		public function GetAccountMes($acc) {
		    $data = $this->db->query("SELECT id,UsePho,UsePeo FROM `user` WHERE UseAcc = '".$acc."' and UseSta=1 ")->result_array();
		    return $data;
		}
    }
