<?php
    require('../conn.php');
    //获取用户信息
    session_start();
    $RolIdS = $_SESSION['RolIdS'];//角色名
    $UseId = $_SESSION['useId'];//用户id
    //获取表单信息
    $formId = $_POST['formId'];//表单id
    $Mes = $_POST['Mes'];//驳回备注
    
    /*
     * 获取流转时间戳，根据时间戳和
     * */
    //根据表单的流转时间戳和部门id获取流转流程的id
        //根据表单id获取流转时间戳
    $sql_Stamp = "select CirSmp from table_mes where id = '".$formId."'";
    $result_Stamp = $conn->query($sql_Stamp)->fetch_assoc();
    if($result_Stamp)
    {
            //更新流转信息表【circle_td】【circle_td驳回：1】
        $sql_Update = "update circle_td set SigSta = '1',HisIdS = '' where DepIdS = '".$RolIdS."' and CirSmp = '".$result_Stamp['CirSmp']."' and  SigSta = 0 order by id asc";
        $result_Updata = $conn->query($sql_Update);
        if(!($result_Updata))
        {
            $data['ErrorMes'] = '更新流转信息表出现错误';
        }else{
            //更新表单状态【将签批状态改为驳回状态】【table_mes驳回：2】
            $sql_changeSta = "update table_mes set TabSta = 2 where id = '".$formId."'";
            $result_changeSta = $conn->query($sql_changeSta);
        }
    }else{
        $data['ErrorMes'] = '获取流转时间戳出现错误';
    }
    
    $data['status'] = 'error';
    if($result_Updata)
    {
        $data['status'] = 'success';
    }
//  print_r($data) ;
    $json = json_encode($data);
    echo $json;