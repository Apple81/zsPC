<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Login extends CI_Controller
{
    /*
     * 公用函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('login_model','login');
    }
    /*
     * 显示登录页面
     * 退出登录时清除session
     */
    public function index()
    {
        if(isset($_SESSION['UserAcc'])) {
            unset($_SESSION['UserAcc']);
        }
        $this->load->helper('form');
        $this->load->view('login.html');
    }
    /*
     * 账号验证和登录系统
     */
    public function loginSys()
    {
        //载入验证类
        $this->load->library('form_validation');
        //设置规则
        $this->form_validation->set_rules('account','用户账号','required|min_length[5]');
        $this->form_validation->set_rules('password','密码','required|min_length[5]');
        //执行验证
        $status = $this->form_validation->run();
        if($status)
        {
            $account = $this->input->post('account');
            $UseKey = $this->input->post('password');
            $UseNam = $this->login->checkAccount($account);
            if(!$UseNam || $UseNam[0]['UseKey']!=$UseKey)
            {
                error('login/index',"账号不存在或密码错误！");
            }
            else
            {
                //成功登录，查【账号名,手机,id,账号所属人名】保存于session
                $data = $this->login->GetAccountMes($account);
                $this->session->set_userdata('UserAId', $data[0]['id']);//账号id
                $this->session->set_userdata('UsePho', $data[0]['UsePho']);//用户手机
                $this->session->set_userdata('UsePeo', $data[0]['UsePeo']);//用户
                $this->session->set_userdata('UserAcc', $account);//账号
                //记录登录记录
                $data_LoginHis = array(
                    'LogTim' => date('Y-m-d H:i:s'),
                    'LogPeo' => $data[0]['UsePeo'],
                    'LogAId' => $data[0]['id'],
                    'LogSta' => 0,
                );
                $this->login->hisMark($data_LoginHis);
                //进入首页
                $this->load->view('affair_proj.html');
            }
        }else
        {
            $this->load->helper('form');
            $this->load->view('login.html');
        }
    }

    /*
     * 注册动作
     */
    public function register()
    {
        //载入验证类 
        $this->load->library('form_validation');
        //设置规则
        $this->form_validation->set_message('matches', '两次密码不相同');      
        $this->form_validation->set_rules('UsePho','手机号码','required|exact_length[11]|is_unique[user.UsePho]',array('is_unique' => '此手机号码已被注册'));
        $this->form_validation->set_rules('UsePeo','真实姓名','required|is_unique[user.UsePeo]',array('is_unique' => '系统已存在此用户的账号信息，请确认后注册'));
        $this->form_validation->set_rules('UseAcc','账号','required|min_length[6]|max_length[11]|is_unique[user.UseAcc]',array('is_unique' => '此账号已被注册'));
        $this->form_validation->set_rules('UseKey','密码','required|min_length[6]|max_length[11]');
        $this->form_validation->set_rules('surpasswod','确认密码','required|min_length[6]|max_length[11]|matches[UseKey]');
        
        //执行验证
        $status = $this->form_validation->run();
        if($status)
        {
            $data = array
            (
                'UseAcc' => $this->input->post('UseAcc'),
                'UsePho' => $this->input->post('UsePho'),
                'UseKey' => $this->input->post('UseKey'),
                'UseSta'=> 0,
                'UsePeo' => $this->input->post('UsePeo'),
                'UseTim' => date('Y-m-d H:i:s')
            );
            $this->login->register($data);
            echo "<script> sessionStorage.setItem('loginTag','login-box'); </script>";
            success('Login/index','注册成功');
        }
        else
        {           
            wrong('注册失败');
            $this->load->helper('form');
            $this->load->view('login.html');
            
        }
    }
     /*
      * 重置密码动作
      */
    public function reset()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_message('matches', '两次密码不相同');
        $this->form_validation->set_rules('phone','手机号码','required');
        $this->form_validation->set_rules('NewUseKey','新密码','required|min_length[6]|max_length[11]');
        $this->form_validation->set_rules('newpasswod','确认新密码','required|min_length[6]|max_length[11]|matches[NewUseKey]');
        $status=$this->form_validation->run();
        if($status)
        {                               
            $phone = $this->input->post('phone');   
            $UsePho=$this->login->checkphone($phone);   
            if(!$UsePho)
            {
                wrong('手机号码不存在');
                $this->load->helper('form');
                $this->load->view('login.html');
            }
            else
            {
                
                $UsePho=$this->input->post('phone');
                $UseKey=$this->input->post('NewUseKey');
                $data=array
                (
                    'UseKey'=>$UseKey
                );
                $data['user']=$this->login->rset($UsePho,$data);
                success('login/index', '修改成功');
            }
        }
        else
        {
            wrong('修改失败');
            $this->load->helper('form');
            $this->load->view('login.html');
        }       
    }
}