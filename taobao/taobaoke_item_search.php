<?php
header("Content-Type:text/html;charset=UTF-8");

require_once 'util.php';
require_once 'TaobaoTaobaokeItemsGet.php';

$appKey = '21030823';
$appSecret = '3150b4b60d8447189d8799d0cbd5d820';

$result = TaobaokeItemsGet("小肥羊");          //传入关键字
var_dump($result->taobaoke_items_get_response->taobaoke_items->taobaoke_item);
?>