<?php
    header("Access-Control-Allow-Origin: *");
    require('../conn.php');
    //获取传值
    $CId = $_POST['CId'];
//  $CId = 55;
    /*
     * 获取表单数据
     */
    //获取表单的基本信息
    $sql = "select id,IntIdA,TabNam,CirSmp,ImgUrl,page from table_mes  where id = '".$CId."'";
    $result = $conn->query($sql);
    if($result->num_rows>0)
    {
        while($row = $result->fetch_assoc())
        {
            $data['data']['id'] = $row['id'];
            $data['data']['IntIdA'] = $row['IntIdA'];
            $data['data']['TabNam'] = $row['TabNam'];
            $data['data']['imgurl'] = $row['ImgUrl'];
            $data['data']['page'] = $row['page'];
            $CirSmp = $row['CirSmp'];
            $page = $row['page'];
        }
    }
    
    //获取表单的签名信息
    $sql_sign = "select SignPa,SignPX,SignPY,FormH,FormW,SignW,SignH,PageFinal from sign_detail where CirSmp = '".$CirSmp."'";
    $data['sql'] = $sql_sign;
    $result_sign = $conn->query($sql_sign);
    if( $result_sign -> num_rows > 0 )
    {
        $y = 0;
        while($row_sign = $result_sign->fetch_assoc())
        {
            $data['sign'][$y]['SignPa'] = $row_sign['SignPa'];
            $data['sign'][$y]['SignPX'] = $row_sign['SignPX'];
            $data['sign'][$y]['SignPY'] = $row_sign['SignPY'];
            $data['sign'][$y]['FormW'] = $row_sign['FormW'];
            $data['sign'][$y]['FormH'] = $row_sign['FormH'];
            $data['sign'][$y]['SignW'] = $row_sign['SignW'];
            $data['sign'][$y]['SignH'] = $row_sign['SignH'];
            $data['sign'][$y]['PageFinal'] = $row_sign['PageFinal'];
            $y ++;
        }
    }
    
    if(isset($data['data']['imgurl']))
    {
        $data['status'] = 'success';
        $json = json_encode($data);
        echo $json;
        return 0;
    }
    $data['status'] = 'fail';
    $json = json_encode($data);
    echo $json;
