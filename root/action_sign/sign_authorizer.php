<?php
require("../conn.php");
$ProNam = $_POST["ProNam"];
$sjc = $_POST["sjc"];
$name = $_POST["name"];
$phone = $_POST["phone"];
$job = $_POST["job"];
$imgurl = $_POST["imgurl"];
$flag = $_POST["flag"];
//$ProNam = AAAAA;
//$name = sdadd;
//$phone = FFFF;
//$job = YYYYY;
	$sqldate = "";
	$sqli = "SELECT * FROM `sign_authorizer`";
	$result = $conn->query($sqli);
	if($result->num_rows>0){
		if($flag==1){
			$sql = "INSERT INTO `sign_authorizer` (sjc,ProNam,name,phone,job,SignSvg) VALUES ('$sjc','$ProNam','$name','$phone','$job','$imgurl')";
			if ($conn->query($sql) === true) {
				$jsonresult='success';
			}else{
				$jsonresult='error';
			}
		}
	}
	$json = '{"result":"'.$jsonresult.'"		
				}';
	echo $json;
	$conn->close();
?>