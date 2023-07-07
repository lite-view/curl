<?php


require __DIR__ . '/vendor/autoload.php';


$r = LiteView\Curl\Lite::request()->get('https://songcj.com/server_info.php');
var_dump($r);

$r = LiteView\Curl\Lite::request()->post('https://songcj.com/server_info.php',[]);
var_dump($r);

