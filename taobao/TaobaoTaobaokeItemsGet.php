<?php
require_once 'util.php';
function TaobaokeItemsGet($keyword)
{
    //参数数组
    $paramArr = array(
      'app_key' => '21030823',
      'method' => 'taobao.taobaoke.items.get',
      'format' => 'json',
      'v' => '2.0',
      'sign_method'=>'md5',
      'timestamp' => date('Y-m-d H:i:s'),
      'fields' => 'name_iid,title,price,item_location,seller_credit_score,click_url,shop_click_url,pic_url,volume',
      'nick' => 'ief菲菲',
      'keyword' => $keyword
      );
      
    //生成签名
    $sign = createSign($paramArr);
    
    //组织参数
    $strParam = createStrParam($paramArr);
    $strParam .= 'sign='.$sign;
    
    //访问服务
    $url = 'http://gw.api.taobao.com/router/rest?'.$strParam; //正式环境调用地址
    $result = file_get_contents($url);
    $result = json_decode($result);
    
    return $result;
}
?>