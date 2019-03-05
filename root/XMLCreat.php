<?php
    
    $url = 'tastatsatst';
    $page = '4';
    
    $doc = new DOMDocument('1.0', 'utf-8');
    $doc->formatOutput = true;
    $rootEle = $doc->createElement('root');
    $doc->appendchild($rootEle);
    $descriptionEle = $doc->createElement('description');
    $rootEle->appendChild($descriptionEle);
    $couponNameEle = $doc->createElement('ImgUrl');
    $couponDescriptionEle = $doc->createElement('page');
    $couponNameEle->appendChild($doc->createTextNode($url));
    $couponDescriptionEle->appendchild($doc->createTextNode($page));
    $descriptionEle->appendchild($couponNameEle);
    $descriptionEle->appendChild($couponDescriptionEle);
    
    $detailEle = $doc->createElement('detail');
    $rootEle->appendchild($detailEle);

//  $itemEle = $doc->createElement('Img_2');
//  $detailEle->appendChild($itemEle);
//  $codeEle = $doc->createElement('code');
//  $codeEle->appendchild($doc->createTextNode('999999'));
//  $itemEle->appendchild($codeEle);
    
    for($i=0;$i<$page;$i++)
    {
        $itemEle = $doc->createElement('Image-'.($i+1));
        $detailEle->appendChild($itemEle);
        $ImgMes = $doc->createTextNode('666666');
        $itemEle->appendchild($ImgMes);
    }
    
    $doc->save('../xmlImg/' . '001.xml');
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
//  $data_array = array(
//      array(
//      'title' => 'title1',
//      'content' => 'content1',
//          'pubdate' => '2009-10-11',
//      ),
//      array(
//      'title' => 'title2',
//      'content' => 'content2',
//      'pubdate' => '2009-11-11',
//      )
//  );
//   
//  //  属性数组
//  $attribute_array = array(
//      'title' => array(
//      'size' => 1
//      )
//  );
//   
//  //  创建一个XML文档并设置XML版本和编码。。
//  $dom=new DomDocument('1.0', 'utf-8');
//   
//  //  创建根节点
//  $article = $dom->createElement('article');
//  $dom->appendchild($article);
//   
//  foreach ($data_array as $data) {
//      $item = $dom->createElement('item');
//      $article->appendchild($item);
//   
//      create_item($dom, $item, $data, $attribute_array);
//  }
//   
//  echo $dom->saveXML($item);
//   
//  function create_item($dom, $item, $data, $attribute) {
//      if (is_array($data)) {
//          foreach ($data as $key => $val) {
//              //  创建元素
//              $$key = $dom->createElement($key);
//              $item->appendchild($$key);
//   
//              //  创建元素值
//              $text = $dom->createTextNode($val);
//              $$key->appendchild($text);
//   
//              if (isset($attribute[$key])) {  //  如果此字段存在相关属性需要设置
//                  foreach ($attribute[$key] as $akey => $row) {
//                      //  创建属性节点
//                      $$akey = $dom->createAttribute($akey);
//                      $$key->appendchild($$akey);
//   
//                      // 创建属性值节点
//                      $aval = $dom->createTextNode($row);
//                      $$akey->appendChild($aval);
//                  }
//              }   //  end if
//          }
//      }   //  end if
//  }   //  end function