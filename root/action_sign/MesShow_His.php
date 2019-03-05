<?php
    header('Access-Control-Allow-Origin:*');
    
    /*
     * 显示表单签名信息
     * */    
    require'../conn.php';
    
    //获取表单id
    $CId = $_POST['CId'];
//  $CId = 55;
    
    //获取所有流程
    $sql = "select TabUDa,RolNam,SigCTm,id,SigSta,CirSmp from circle_detail where tableId = '".$CId."'";
    $result = $conn->query($sql);
//  echo $sql;
    if($result -> num_rows>0)
    {
        $i=0;
        while($row = $result->fetch_assoc())
        {
            $data['his'][$i]['TabUDa'] = $row['TabUDa'];//表单上传时间
            $data['his'][$i]['RolNam'] = $row['RolNam'];//角色名称
            $data['his'][$i]['SigCTm'] = $row['SigCTm'];//签名时间
            $data['his'][$i]['SigSta'] = $row['SigSta'];//签名状态
//          echo $row['SigSta'];
            //判断流程状态
            switch($row['SigSta']){
                case 0:
                    $data['signMes'][$i]['CirSta'] = '待签批';
                    $data['signMes'][$i]['SignPa'] = '待签名';
                    $data['signMes'][$i]['SignDate'] = '待签名';
                    $data['signMes'][$i]['SignEls'] = '待签名';
                    $data['signMes'][$i]['SigCTm'] = '待签名';
                    $data['signMes'][$i]['UsePeo'] = '待签名';
                break;
                case 1:
                    $data['signMes'][$i]['CirSta'] = '驳回';
                    $data['signMes'][$i]['SignPa'] = '已驳回';
                    $data['signMes'][$i]['SignDate'] = '已驳回';
                    $data['signMes'][$i]['SignEls'] = '已驳回';
                    $data['signMes'][$i]['SigCTm'] = '已驳回';
                    $data['signMes'][$i]['UsePeo'] = '已驳回';
                break;
                case 2:
                    $data['signMes'][$i]['CirSta'] = '逾期';
                    $data['signMes'][$i]['SignPa'] = '已逾期';
                    $data['signMes'][$i]['SignDate'] = '已逾期';
                    $data['signMes'][$i]['SignEls'] = '已逾期';
                    $data['signMes'][$i]['SigCTm'] = '已逾期';
                    $data['signMes'][$i]['UsePeo'] = '已逾期';
                break;
                case 5:
                    $data['signMes'][$i]['CirSta'] = '已签批';
                    //如果已经签名则获取签名的详情【包括签名人，签名时间,备注，结果】
                    $sql_Cir = "select SignPa,SignDate,SignEls,DepIdS,SigCTm,UsePeo,RolNam from sign_detail where CirSmp = '".$row['CirSmp']."'";
                    $result_Cir = $conn->query($sql_Cir)->fetch_assoc();
                    $data['signMes'][$i]['SignPa'] = $result_Cir['SignPa'];
                    $data['signMes'][$i]['SignDate'] = $result_Cir['SignDate'];
                    $data['signMes'][$i]['SignEls'] = $result_Cir['SignEls'];
                    $data['signMes'][$i]['SigCTm'] = $result_Cir['SigCTm'];
                    $data['signMes'][$i]['UsePeo'] = $result_Cir['UsePeo'];
                break;
                default:break;
            }
            $i++;
        }
    }
    $data['status'] = 'error';
    //组件返回值
    if(isset($data['signMes'][0]['CirSta']))
    {
        $data['status'] = 'success';
    }
//  print_r($data) ;
    $json = json_encode($data);
    echo $json;  
