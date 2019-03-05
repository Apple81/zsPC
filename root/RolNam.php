<?php
	require("conn.php");		
	$sqldate = "";
	
		$sql = "select distinct RolNam,id from role where RolSta='0'";
		$result = $conn->query($sql);
		$count=mysqli_num_rows($result);	
		if ($result->num_rows > 0) {
			 while($row = $result->fetch_assoc()) {
			 	$sqldate= $sqldate.'{"RolNam":"'.$row["RolNam"].'","id":"'.$row["id"].'"},';
			 }
		} else {
	
		}
		$jsonresult='success';
		$otherdate = '{"result":"'.$jsonresult.'",
					"count":"'.$count.'"
					}';

	$json = '['.$sqldate.$otherdate.']';
//	$json = '{"result":"success","data":[{"id":"18","RolNam":"施工部门-项目经理"},{"id":"19","RolNam":"施工部门-质量检查员"}],"count":"7"}';
	echo $json;
	$conn->close();		
?>