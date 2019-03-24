<?php
    //file
    require '../conn.php';
	$id = $_POST["id"];
    $sqldate = "";
    //get data
    $sql = "select * from 测点信息  where id = '".$id."' ";
    $result = $conn->query($sql);
    $class = mysqli_num_rows($result);
    if($result->num_rows>0){
    	while($row=$result->fetch_assoc()){
			$sqldate= $sqldate.'{"编号":"'.$row["编号"].'","批注":"'.$row["批注"].'"},';
		 }
    }else{
        
    }
    $jsonresult='success';
	$otherdate = '{"result":"'.$jsonresult.'",
					"count":"'.$class.'"
				}';
				
	$json = '['.$sqldate.$otherdate.']';
    //return
//  print_r($data);
	echo $json;
	$conn->close();
?>