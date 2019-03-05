<?php
    require ('../conn.php');
    $falg = $_POST['type'];
    $ProId = $_POST['ProId'];
//  $ProId = '0b5c5b47-0927-48ec-a336-9b925881ec54';
    
    if($falg == 'table')
    {
        $sql_countTab = "select count(id) as TabNum from table_mes where ProAId = '".$ProId."'";
        $result = $conn->query($sql_countTab);
        $row = $result->fetch_assoc();
    }
    if($falg == 'document')
    {
        
    }
    
    $data['statua'] = 'error';
    if(isset($row)){
        $data['statua'] = 'success';
        $data['row'] = $row['TabNum'];
    }
    
    $json = json_encode($data);
    echo $json;
    
?>