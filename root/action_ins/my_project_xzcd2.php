<?php
require ("../conn.php");
$sjc = $_POST["sjc"];
$bh = $_POST["cdlxbh"];
$sql = "SELECT id,编号 from 测点信息 where 时间戳='".$sjc."' and 编号 LIKE '".$bh."%' ORDER BY  id DESC LIMIT 1";
$result = $conn -> query($sql);
$bh_sql = "";
if($result->num_rows>0){
	while($row = $result->fetch_assoc()){
				$bh_sql = $row['编号'];
	    }
     } if(!empty($bh_sql)){
			$jsonresult = 'success';
			$sqidata = $bh_sql;
		}else{
			$jsonresult = 'error';
			$sqidata = " ";
		}
	$json = '{"result":"' . $jsonresult . '","sqldata":"'.$sqidata.'"}';
	echo $json;  
	$conn -> close();
?>  