<?php
    require("../conn.php");

	$sqldate = ""; 

	$sql = "select * from circle_detail";
	$result = $conn->query($sql);
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
			$sqldate = $sqldate.'{"id":"'. $row["id"].'","SigCTm":"'. $row["SigCTm"].'","projectName":"'. $row["projectName"].'","ProAId":"'. $row["ProAId"].'","TabNam":"'. $row["TabNam"].'"},';
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