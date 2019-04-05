<?php
require("../conn.php");

$sjc = $_POST["sjc"];

	$sqldate = "";
	
	if($sjc==1){
		$sqli = "SELECT * FROM `sign_authorizer`";
		$result = $conn->query($sqli);
		if($result->num_rows>0){
		    while($row=$result->fetch_assoc()){
				$sqldate= $sqldate.'{"id":"'.$row["id"].'","ProNam":"'.$row["ProNam"].'","name":"'.$row["name"].'","phone":"'.$row["phone"].'","job":"'.$row["job"].'","sjc":"'.$row["sjc"].'"},';
			}
		}else{
			
		}
	}else{
		$sqli = "SELECT * FROM `sign_authorizer` where sjc = '".$sjc."'";
		$result = $conn->query($sqli);
		if($result->num_rows>0){
		    while($row=$result->fetch_assoc()){
				$sqldate= $sqldate.'{"SignSvg":"'.$row["SignSvg"].'"},';
			}
		}else{
			
		}
	}
	$jsonresult='success';
	$otherdate = '{"result":"'.$jsonresult.'"
				}';
				
	$json = '['.$sqldate.$otherdate.']';
	echo $json;
	$conn->close();
?>