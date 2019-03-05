<?php
	require("conn.php");
	$account=$_POST["account"];
	$password=$_POST["password"];
//  $account = 'admin';
//  $password = '123456';
	
	$sql = "select id,UseKey,UsePeo,UsePho from user where UseAcc='".$account."' and UseSta=1";
	$result = $conn->query($sql);
	
	$row = $result->fetch_assoc();
	
	if($password==$row["UseKey"])
	{
	    //准备返回信息
		$data['status'] ='success';
		$data['data']['phone'] = $row['UsePho'];
		$data['data']['Name'] = $row['UsePeo'];
		$data['data']['id'] = $row['id'];
		
		//获取部门信息
        $sql_Dept = "select RolNam,RolIdS from Check_UseRol where UseIdS = '".$data['data']['id']."'";
        $result_Dept = $conn->query($sql_Dept);
        $row_Dept = $result_Dept->fetch_assoc();
        $data['data']['RolName'] = $row_Dept['RolNam'];
        $data['data']['RolIdS'] = $row_Dept['RolIdS'];
        
        //将用户信息写入后台缓存
        session_start();
        $_SESSION['RolName'] = $row_Dept['RolNam'];
        $_SESSION['RolIdS'] = $row_Dept['RolIdS'];
        $_SESSION['UsePho'] = $row['UsePho'];
        $_SESSION['UsePeo'] = $row['UsePeo'];
        $_SESSION['useId'] = $row['id'];
        
	}else{
		$data['status']='error';
	}	
	$json = json_encode($data);
	echo $json;
	$conn->close();	
	
?>