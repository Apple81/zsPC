<?php
    
//  $url = $_GET['url'];
    $url = 'http://192.168.0.198:8080/TongXinweb/images/20913a05-9a3d-48a4-ac04-c83ffa06a394/60799dcd-27bd-48ab-8bc3-af64e7ad8c0d(1).png';
    
    function Base64EncodeImage($ImageFile) {
        if(file_exists($ImageFile) || is_file($ImageFile)){
            $base64_image = '';
            $image_info = getimagesize($ImageFile);
            $image_data = fread(fopen($ImageFile, 'r'), filesize($ImageFile));
            $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
            return $base64_image;
        }
        else{
            return false;
        }
    }
    $data['img'] = Base64EncodeImage($url);
    if(isset($data['img']))
    {
        $data['status'] = 'success';
    }else{
        $data['status'] = 'fail';
    }
    $json = json_encode($data);
    echo $json;
