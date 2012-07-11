<?php
header("Content-Type:text/html;charset=UTF-8");

require_once 'jiepang.api.php';

$jiepang = new JiepangApi();
$auth_url = $jiepang->get_authorize_url();
   
if (isset($auth_url))               //用户授权
{ 
    Header("HTTP/1.1 303 See Other"); 
    Header("Location: $auth_url"); 
    exit();
}

?>