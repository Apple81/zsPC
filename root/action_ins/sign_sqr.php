<?php
    //file
    require '../conn.php';
    $sqldate = "";
    //get data
    $sql = "select * from sign_detail ";
    $result = $conn->query($sql);
    $class = mysqli_num_rows($result);
    if($result->num_rows>0){
    	while($row=$result->fetch_assoc()){
			$sqldate= $sqldate.'{"id":"'.$row["id"].'","UsePeo":"'.$row["UsePeo"].'"},';
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