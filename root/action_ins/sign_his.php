<?php
    require("../conn.php");
	$userid = $_POST["userid"];
	$sqldate = ""; 

	$sql = "select * from sign_detail where userid = '".$userid."'";
	$result = $conn->query($sql);
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
			$sqldate = $sqldate.'{"id":"'. $row["id"].'","SignDate":"'. $row["SignDate"].'","projectName":"'. $row["ProNam"].'","TabNam":"'. $row["TabNam"].'"},';
		}
	}
	$jsonresult = 'success';
	$otherdate = '{"result":"'.$jsonresult.'"
				}';
	$json = '['.$sqldate.$otherdate.']';
//	}	
	echo $json;
	$conn->close();		
?>