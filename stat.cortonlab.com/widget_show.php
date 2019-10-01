<?php
header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
header("Access-Control-Allow-Credentials: true");
require_once('/var/www/www-root/data/www/panel.cortonlab.com/config/db.php');

//$_SERVER['HTTP_ORIGIN']='http://okardio.com/dfhgj';

$domen=parse_url ( $_SERVER['HTTP_ORIGIN'], PHP_URL_HOST );
$sql= "SELECT `id` FROM `ploshadki` WHERE `domen`='".$domen."'";

$ploshadka_id = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
if (!$ploshadka_id)exit;

$redis = new Redis();
$redis->pconnect('185.75.90.54', 6379);
$redis->select(1);
$arr=explode(',',addslashes($_GET['anons_ids']));

foreach ($arr as $value) {
    $value=substr($value, 0, -1);
    $redis->incr(date('d').':'.$value);
};

$redis->select(3);
$valueold="";
foreach ($arr as $value) {
    $value=substr($value, -1);
    $redis->incr(date('d').':'.$ploshadka_id.':'.$value);
    if (($value!=$valueold) and ($value=='r')){
        $sql= "INSERT INTO `widget_prosmotr`(`prosmotr_id`, `ploshadka_id`, `date`) VALUES ('".addslashes($_GET['prosmort_id'])."','".$ploshadka_id."',CURDATE())";
        $GLOBALS['dbstat']->query($sql);
        $valueold='r';
    }

};
$redis->close();