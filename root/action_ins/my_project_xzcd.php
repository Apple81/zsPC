<?php
require ("../conn.php");
$sjc = $_POST["sjc"];
//$xmid = $_POST["xmid"];
$cdgs = $_POST["cdgs"];
$bh = $_POST["bh"];
$cdlx = $_POST["cdlx"];
$qsbh = $_POST["qsbh"];
//$checkId = $_POST["checkId"];
$bianhao = " ";
if (1) {
	for ($i = $qsbh; $i < $cdgs+$qsbh ; $i++) {
		$bianhao = $bh . $i;
		
		$sqli = "insert into 测点信息 (实测id,测点类型, 编号,时间戳,状态) values ('1','$cdlx','$bianhao','$sjc','0')";
			if ($conn -> query($sqli) === TRUE) {
				$jsonresult = 'success';
			} else {
				$jsonresult = 'error';
			}
	}
	$json = '{"result":"' . $jsonresult . '"		
		}';
	echo $json;
	  
	$conn -> close();
}
?>  