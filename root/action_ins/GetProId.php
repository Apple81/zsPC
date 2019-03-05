<?php
    //file
    require '../conn.php';
    
    //get data
    $sql = "select ProAId from table_mes group by ProAId";
    $result = $conn->query($sql);
    
    //creat retuen data
    $data['status'] = 'error';
    if($result->num_rows>0)
    {
        $i = 0;
        while($row = $result->fetch_assoc())
        {
            $data['data'][$i] = $row['ProAId'];
            $i++;
        }
        $data['status'] = 'success';
    }else{
        $data['status'] = 'none';
    }
    
    //return
//  print_r($data);
    $json = json_encode($data);
    echo $json;
