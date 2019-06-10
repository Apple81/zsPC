<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class MesContro_model extends CI_Model{
    /*
     * 关于树节点【归集树】
     */
    public function GetTreeNodePack($projectId)
    {
        $sql = "select * from tree_sta where projectId = '".$projectId."'";
        $data['data'] = $this->db->query($sql)->result_array();
        return $data;
    }
    /*
     * 信息状态管理
     */
    //检测是不是有表单类型
    public function FromTypeCheck($fromId,$TabName)
    {
    	
    	$sql = "select id,TabTyp,TabDTm from ".$TabName." where IntIdA ='".$fromId."'";
//      $sql = "select id,TabTyp,TabDTm from ".$TabName." where IntIdA ='".$fromId."'";
        $FromMes = $this->db->query($sql)->result_array();
        if($FromMes[0]['TabTyp'] && $FromMes[0]['TabDTm'])
        {
            return 'allow';
        }else{
            return 'none';
        }
    }
    //更改状态【0草稿,1签批,2驳回,3逾期,4归集】
    public function StaChange($MesIdArr,$Type,$PageType,$urlDel,$urlTree,$proName)//$url用于删除接口中的数据
    {
        /*
         * 判断是什么类型操作【提交，驳回，归集，重新提交，撤回归集】
         * 判断表格是不是在缓存表中，如果不在的换则将表单信息及其树节点信息从接口保存转存到系统数据库【1.保存基本信息；2.保存树节点信息，获取模板信息；3.保存表单模板id和名称】
         * 删除接口信息【如果】
         * 
         * */
        $FormN = 'table_mes';
        switch ($Type)
        {
            case 'UpLoad':$CT = 1;$actionType = '提交';break;
            case 'Reject':$CT = 2;$actionType = '驳回';break;
            case 'PackUp':$CT = 4;$actionType = '归集';break;
            case 'CallBk':$CT = 5;$actionType = '撤回归集';break;
            case 'ReUpld':$CT = 1;$actionType = '重新提交';break;
            default:break;
        }
        
        //如果是初次提交则需要将信息从接口数据库中转移到本系统的数据库
        if($PageType == 'draf')
        {
            foreach($MesIdArr as $v)
            {
                //将数据复制到系统的表单信息表中'table_mes'
                    //检测系统中有没有相同id的表单
                $sqlCheIdCache = "select id,TabNam from table_mes_cache where IntIdA = '".$v."'";
                $formInStaCache = $this->db->query($sqlCheIdCache)->num_rows();
                $formMesCache = $this->db->query($sqlCheIdCache)->result_array();
                $sqlCheId = "select id from table_mes where IntIdA = '".$v."'";
                $formInSta = $this->db->query($sqlCheId)->num_rows();
                //如果表单已经存在，则删除原表单
                if($formInSta > 0 && $formInStaCache > 0)
                {
                    $sql_delOld = "delete from table_mes where IntIdA = '".$v."'";
                    $this->db->query($sql_delOld);
                }
                $sql_copy = "insert into table_mes (IntIdA,TabMId,ProAId,TabNam,TabUDa,TabCTm,imgurl,page,CirSmp,ImpSta,CasSta,TabEls,TabTyp,TabDTm,TabSta,fileUrl) ";
                $sql_copy .= "select IntIdA,TabMId,ProAId,TabNam,TabUDa,TabCTm,imgurl,page,CirSmp,ImpSta,CasSta,TabEls,TabTyp,TabDTm,TabSta,fileUrl from table_mes_cache where IntIdA = '".$v."' ";
                $this->db->query($sql_copy);
                $rowCopy = $this->db->affected_rows();
                $tabMesNewId = $this->db->insert_id();
                //转存节点信息
                    //获取表单信息
                    
                    $url = $urlTree.$v;
                    $ch = curl_init ();
                    curl_setopt ( $ch, CURLOPT_URL, $url );
                    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
                    curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
                    curl_setopt ( $ch, CURLOPT_POST, 1 ); //启用POST提交
                    $file_contents = curl_exec ( $ch );
                        //json解码
                        $TreeMes = json_decode($file_contents,true);
                        //计算数组长度
                        $NumTreeNode = count($TreeMes['data']);
                        //保存到系统数据库
                        $NumTreeNodeAll = 0;
                        for($i = 0;$i < $NumTreeNode;$i++)
                        {
                            $nodeId = $TreeMes['data'][$i]['nodeId'];
                            $nodeName = $TreeMes['data'][$i]['nodeName'];
                            $parentId = $TreeMes['data'][$i]['parentId'];
                            $formId = $TreeMes['data'][$i]['formId'];
                            $projectId = $TreeMes['data'][$i]['projectId'];
                            $id = $TreeMes['data'][$i]['id'];
//                          print_r($TreeMes['data'][0]);
                            //保存表单修改属性后的名字
                            if($nodeId == $v )
                            {
                                $nodeName = $formMesCache[0]['TabNam'];
                                //获取模板ID
                                $ModleId = $TreeMes['data'][$i]['parentId'];
                            }
                            $sql_SaveNode = "insert into tree (nodeId,nodeName,parentId,formId,id,projectId) values('".$nodeId."','".$nodeName."','".$parentId."','".$formId."','".$id."','".$projectId."')";
                            $this->db->query($sql_SaveNode);
                            $rowTreeNode = $this->db->affected_rows();
                            //计算成功的操作条数
                            if(isset($rowTreeNode)){
                                $NumTreeNodeAll += $rowTreeNode;
                            }
                        }
                    curl_close ( $ch );
                    
                //保存表单的模板名和模板id
                    //查找模板名称
                    $sql_CheMName = "select nodeName from tree where nodeId = '".$ModleId."'";
                    $result_CheMName = $this->db->query($sql_CheMName)->result_array();
//                  echo $sql_CheMName;
//                  print_r($result_CheMName) ;
                    //更新表单数据信息
                    $sql_SaveMName = "update table_mes set TabMId = '".$ModleId."',TabMNa = '".$result_CheMName[0]['nodeName']."',proNam = '".$proName."' where IntIdA = '".$v."' ";
                    $this->db->query($sql_SaveMName);
                //TODO:记得去掉注释
                //如果成功转存数据，则删除数据
                if($rowCopy > 0)
                {
//                    删除缓存库的数据
//                  $sql_del = "delete from table_mes_cache where IntIdA = '".$v."'";
//                  $this->db->query($sql_del);
////                    删除接口的数据
//                  $url = $urlDel.$v;
//                  $ch = curl_init ();
//                  curl_setopt ( $ch, CURLOPT_URL, $url );
//                  curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
//                  curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
//                  curl_setopt ( $ch, CURLOPT_POST, 1 ); //启用POST提交
//                  $file_contents = curl_exec ( $ch );
////                  echo $file_contents;
//                  curl_close ( $ch );
                }
                

                //如果是重新提交需要删除表单现有的签名
        //      if($CT == 2)
        //      {
        //          //查找现有的表单的流转时间戳
        //          
        //          //清空有此时间戳的circle_td
        //          
        //          //清空有此时间戳的签名
        //          
        //      }
                
                //执行状态修改操作
                $sql = "UPDATE ".$FormN." SET TabSta = CASE IntIdA ";
                $sql .= sprintf("WHEN %d THEN %d ", $v, $CT); // 拼接SQL语句
                $sql .= "END WHERE IntIdA IN ('".$v."')";
                $this->db->query($sql);
//              $data['rows']="1";
                $data['rows'] = $this->db->affected_rows();

                //保存操作记录
                $sql_his = "INSERT INTO table_his(HisNam,HisTim,HisPeo,HisSig,TabIdS,IntIdA) VALUE('".$actionType."','".date('Y-m-d H:i:s')."','".$_SESSION['UsePeo']."',0,'".$tabMesNewId."','".$v."') ";
                // $data['sqlTest'] = $sql_his;
                $this->db->query($sql_his);
               
            }
        }
        //手动归集
        if($PageType == 'sign'){
        	//执行状态修改操作
        	foreach($MesIdArr as $v)
            {
            	$sql_mes="select id from table_mes where IntIdA='".$v."'";
            	$Mes=$this->db->query($sql_mes)->result_array();
                $sql = "UPDATE ".$FormN." SET TabSta = CASE IntIdA ";
                $sql .= sprintf("WHEN %d THEN %d ", $v, $CT); // 拼接SQL语句
                $sql .= "END WHERE IntIdA IN ('".$v."')";
                $this->db->query($sql);
//              $data['rows']="1";
                $data['rows'] = $this->db->affected_rows();

                //保存操作记录
                $sql_his = "INSERT INTO table_his(HisNam,HisTim,HisPeo,HisSig,TabIdS,IntIdA) VALUE('".$actionType."','".date('Y-m-d H:i:s')."','".$_SESSION['UsePeo']."',0,'".$Mes[0]['id']."','".$v."') ";
                // $data['sqlTest'] = $sql_his;
                $this->db->query($sql_his);
            }
        }
        return $data;
    }
    //删除表单
    public function StaChangeDel($MesIdArr,$url)
    {
        foreach($MesIdArr as $v)
        {
            /*
             * 查询是缓存表还是系统表的数据，删除系统数据库信息
             * 删除接口数据信息
             */
            //TODO:记得去掉注释
                //删除缓存表
//          $sqlCheIdCache = "select id from table_mes_cache where IntIdA = '".$v."'";
//          $formInStaCache = $this->db->query($sqlCheIdCache)->num_rows();
//          if($formInStaCache > 0)
//          {
//              $sql_delCha = "delete from table_mes_cache where IntIdA = '".$v."'";
//              $this->db->query($sql_delCha);
//          }
//              //删除系统表
//          $sqlCheId = "select id from table_mes where IntIdA = '".$v."'";
//          $formInSta = $this->db->query($sqlCheId)->num_rows();
//          if($formInSta > 0)
//          {
//              $sql_delOld = "delete from table_mes where IntIdA = '".$v."'";
//              $this->db->query($sql_delOld);
//          }
        }
    }
    
    //检测节点中的表单在不在缓存表中
    public function ShowFormMes($formId)
    {
        $sql = "select IntIdA,TabMId,ProAId,TabNam,TabUDa,TabCTm,imgurl,page,CirSmp,ImpSta,CasSta,TabEls,TabTyp,TabDTm,TabSta from table_mes_cache where IntIdA = '".$formId."'";
        $row = $this->db->query($sql)->num_rows();
        return $row;
    }
    //将接口中的数据保存到缓存表中
    public function SaveFormMes($Mes)
    {
        $sql = "insert into table_mes_cache (IntIdA,TabMId,ProAId,TabNam,TabUDa,TabCTm,imgurl,page) ";
        $sql .= "values('".$Mes['formId']."','".$Mes['nodeId']."','".$Mes['projectId']."','".$Mes['formName']."','".$Mes['uploadTime']."','".$Mes['createTime']."','".$Mes['imgurl']."','".$Mes['page']."')";
        
        $this->db->query($sql);
    }
    /*
     * 流程管理
     */
    //新建流转信息
    public function CirSave_New($MId,$MData)
    {
        $dataList = array('CirOne','CirTwo','CirThr','CirFor','CirFiv','CirSix','CirSen','CirEig','CirNin','CirTen',);
        $UseCId = $_SESSION['UserAId'];
        $UseCTm = date('Y-m-d H:i:s');
        list($t1, $t2) = explode(' ', microtime());
        $CirSmp = (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
        $i=1;
        //判断此类型有没有流程信息【判断是否重复新建】
        $queryChe = $this->db->query("select CirSmp from type_mes where id = '".$MId."'");
        $rowChe = $queryChe->row();
            //如果是重复新建
        if(isset($rowChe))
        {
            $CirSmpOld = $rowChe->CirSmp;
            //查询流程信息的详细信息
            $queryDetail = $this->db->query("select CirOne,CirTwo,CirThr,CirFor,CirFiv,CirSix,CirSen,CirEig,CirNin,CirTen from circle_default where CirSmp = '".$CirSmpOld."' ");
            $rowDetail = $queryDetail->row_array();
            //删除流程详情
            for($i=0;$i<count($rowDetail);$i++){
                $sqlDelDetail = "delete from circle_detailm where id = '".$rowDetail[$dataList[$i]]."' ";
                $this->db->query($sqlDelDetail);
            }
            //若有流程信息则将其删除
            $sqlDelMes = "delete from circle_default where CirSmp = '".$CirSmpOld."'";
            $this->db->query($sqlDelMes);
        }
        //创建流程信息【或重新新建】
        $this->db->query("insert into circle_default set CirSmp = ".$CirSmp.",CirCPe = '".$UseCId."',CirCTm = '".$UseCTm."' ");
        
        for($i=0;$i<count($MData);$i++)
        {
            //获取部门id
            $query = $this->db->query("select id from role where RolNam = '".$MData[$i]."' and RolSta = 0");
            $row = $query->row_array();
            //创建流程信息
            $sql = "insert into circle_detailm set DepIdS = '".$row['id']."',UseCId = '".$UseCId."',UseCTm = '".$UseCTm."',CirSmp = '".$CirSmp."' ";
            $this->db->query($sql);
            $NewMesId = $this->db->insert_id();
            //绑定流程信息与流程详情
            $sqlTie = "update circle_default set ".$dataList[$i]." = '".$NewMesId."', CirNum = '".($i+1)."' where CirSmp='".$CirSmp."' ";
            $this->db->query($sqlTie);
        }
        //绑定流程信息与类型
        $this->db->query("update type_mes set CirSmp='".$CirSmp."' where id = '".$MId."' ");
        $sqlSta = $this->db->affected_rows();
        //释放掉查询结果所占的内存，并删除结果的资源标识
        $query->free_result();
        $queryChe->free_result();
        return $sqlSta;
    }
    //显示流转信息
    public function CirMes_Show($id)
    {
        //定义数组
        $dataList = array('CirOne','CirTwo','CirThr','CirFor','CirFiv','CirSix','CirSen','CirEig','CirNin','CirTen',);
        //获取流转属性时间戳
        $queryChe = $this->db->query("select CirSmp from type_mes where id = '".$id."'");
        $rowChe = $queryChe->row();
//      p($rowChe) ;
        //获取流转属性
        $CirSmp = $rowChe->CirSmp;
        if($CirSmp)
        {
            //查询流程信息的详细信息
            $queryDetail = $this->db->query("select CirOne,CirTwo,CirThr,CirFor,CirFiv,CirSix,CirSen,CirEig,CirNin,CirTen,CirNum from circle_default where CirSmp = '".$CirSmp."'");
            $rowDetail = $queryDetail->row();
            $CirNum = $rowDetail->CirNum;
            //查找流程详情
            for($i=0;$i<$CirNum;$i++){
                if($rowDetail->$dataList[$i])
                {
                    $queryMes = $this->db->query("select RolNam from circle_detailm a,role b where a.DepIdS = b.id and a.id = '".$rowDetail->$dataList[$i]."' ");
                    $rowMus = $queryMes->row_array();
                    $data[] = $rowMus['RolNam'];
                }
            }
            return $data;
        }
        return $data = array('','','','');
    }
    //修改流转信息
    public function CirSave_Change()
    {
        
    }
    /*
     * 打印功能
     * */
    //获取需要打印的表单及其签名信息
    public function PrintOutGetMes($FormIdA,$tabName)
    {
        /*
         * 根据表单id，查询：表单图片信息和表单的页数信息,及其签名后的签名信息id
         * */
        $query = $this->db->query("SELECT ImgUrl,page FROM ".$tabName." WHERE IntIdA = '".$FormIdA."'")->result_array();
        $data['ImgUrl'] = $query[0]['ImgUrl'];
        $data['page'] = $query[0]['page'];
        return $data;
    }
    //获取签名信息
    public function PrintOutGetSign($FormIdA)
    {
        /*
         * 根据表单id，查询：表单图片信息和表单的页数信息,及其签名后的签名信息id
         * 根据签名信息id，查询：签名信息的详细信息并组成返回值返回页面
         * */
        $query = $this->db->query("SELECT ImgUrl,SigId,page FROM circle_detail WHERE IntIdA = '".$FormIdA."'")->result_array();
//      $data['ImgUrl'] = $query[0]['ImgUrl'];
//      $data['page'] = $query[0]['page'];
        $i = 0;
        foreach ($query as $v)
        {
//          echo $v['ImgUrl'];
//          echo '<br />';
            if(isset($v['SigId'])){
                $signMes = $this->db->query("SELECT SignPa,SignPX,SignPY,FormW,FormH,SignW,SignH,PageFinal FROM sign_mes WHERE id = '".$v['SigId']."'")->result_array();
                $data['Sign'][$i]['SignPa'] = $signMes[0]['SignPa'];
                $data['Sign'][$i]['SignPX'] = $signMes[0]['SignPX'];
                $data['Sign'][$i]['SignPY'] = $signMes[0]['SignPY'];
                $data['Sign'][$i]['FormW'] = $signMes[0]['FormW'];
                $data['Sign'][$i]['FormH'] = $signMes[0]['FormH'];
                $data['Sign'][$i]['SignW'] = $signMes[0]['SignW'];
                $data['Sign'][$i]['SignH'] = $signMes[0]['SignH'];
                $data['Sign'][$i]['PageFinal'] = $signMes[0]['PageFinal'];
                $i++;
//              echo $v['SigId'];
//              echo '<br/>';
            }
        }
        $data['Num'] = $i;
        return $data;
    }
    //获得当前工程的归集表单
   	public function GetPackMes($projectId)
    {
        $sql = "select TabMNa,TabMId,COUNT(TabMNa) AS num from table_mes where ProAId = '".$projectId."' AND TabSta='4' group by TabMNa";
        $data= $this->db->query($sql)->result_array();
        return $data;
    }
    public function WgFinish($TabMId,$proid){
    	$sql = "update table_mes set TabSta='5' where TabMId='".$TabMId."' and ProAId='".$proid."' and TabSta='4'";
//      $data= $this->db->query($sql)->num_rows;
        $this->db->query($sql);
        $result = $this->db->affected_rows();
        if($result>0){
    		$data['status']="success";
    		}
    		else{
    		$data['status']="error";}
        return $data;
    }
}