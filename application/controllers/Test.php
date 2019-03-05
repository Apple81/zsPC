<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Document_model','doc');
    }
    //测试专用
    public function show()
    {
//      $img = 'http://112.74.34.150:8080/TongXinweb/images/3378e3cd-1134-4927-8438-09074ff65df3/39da602f-0303-450a-ae33-cb61fe867a65/(1).png';
//      echo filesize($img);
        
        $uri = "http://112.74.34.150:8080/TongXinweb/images/3378e3cd-1134-4927-8438-09074ff65df3/39da602f-0303-450a-ae33-cb61fe867a65/(1).png";
//      echo remote_filesize($url,$user='',$pw='');
        $user='';
        $pw='';
//      function remote_filesize($uri,$user='',$pw='')
//      {
        // start output buffering
            ob_start();
        // initialize curl with given uri
            $ch = curl_init($uri); // make sure we get the header
            curl_setopt($ch, CURLOPT_HEADER, 1); // make it a http HEAD request
            curl_setopt($ch, CURLOPT_NOBODY, 1); // if auth is needed, do it here
            if (!empty($user) && !empty($pw))
            {
                $headers = array('Authorization: Basic ' . base64_encode($user.':'.$pw));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }
            $okay = curl_exec($ch);
            curl_close($ch); // get the output buffer
            $head = ob_get_contents(); // clean the output buffer and return to previous // buffer settings
            ob_end_clean();  // gets you the numeric value from the Content-Length // field in the http header
            $regex = '/Content-Length:\s([0-9].+?)\s/';
            $count = preg_match($regex, $head, $matches);  // if there was a Content-Length field, its value // will now be in $matches[1]
            if (isset($matches[1]))
            {
                $size = $matches[1];
            }
            else
            {
                $size = 'unknown';
            }
            $last_mb = round($size/(1024*1024),3);
            $last_kb = round($size/1024,3);
//          return $last_kb . 'KB / ' . $last_mb.' MB';
//          echo $last_kb . 'KB / ' . $last_mb.' MB';
            echo $size;
//      }
        
        
        
        
    }
    public function GetTreeNode()
    {
        $projectId = $this->uri->segment(3);
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
}
