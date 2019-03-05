<?php
    header('Access-Control-Allow-Origin:*');
    
    //获取数据
//  $widthBackG = $_POST['widthBackG'];//背景宽度
//  $heightBackG = $_POST['heightBackG'];//背景高度
//  $widthSign = $_POST['widthSign'];//签名宽度
//  $heightSign = $_POST['heightSign'];//签名高度
//  $localX = $_POST['localX'];//横坐标
//  $lacalY = $_POST['lacalY'];//纵坐标
//  $SvgMes = $_POST['SvgMes'];//签名图
//  $FromId = $_POST['FromId'];//表单id
    
    $flag = $_POST['flag'];
//  $data['flag'] = $flag;
    $data['status'] = 'error';
    switch( $flag )
    {
        case 'SaveSvgFile':
            //获取签名数据
            $SignMes = $_POST['svgElement'];
            //创建签名文件
                //获取表单id和当前时间戳【毫秒级时间戳】
                $formId = $_POST['formId'];
                //$timeSmp = strtotime(date('Y-m-d'));
                    //毫秒级时间戳
                list($s1, $s2) = explode(' ', microtime());
                $timeSmp = (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
                //判断当前表单有没有对应文件夹
                $path = '../../signUpload/'.$formId;
                if(!file_exists($path))
                {
                    //创建目录
                    mkdir($path,0777,true);
                }
                //创建svg文件【以毫秒级时间戳作为文件名】
                $pathAll = $path.'/'.$timeSmp.'.svg';
                $pathReturn = 'signUpload/'.$formId.'/'.$timeSmp.'.svg';
                $route = fopen($pathAll,'x+') or die("保存签名信息出现问题，请及时联系管理员，错误类型：".$php_errormsg);
                //写入svg文件【写入SVG图片数据】[fwrite() 返回写入的字符数]
                $fileSta = fwrite($route,$SignMes);
            //关闭文件
            fclose($route);
            //返回svg文件路径
            if($fileSta){
                $data['path'] = $pathReturn;
                $data['status'] = 'success';
            }
            break;
//      case 'SaveSvgFile':
//          break;
//      case 'SaveSvgFile':
//          break;
//      case 'SaveSvgFile':
//          break;
//      case 'SaveSvgFile':
//          break;
        default:break;
    }
    
    $json = json_encode($data);
    echo $json;