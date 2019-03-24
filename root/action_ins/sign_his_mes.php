<?php
    require("../conn.php");
	$id = $_POST["id"];
	$sqldate = ""; 

	$sql = "select * from user where id = '".$id."'";
	$result = $conn->query($sql);
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
			$sqldate = $sqldate.'{"UsePeo":"'. $row["UsePeo"].'"},';
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