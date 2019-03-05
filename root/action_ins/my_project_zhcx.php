<?php
	require("../conn.php");
	$starttimevalue=$_POST["starttimevalue"]; //起始日期
	$endtimevalue=$_POST["endtimevalue"]; //截止日期
	$projectId = $_POST['projectId'];
	
	$showtime=date("Y-m-d");
	
	//待签批  逾期  驳回  归集
	$sql1 = "select count(*) as sign from table_mes where TabSta='1' and ProAId = '".$projectId."' and TabUDa between '$starttimevalue'and '$endtimevalue' ";
	$result1 = $conn->query($sql1);
	if($result1->num_rows > 0){
		while($row = $result1->fetch_assoc()){
			$countsign = $row["sign"];
		}
	}
	
	
	//逾期
	$sql2 = "select count(*) as overdue from table_mes where TabSta='3' and ProAId = '".$projectId."' and TabUDa between '$starttimevalue'and '$endtimevalue' ";
	$result2 = $conn->query($sql2);
	if($result2->num_rows > 0){
		while($row = $result2->fetch_assoc()){
			$countoverdue = $row["overdue"];
		}
	}
	
	//驳回
	$sql3 = "select count(*) as reject from table_mes where TabSta='2' and ProAId = '".$projectId."' and TabUDa between '$starttimevalue'and '$endtimevalue' ";
	$result3 = $conn->query($sql3);
	if($result3->num_rows > 0){
		while($row = $result3->fetch_assoc()){
			$countreject = $row["reject"];
		}
	}
	
	
	//归集
	$sql5 = "select count(*) as Imputation from table_mes where TabSta='4' and ProAId = '".$projectId."' and TabUDa between '$starttimevalue'and '$endtimevalue' ";
	$result5 = $conn->query($sql5);
	if($result5->num_rows > 0){
		while($row = $result5->fetch_assoc()){
			$countImputation = $row["Imputation"];
		}
	}
	
	
	$sqldate="";
	$jsonresult='success';
	$otherdate = '{"result":"'.$jsonresult.'",
				"countsign":"'.$countsign.'",
				"countoverdue":"'.$countoverdue.'",
				"countreject":"'.$countreject.'",
				"countImputation":"'.$countImputation.'"
			}';		
	$json = '['.$sqldate.$otherdate.']';
	
	echo $json;
	$conn->close();
	
?>