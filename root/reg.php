<?php
	require("conn.php");
	
	$account = $_POST["account"];
	$password = $_POST["password"];
	$email = $_POST["email"];
	$mobile = $_POST["mobile"];
	$my_name = $_POST["my_name"];
	$role_name = $_POST["role_name"];
	
	if($account){
	$sql = "select * from user where UseAcc ='".$account."' ";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		$jsonresult='该账号已被注册了,请更换!';
	} else {
		$sql = "select * from user where UsePho ='".$mobile."'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			$jsonresult='该手机已被注册，请更换!';
		} else{
			$sql = "select * from user where UseEml ='".$email."'";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
			$jsonresult='该邮箱已被注册,请更换!';
			} else{
				$sqli = "insert into user (UseAcc,UseKey,UseEml,UsePho,UsePeo) values ('$account', '$password', '$email', '$mobile' , '$my_name')";
				
				if ($conn->query($sqli) === TRUE) {
					$autoId = $conn->insert_id;//
					$sql = "INSERT INTO link_userol (UseIdS,RolIdS,LinSmp) values ('$autoId','$role_name','".time()."')";
					$result = $conn->query($sql);
					$jsonresult='success';
				} else {
				    $jsonresult='error';
				}
			}
		}
	}	
	$json = '{"result":"'.$jsonresult.'"
				}';
	echo $json;
	$conn->close();

	}
?>