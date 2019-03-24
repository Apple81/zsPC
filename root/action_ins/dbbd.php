<?php
//file
require '../conn.php';
$flag = $_POST["flag"];

$sqldate = "";
//get data
if ($flag == 1) {
	$sql = "select * from table_mes ";
	$result = $conn -> query($sql);
	$class = mysqli_num_rows($result);
	if ($result -> num_rows > 0) {
		while ($row = $result -> fetch_assoc()) {
			$sqldate = $sqldate . '{"id":"' . $row["id"] . '","表单名称":"' . $row["TabNam"] . '","提交日期":"' . $row["TabUDa"] . '","表单状态":"' . $row["TabSta"] . '"},';
		}
	} else {

	}
} else if ($flag==2) {
	$sql = "select * from file_mes ";
	$result = $conn -> query($sql);
	$class = mysqli_num_rows($result);
	if ($result -> num_rows > 0) {
		while ($row = $result -> fetch_assoc()) {
			$sqldate = $sqldate . '{"id":"' . $row["id"] . '","文档名称":"' . $row["FleNam"] . '","创建日期":"' . $row["FleCDa"] . '","文档状态":"' . $row["FleSta"] . '","文档编制人":"' . $row["FleCPe"] . '"},';
		}
	} else {

	}
}
$jsonresult = 'success';
$otherdate = '{"result":"' . $jsonresult . '",
					"count":"' . $class . '"
				}';

$json = '[' . $sqldate . $otherdate . ']';
//return
//  print_r($data);
echo $json;
$conn -> close();
?>