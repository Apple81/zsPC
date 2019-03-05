<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Document_model extends CI_Model{
    /*
     * 信息查询
     */
    public function File_SelType()
    {
        $data = $this->db->query("SELECT id,TypNam FROM `type_mes` WHERE TypeFT = 1")->result_array();
        return $data;
    }
    
    /*
     * 功能实现
     */
    //新增文档
    public function File_AddNew($data)
    {
        $this->db->insert('file_mes',$data);        
    }
    /*
     * 文档页面加载
     */
    //页面加载时的表格信息查询
    public function docDraf_Sel($CT)
    {
        $data=$this->db->query("SELECT id,FleNam,FleCDa FROM file_mes WHERE FleSta=".$CT."")->result_array();
        return $data;
    }
    //按模板列表查询
    public function TreeShowSelect_doc($id,$typeSta)
    {
        $data = $this->db->query("SELECT id,FleNam,FleCDa,FleCPe,Theme,SelType,TargetObj,CopyObj,SendPart,SendPeo,CheckPart,CheckPeo,PassPart,PassPeo,ObjElse FROM file_mes WHERE  FleSta = ".$typeSta."")->result_array();
        return $data;
    }
    /*
     * 
     */
    //页面加载时的表格信息查询
    public function hisDraf_Sel($CT)
    {
        $data=$this->db->query("SELECT id,HisNam,HisTme,HisPeo,HisEls,HisSig FROM file_his WHERE HisSig=".$CT."")->result_array();
        return $data;
    }
    //按模板列表查询
    public function TreeShowSelect_his()
    {
        $data = $this->db->query("SELECT id,HisNam,HisTme,HisPeo,HisEls,HisSig FROM file_his")->result_array();
        return $data;
    }
    //修改基本属性
    public function update_doc($Type,$data,$id)
    {
        $this->db->update($Type,$data,array('id'=>$id));
    }
    //新增历史操作
    public function CreatHis($data){
        $this->db->insert('file_his',$data);
    }
}