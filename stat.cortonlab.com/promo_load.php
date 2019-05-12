<?php
header('Access-Control-Allow-Origin: *');
$_GET = array_map('addslashes', $_GET);

$prosmort_id=(int)$_GET['prosmort_id'];if ($prosmort_id==0)exit;
if (($_GET['ref']=="") OR (!isset($_GET['anons_id'])) OR ($_GET['anons_id']==""))exit;

$domen=parse_url($_SERVER['HTTP_ORIGIN'], PHP_URL_HOST);

$db = new PDO("mysql:host=185.75.90.54;dbname=corton", 'www-root', 'Do5aemub0e7893', array(PDO::ATTR_PERSISTENT => true));
$dbstat = new PDO("mysql:host=185.75.90.54;dbname=corton-stat", 'www-root', 'Do5aemub0e7893', array(PDO::ATTR_PERSISTENT => true));

$sql= "SELECT `id` FROM `ploshadki` WHERE `domen`='".$domen."'";
$ploshadka_id = $db->query($sql)->fetch(PDO::FETCH_COLUMN);

$sql= "INSERT INTO 
    `stat_promo_prosmotr`
SET
    `date` = '".date('Y-m-d')."',
    `ploshadka_id` = ".$ploshadka_id.",
    `prosmotr_id` = '".$prosmort_id."',
    `anon_id` = '".$_GET['anons_id']."',
    `url_ref` = '".$_GET['ref']."',
    `ip` = '".$_SERVER['REMOTE_ADDR']."',
    `tizer` = '".$_GET['t']."',
    `user-agent`= '".$_SERVER['HTTP_USER_AGENT']."',
    `timestamp` = '".date('H:i:s')."'";
$dbstat->query($sql);

$sql = "UPDATE `stat_promo_day_count` SET `perehod` = `perehod` + 1 WHERE `data`=CURDATE() AND `anons_id`='".$_GET['anons_id']."'";
if (!$dbstat->exec($sql)){$dbstat->query("INSERT INTO `stat_promo_day_count` SET `anons_id` = '".$_GET['anons_id']."', `data` = CURDATE(), `perehod` = 1");
}

$sql = "UPDATE `balans_ploshadki` SET ".$_GET['t']."_promo_load=".$_GET['t']."_promo_load+1  WHERE `date`=CURDATE() AND `ploshadka_id`='".$ploshadka_id."'";
if (!$dbstat->exec($sql)){
    $sql = "INSERT INTO `balans_ploshadki` SET ".$_GET['t']."_promo_load=".$_GET['t']."_promo_load+1, `date`=CURDATE() AND `ploshadka_id`='".$ploshadka_id."'";
    $dbstat->query($sql);
}

/* Структура Redis
 * db0 id_Просмотра
 * db1 Счетчики показа анонсов за день
 * db2 block_ip
 */