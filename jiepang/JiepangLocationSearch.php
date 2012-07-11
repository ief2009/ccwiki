<?php

require_once 'jiepang.api.php';
session_start();
$jiepang = new JiepangApi();

$locations = array();   //存放地点信息
$photos = array();      //存放图片信息
$events = array();      //存放事件信息
$address = array();     //存放地址信息

$locations = $jiepang->api('locations/search', array
(
   'lat' => '30.2613',      //经纬度参数           
   'lon' => '120.1255',            
   'q' => '西湖',             //关键词参数
   'count' => '10',         //返回结果数
));

foreach($locations['items'] as &$location)                  //对于搜索到的每一个地点
{
    $location['categories'] = '';
    $photos = $jiepang->api('locations/photos', array
    (           
       'guid' => $location['guid'],  
       'count' => '10',
    ));
    foreach($photos['items'] as &$photo)                   //查看图片信息，并保存在相对应的locations['items']['photo']数组下
    {
        $location['photos']['body'] = $photo['body'];
        $location['photos']['photo'] = $photo['photo'];
        $location['photos']['id'] = $photo['id'];
     }
    
    $address = $jiepang->api('locations/show', array        //查看地址信息，并保存在对应的location['items']中
    (           
       'guid' => $location['guid'],  
    ));
    $location['lat'] = $address['lat'];
    $location['lon'] = $address['lon'];
    $location['city'] = $address['city']; 
    
    if($location['has_event'])                          //如果有活动，则查看活动信息，并保存在对应的location['event']中
    {
        $events = $jiepang->api('locations/event', array
        (           
          'guid' => $location['guid'],  
        ));

        $location['event']['content'] = $events['content'];
        $location['event']['start'] = $events['start'];
        $location['event']['end'] = $events['end'];
        $location['event']['url'] = $events['url'];
      }
      else                                               
          $location['event'] = '';
}
var_dump($locations);
var_dump($locations['items'][0]['photos']);
?>
