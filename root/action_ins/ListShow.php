<?php
    require("../conn.php");
	date_default_timezone_set("PRC"); 
    $falg = $_POST['falg'];
//  $falg = 'listMesType_deta';
    $showtime = date("Y-m-d");
//  echo $showtime;
    
    switch($falg){
        //获取表单类型信息
        case 'listMesType':
            $type = $_POST['type'];
            $projectId = $_POST['projectId'];
            $DepIdS = $_POST['RolId'];
//          $type = 'tab';
//          $projectId = '55db9bb0-6e59-41cf-a548-649eb1f76a7b';
//          $DepIdS = 20;
            
            /*
             * 根据文件类型查询对应的所有类型
             * 根据类型id，项目id，角色id查询表单当前状态视图【sign_check】 ：此项目对应类型对应觉得的应处理信息条数
             */
            //设置文件类型，文档为1，表单为0
            $sign = 1;
            if($type=='tab')
            {
                $sign = 0;
            }
            //获取所有类型
            $sql = "select id,TypNam from type_mes where TypeFT = ".$sign." order by id";
            $result = $conn->query($sql);
            if($result->num_rows>0)
            {
                $i = 0;
                while($row = $result->fetch_assoc())
                {
                    $data['data'][$i]['id'] = $row['id'];
                    $data['data'][$i]['TypNam'] = $row['TypNam'];
                    //判断表单状态
                    $sql_GetMes = "select id,TabNam,TabCTm,TabDTm,CirSmp,SigSta from sign_check where ProAId = '".$projectId."' and TabSta = 1 order by TabDTm";
                    $result_GetMes = $conn->query($sql_GetMes);
                    if($result_GetMes->num_rows>0)
                    {
                        while($row_GetMes = $result_GetMes->fetch_assoc())
                        {
                            //如果当前日期大于时间期限则判断为逾期，改变表单状态【逾期状态为3】
                            if($row_GetMes['TabDTm'] <= $showtime)
                            {
                                $sql_ChangeSta01 = "update table_mes set TabSta = 3 where id = '".$row_GetMes['id']."'";
                                $result_ChangeSta = $conn->query($sql_ChangeSta01);
//                              continue;
                            }
                            //判断表单是不是已经走完签批流程，改变表单状态【归集状态为4】
                            if($row_GetMes['SigSta'] == 5)
                            {
                                $sql_ChangeSta02 = "update table_mes set TabSta = 4 where id = '".$row_GetMes['id']."'";
                                $result_ChangeSta = $conn->query($sql_ChangeSta02);
//                              continue;
                            }
                        }
                    }
                    
//                  $CountForm = "select SignId,id,CirSmp,TabDTm,TabNam,TabCTm,DepIdS from sign_check where cast(TabDTm as datetime) > '$showtime' and TabTyp = ".$row['id']." and ProAId = '".$projectId."' and TabSta = 1 GROUP BY CirSmp order by TabDTm";
////                  $CountForm = "select COUNT(signId) as num from sign_check where  TabTyp = '".$row['id']."' and TabSta = 1 and ProAId = '".$projectId."' and DepIdS = '".$DepIdS."'";
//                  $result_CountForm = $conn->query($CountForm);
//                  $data['data'][$i]['Num'] = 0;
//                  if($result_CountForm -> num_rows > 0){
//                      $Num = 0;
//                      while($row_CountForm = $result_CountForm ->fetch_assoc())
//                      {
//                          if($row_CountForm['DepIdS'] == $DepIdS)
//                          {
//                              $Num++;
//                          }
//                      }
//                      $data['data'][$i]['Num'] = $Num;
//                  }
                    $sql_sel = "select id,CirSmp,TabNam,TabCTm,TabDTm from table_mes where cast(TabDTm as datetime) > '".$showtime."' and ProAId = '".$projectId."' and TabSta = 1 and  TabTyp = ".$row['id']."";
                    $result_sel = $conn->query($sql_sel);
                    $data['data'][$i]['Num'] = 0;
                    
                    if($result_sel->num_rows>0)
                    {
                        $y = 0;
                        while($row_sel = $result_sel->fetch_assoc())
                        {
                            //查询流转信息
                            $CirSmp = $row_sel['CirSmp'];
                            $sql_SelCir = "select id,DepIdS,SigSta,SigCTm,MesCTm,MesSmp from circle_td where CirSmp = '".$CirSmp."' and SigSta = 0 order by id";
                            $result_SelCir = $conn->query($sql_SelCir)->fetch_assoc();
                                //如果当前到登录账号的角色签名
                            if($result_SelCir['DepIdS'] == $DepIdS)
                            {
                                $y++;
                            }
                                //如果没有符合条件的数据则表明表单达到进入归集流程的条件
                            if($result_SelCir['DepIdS'] == '' || (!isset($result_SelCir['DepIdS'])) )
                            {
                                $sql_ChangeSta02 = "update table_mes set TabSta = 4 where id = '".$row_sel['id']."'";
                                $result_ChangeSta = $conn->query($sql_ChangeSta02);
                            }
                        }
                        $data['data'][$i]['Num'] = $y;
                    }
                    $i++;
                }
                $data['status'] = 'success';
            }
            else{
                $data['status'] = 'fail';
            }
            $json = json_encode($data);
            echo $json;
            break;
            
        //获取表单信息
        case 'listMesType_detail':
            session_start();
            $DepIdS = $_SESSION['RolIdS'];
            $TypeId = $_POST['TypeId'];
            $projectId = $_POST['projectId'];
//          $TypeId = '24';
//          $projectId = '20913a05-9a3d-48a4-ac04-c83ffa06a394';
//          $DepIdS = 20;
            /*
             * 根据工程id和类型id查询表单中的待签表单
             * 根据表单的流转时间戳CirSmp查询表单的当前签批状态
             * */
            $sql_sel = "select id,CirSmp,IntIdA,TabNam,TabCTm,TabDTm,TabMNa from table_mes where ProAId = '".$projectId."' and TabSta = 1 and TabTyp='".$TypeId."'";
            $result_sel = $conn->query($sql_sel);
            $data['row'] = 0;
            $data['status'] = 'error';
            if($result_sel->num_rows>0)
            {
                $i = 0;
                while($row_sel = $result_sel->fetch_assoc())
                {
                    //查询流转信息
                    $CirSmp = $row_sel['CirSmp'];
                    $sql_SelCir = "select id,DepIdS,SigSta,SigCTm,MesCTm,MesSmp,CirSmp from circle_td where CirSmp = '".$CirSmp."' and SigSta = 0 order by id";
                    $result_SelCir = $conn->query($sql_SelCir)->fetch_assoc();
                    if($result_SelCir['DepIdS'] == $DepIdS)
                    {
                        $data['data'][$i]['CirSmp'] = $row_sel['CirSmp'];
                        $data['data'][$i]['id'] = $row_sel['id'];
                        $data['data'][$i]['TabNam'] = $row_sel['TabNam'];
                        $data['data'][$i]['TabCTm'] = $row_sel['TabCTm'];
                        $data['data'][$i]['TabDTm'] = $row_sel['TabDTm'];
                        $data['data'][$i]['TabMNa'] = $row_sel['TabMNa'];
						$data['data'][$i]['IntIdA'] = $row_sel['IntIdA'];
                        $data['data'][$i]['DepIdS'] = $result_SelCir['DepIdS'];
						
                        $i++;
                    }
                }
                $data['row'] = $i;
                $data['status'] = 'success';
            }
//          print_r($data);
            $json = json_encode($data);
            echo $json;
            break;
            
        case 'listMesType_deta':
            session_start();
            $DepIdS = $_SESSION['RolIdS'];
            $TypeId = $_POST['TypeId'];
            $projectId = $_POST['projectId'];
//          $TypeId = '8';
//          $projectId = '0b5c5b47-0927-48ec-a336-9b925881ec54';
//          $DepIdS = 4;
            
            /*
             * 根据截止时间，表单类型，用户角色id，工程id查询表单当前状态视图【circle_detail】 ：此项目对应类型对应觉得的应处理信息条数
             */
            //根据类型id查找表单信息
                //表单查询
            $data['status'] = 'fail';
            $sql_GetMes = "select tableId,TabNam,TabCTm,TabDTm,CirSmp,TabSta,TabMNa from circle_detail where cast(TabDTm as datetime) > '$showtime' and TabTyp = ".$TypeId." and DepIdS = '".$DepIdS."' and ProAId = '".$projectId."' order by TabDTm";
            $result_GetMes = $conn->query($sql_GetMes);
            if($result_GetMes->num_rows>0)
            {
                $i = 0;
                $data['row'] = 0;
                while($row = $result_GetMes->fetch_assoc())
                {
                    
                    //否则输出表单
                    $data['CirSmp'][$i] = $row['CirSmp'];
                    $data['data'][$i]['id'] = $row['tableId'];
                    $data['data'][$i]['TabNam'] = $row['TabNam'];
                    $data['data'][$i]['TabCTm'] = $row['TabCTm'];
                    $data['data'][$i]['TabDTm'] = $row['TabDTm'];
                    $data['data'][$i]['TabSta'] = $row['TabSta'];
                    $data['data'][$i]['TabMNa'] = $row['TabMNa'];
                    $i++;
                }
                $data['row'] = $i;
                
                if($data['row'] > 0)
                {
                    $data['status'] = 'success';
                }
            }
            else
            {
                $data['status'] = 'fail';
            }
            
            $json = json_encode($data);
            echo $json;
            break;    
            
            //获取表单信息
        case 'listMesType_search':
            session_start();
            $DepIdS = $_SESSION['RolIdS'];
            $SearchVal = $_POST['SearchVal'];
            $TypeId = $_POST['TypeId'];
            $projectId = $_POST['projectId'];
//          $TypeId = '8';
//          $projectId = '0b5c5b47-0927-48ec-a336-9b925881ec54';
//          $DepIdS = 4;
            
            /*
             * 根据截止时间，表单名称，表单类型，用户角色，工程id查询表单当前状态视图【circle_detail】 ：根据表单名称搜索表单信息
             */
            //搜索表单信息
                //表单查询
            $data['status'] = 'fail';
//          $sql_GetMes = "select id,TabNam,TabCTm,TabDTm,CirSmp from table_mes where TabTyp = ".$TypeId." and ProAId = '".$projectId."' and TabSta = 1 order by TabDTm";
//          $sql_GetMes = "select id,CirSmp,TabDTm,TabNam,TabCTm,TabSta from circle_detail where cast(TabDTm as datetime) <= '$showtime' and TabNam LIKE '%".$SearchVal."%' and TabTyp = ".$TypeId." and ProAId = '".$projectId."' and DepIdS = '".$DepIdS."' order by TabDTm";
			$sql_GetMes = "select tableId,TabNam,TabCTm,TabDTm,CirSmp,TabSta from circle_detail where cast(TabDTm as datetime) > '$showtime' and TabNam LIKE '%".$SearchVal."%' and TabTyp = ".$TypeId." and DepIdS = '".$DepIdS."' and ProAId = '".$projectId."' order by TabDTm";
            $result_GetMes = $conn->query($sql_GetMes);
            if($result_GetMes->num_rows>0)
            {
                $i = 0;
                $data['row'] = 0;
                while($row = $result_GetMes->fetch_assoc())
                {
                    $data['CirSmp'][$i] = $row['CirSmp'];
                    $data['data'][$i]['id'] = $row['tableId'];
                    $data['data'][$i]['TabNam'] = $row['TabNam'];
                    $data['data'][$i]['TabCTm'] = $row['TabCTm'];
                    $data['data'][$i]['TabDTm'] = $row['TabDTm'];
                    $data['data'][$i]['TabSta'] = $row['TabSta'];
                    $i++;
                }
                $data['row'] = $i;

                if($data['row'] > 0)
                {
                    $data['status'] = 'success';
                }
            }
            else
            {
                $data['status'] = 'fail';
            }
            
            $json = json_encode($data);
            echo $json;
            break;
        
        default:break;
    }
    
    

    
