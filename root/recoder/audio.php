<?php
	require("../conn.php");		
	$sqldate = "";
	$CId=$_POST["CId"];
//	$CId=122;
	
		$sql = "select mp3Flie from audio where CId='".$CId."'";
		$result = $conn->query($sql);
		$count=mysqli_num_rows($result);	
		if ($result->num_rows > 0) {
			 while($row = $result->fetch_assoc()) {
			 	$sqldate= $sqldate.'{"mp3Flie":"'.$row["mp3Flie"].'"},';
			 }
		} else {
	
		}
		$jsonresult='success';
		$otherdate = '{"result":"'.$jsonresult.'",
					"count":"'.$count.'"
					}';

	$json = '['.$sqldate.$otherdate.']';
//	$returnData = json_encode($json);
	echo $json;
	$conn->close();		
?>