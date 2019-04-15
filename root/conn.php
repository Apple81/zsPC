<?php
    //本地连接
	$servername = "127.0.0.1:3306";
	$username = "root";
	$password = "123456";
	$dbname = "zsdb";
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}else{
//        echo "Connected successfully";
	}

?> 