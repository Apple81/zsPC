<?php
require('../conn.php');
$sjc = $_POST['sjc'];
$sql = "SELECT * FROM `测点信息` where 时间戳='".$sjc."'";
$sqldate="";
$result = $conn->query($sql);
if($result->num_rows>0){
	while ($row = $result -> fetch_assoc()) {
		$sqldate = $sqldate . '{"id":"' . $row["id"] . '","X":"' . $row["X坐标"] . '","Y":"' . $row["Y坐标"] . '","X_father":"' . $row["父图X坐标"] . '","Y_father":"' . $row["父图Y坐标"] . '","num":"' . $row["编号"] . '","测点类型":"' . $row["测点类型"] . '","状态":"' . $row["状态"] . '","id":"' . $row["id"] . '"},';
	}
}
$jsonresult = 'success';
$otherdate = '{"result":"'.$jsonresult.'"
					}';

$json = '[' . $sqldate . $otherdate . ']';
echo $json;

$conn -> close();
?>