<?php
require("../conn.php");
$Sign_imgurl = $_POST["Sign_imgurl"];
$sjc = $_POST["sjc"];

	$sqldate = "";
	$sqli = "SELECT * FROM `sign_authorizer`";
	$result = $conn->query($sqli);
	if($result->num_rows>0){
		$sql = "update  `sign_authorizer` set SignSvg = '".$Sign_imgurl."' where sjc = '".$sjc."' ";
		if ($conn->query($sql) === true) {
			$jsonresult='success';
		}else{
			$jsonresult='error';
		}
		
	}
	$json = '{"result":"'.$jsonresult.'"		
				}';
	echo $json;
	$conn->close();
?>