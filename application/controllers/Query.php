<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Query extends CI_Controller{
    //查询
    public function queryShow_Seek()
    {
        $this->load->view('query_seek.html');
    }
    //统计分析
    public function queryShow_Anlz()
    {
        $this->load->view('query_anlz.html');
    }
    
}
