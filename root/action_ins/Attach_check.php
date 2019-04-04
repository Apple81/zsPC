<?php
    //file
    require '../conn.php';
	$sjc =$_POST["sjc"];
    $sqldate = "";
    //get data
    $sql = "select * from table_mes where CirSmp = '".$sjc."'";
    $result = $conn->query($sql);
    $class = mysqli_num_rows($result);
    if($result->num_rows>0){
    	while($row=$result->fetch_assoc()){
			$sqldate= $sqldate.'{"fileUrl":"'.$row["fileUrl"].'"},';
			
		 }
    }else{
        
    }
//	$sqldate ='http://192.168.1.184:8081/ZS-PC/formUpload'. $sqldate;
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