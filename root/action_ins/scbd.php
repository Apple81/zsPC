<?php
require("../conn.php");
$obj = $_POST["name"];
$content =$_POST["content"];
$len = sizeof($obj);
for($i=0;$i<$len;$i++){
	$por = $_POST[$obj[$i]];
	$lxmc = explode("|", $obj[$i]);
	$sqli = "SELECT * FROM `测点信息` WHERE `编号`='$obj[$i]'";
	$result = $conn->query($sqli);
	if($result->num_rows>0){
		$sql = "UPDATE `测点信息` SET X坐标='".$por[0]."',Y坐标='".$por[1]."',父图X坐标='".$por[2]."',父图Y坐标='".$por[3]."',状态='save',批注='".$content."' WHERE `编号`='".$lxmc[0]."'";
		if ($conn->query($sql) === TRUE) {
			$over = "更新成功！";
		}else{
			$over = "第".($i+1)."条数据保存出错";
			break;
		}
	}else{
		
	}
	
}
echo $over;
$conn -> close();
?>