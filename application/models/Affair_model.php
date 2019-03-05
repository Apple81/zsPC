<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Affair_model extends CI_Model{
    //信息查询[待办、重要或者紧急]
    public function affairShow_table($type,$projectId)
    {
//      $sql = "SELECT IntIdA as formId,TabMId as nodeId,ProAId as projectId,TabNam as formName,TabUDa as uploadTime,TabCTm as createTime,imgurl,page,CirSmp,ImpSta,CasSta,TabEls,TabTyp,TabDTm FROM table_mes WHERE ProAId = '".$PId."' AND TabSta = '1' ";
        $sql = "SELECT IntIdA as formId,TabMId as nodeId,ProAId as projectId,TabNam as formName,TabUDa as uploadTime,TabCTm as createTime,imgurl,page,CirSmp,ImpSta,CasSta,TabEls,TabTyp,TabDTm,TabSta,TabMNa FROM table_mes WHERE TabSta = '1' and ProAId = '".$projectId."' ";
        switch ( $type ) {
            case 1:
                $sql .= " and ImpSta = 1";
                break;
            case 2:
                $sql .= " and CasSta = 1";
                break;
            default:break;
        }
        $data['aaData'] = $this->db->query($sql)->result_array();
        //将文档的信息插入$data['aaData']
        
        return $data;
    }
    //信息查询[已经归档的文件]
    public function affairShow_packDoc(){
        $sql = "select id,FleNam as name from file_mes where FleSta=4 ";
        $data = $this->db->query($sql)->result_array();
        return $data;
    }
    //获取被选中的对应信息
    public function MesSel()
    {
        
    }
    
}