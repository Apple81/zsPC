<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Form_model extends CI_Model{
    /*
     * 页面加载
     */
    //按状态查询表单列表
    public function FormSta($FormType,$ProId)
    {
        /*
         * 根据状态查询表单【1：签批；2：驳回；3：逾期；】
         */
        switch($FormType)
        {
            case 'sign':
                $formSta = 1;
                break;
            case 'rejt':
                $formSta = 2;
                break;
            case 'over':
                $formSta = 3;
                break;
            default:break;
        }
        $sql = "SELECT IntIdA as formId,TabMId as nodeId,ProAId as projectId,TabNam as formName,TabUDa as uploadTime,TabCTm as createTime,imgurl,page,CirSmp,ImpSta,CasSta,TabEls,TabTyp,TabDTm,TabSta,TabMNa FROM table_mes WHERE TabSta = '".$formSta."' and ProAId = '".$ProId."'";
        $data = $this->db->query($sql)->result_array();
        return $data;
    }
    //按表单类型id查询表单类型名称
    public function CheckTypeName($TypeId,$type)
    {
        $result = $this->db->query("select TypNam FROM type_mes where TypeFT = '".$type."' and id = '".$TypeId."'")->result_array();
        return $result[0]['TypNam'];
    }
    //按模板列表查询
    public function TreeShowSelect($id,$typeSta)
    {
        $sql = "SELECT IntIdA as formId,TabMId as nodeId,ProAId as projectId,TabNam as formName,TabUDa as uploadTime,TabCTm as createTime,imgurl,page,CirSmp,ImpSta,CasSta,TabEls,TabTyp,TabDTm,TabSta,TabMNa FROM table_mes WHERE TabMId = '".$id."'  AND TabSta = '".$typeSta."' ";
        $data = $this->db->query($sql)->result_array();
        return $data;
    }
    
	//表单信息获取
	public function FormMesLoad($FormId,$tab)
	{
//	    $FormId = '61b78c07-7eb1-4d0b-9f89-9088b26212c5';
//	    $tab = 'table_mes_cache';

        /*
	     * 根据表单id，表名称查询表单信息
	     * 根据表单信息中的流转时间戳查询流转信息
	     */
	    
	    //获取基本属性
	    $sql_base = "select IntIdA,TabMId,ProAId,TabNam,TabUDa,TabCTm,imgurl,page,CirSmp,ImpSta,CasSta,TabEls,TabTyp,TabDTm,TabSta from ".$tab." where IntIdA = '".$FormId."'";
        $data['base'] = $this->db->query($sql_base)->result_array();
        
        //获取表单类型信息
        $data['type'] = '';
        if(isset($data['base'][0]['TabTyp']))
        {
            $sql_type = "select TypNam,CirSmp from type_mes where id = '".$data['base'][0]['TabTyp']."'";
            $type = $this->db->query($sql_type)->result_array();
            $data['type'] = $type[0]['TypNam'];
        }

        //获取流转属性
            //流程详情
        $data['cirDetali'] = array();
            //流程总数
        $data['cirNum'] = 0;
        
        //查询流转信息
        if(isset($data['base'][0]['CirSmp']))
        {
            $CirSmp = $data['base'][0]['CirSmp'];
            $sql_SelCir = "select id,DepIdS,SigSta,SigCTm,MesCTm,MesSmp from circle_td where CirSmp = '".$CirSmp."' order by id";
//          echo $sql_SelCir;
            $CircliMes = $this->db->query($sql_SelCir)->result_array();
            $data['cirDetali'] = $CircliMes;
            $i = 0;
//          print_r ($data['cirDetali']);
            foreach($data['cirDetali'] as &$v)
            {
                $sql_cheTypeName = "select RolNam from role where id = '".$v['DepIdS']."'";
                $TyNam = $this->db->query($sql_cheTypeName)->result_array();
                $v['DepNam'] = $TyNam[0]['RolNam'];
                $data['cirNum']++;
            }
        }
        
//      print_r ($data);
	    return $data;
	}
	
	//获取表单的历史信息
	public function FormgetHis($FormId)
	{
	    //获取操作记录
        $sql_his = "select HisNam,HisTim,HisPeo,HisEls,HisSig from table_his where IntIdA = '".$FormId."' order by HisTim ";
	    $data = $this->db->query($sql_his)->result_array();
	    return $data;
	}
	
	/*
	 * 信息修改
	 */
	//表单基本信息
	public function FormMesBaseSave($formId,$ChangeMes,$FormType,$TableName)
	{
	    /*
	     * $formId=>表单id
	     * $ChangeMes=>表单信息{表单名称，表单其他，表单截止时间}
	     * $FormType=>表单类型
	     * $TableName=>操作的数据表名
	     * 
	     * 根据类型名称，查询：类型的流转信息
	     * 查询此表单是否有流转信息【如果已经有了流转信息则删除旧的流转信息，插入新的流转信息；如果没有则直接插入信息流转信息】
	     * 保存表单除流转信息外的其他信息
	     */
	    
	    $sql = "select id,CirSmp from type_mes where TypNam = '".$FormType."' and TypeFT = 0";
	    //获取类型id和类型的流转信息
	    $formTypeId = $this->db->query($sql)->result_array();
//	    echo $FormType;
//	    print_r($formTypeId);
//	    echo "<br/>";
//	    echo $formTypeId[0]['id'];
	    $ChangeMes['TabTyp'] = $formTypeId[0]['id'];
	    $CirMesOld = $formTypeId[0]['CirSmp'];
	    
        //查找旧流转信息
        $sql_base = "select IntIdA,TabMId,ProAId,TabNam,TabUDa,TabCTm,imgurl,page,CirSmp,ImpSta,CasSta,TabEls,TabTyp,TabDTm,TabSta from ".$TableName." where IntIdA = '".$formId."'";
        $TabMes = $this->db->query($sql_base)->result_array();
        //如果是提交之前可以修改表单属性
        if($TableName == 'table_mes_cache')
        {
            //删除旧流转信息
            if(isset($TabMes[0]['CirSmp']))
            {
                $sql_DelMesCir = "delete from circle_true where CirSmp = '".$TabMes[0]['CirSmp']."'";
                $this->db->query($sql_DelMesCir);
                $sql_DelMesCirTD = "delete from circle_td where CirSmp = '".$TabMes[0]['CirSmp']."'";
                $this->db->query($sql_DelMesCirTD);
            }
            //转存表单属性
                //获取表单的对应类型的流转信息
            $sql_CheMesCir = "select CirOne,CirTwo,CirThr,CirFor,CirFiv,CirSix,CirSen,CirEig,CirNin,CirTen,CirNum from circle_default where CirSmp = '".$CirMesOld."'";
            $cirMes = $this->db->query($sql_CheMesCir)->result_array();
                //获取当前毫秒级时间戳
                $CirSmpNew = getMillisecond();
                $date = date('Y-m-d');
                
                //插入表单对应的流转信息
                $sql_MesCirNew = "insert into circle_true(CirSmp,CirCTm,CirCpe) ";
                $sql_MesCirNew.= "values('".$CirSmpNew."','".$date."','".$_SESSION['UsePeo']."')";
                $this->db->query($sql_MesCirNew);
                //保存子节点信息
                if(isset($cirMes[0]))
                {
                    $CirKey = array_keys($cirMes[0]);
                    for($i=0;$i<$cirMes[0]['CirNum'];$i++)
                    {
                        //如果这个键有对应的值
                        if($cirMes[0][$CirKey[$i]])
                        {
                            //查询流转子节点信息
                            $sql_cirDet = "select DepIdS from circle_detailm where id = '".$cirMes[0][$CirKey[$i]]."' ";
                            $CircliMes = $this->db->query($sql_cirDet)->result_array();
                            //将信息插入新表
//                          $sql_CirDetNew = "insert into circle_td(DepIdS,MesCTm,MesOrd,MesSmp,CirSmp)";
                            $sql_CirDetNew = "insert into circle_td(DepIdS,MesCTm,MesSmp,CirSmp)";
//                          $sql_CirDetNew .= "values('".$CircliMes[0]['DepIdS']."','".$date."',1,'".$CirSmpNew.$i."','".$CirSmpNew."')";
                            $sql_CirDetNew .= "values('".$CircliMes[0]['DepIdS']."','".$date."','".$CirSmpNew.$i."','".$CirSmpNew."')";
                            $this->db->query($sql_CirDetNew);
                            
                        }
                    }
                }
            $ChangeMes['CirSmp'] = $CirSmpNew;
        }
        
        //保存表单信息
        $where = "IntIdA = '".$formId."'";
        $sql_SaveFormMes = $this->db->update_string($TableName, $ChangeMes, $where);
        $this->db->query($sql_SaveFormMes);
        
        $data['row'] = $this->db->affected_rows();
	    $data['status'] = 'success';
	    return $data;
	}
	//表单流转属性
	public function FormMesCirSave()
    {
        //
    }
    
}
