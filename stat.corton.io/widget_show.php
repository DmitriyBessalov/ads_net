<?php
header('Access-Control-Allow-Origin: *');
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->select(1);
$arr=explode(',',addslashes($_GET['anons_ids']));
foreach ($arr as $value) {
    $value=substr($value, 0, -1);
    $redis->incr(date('d').':'.$value);
};

$domen=parse_url ( $_SERVER['HTTP_ORIGIN'], PHP_URL_HOST );

$db = new PDO("mysql:host=185.75.90.54;dbname=corton", 'www-root', 'Do5aemub0e7893', array(PDO::ATTR_PERSISTENT => true));
$sql= "SELECT `id` FROM `ploshadki` WHERE `domen`='".$domen."'";
$ploshadka_id = $db->query($sql)->fetch(PDO::FETCH_COLUMN);

$redis->select(3);
foreach ($arr as $value) {
    $value=substr($value, -1);
    $redis->incr(date('d').':'.$ploshadka_id.':'.$value);
};

$redis->close();