<?php
header('Access-Control-Allow-Origin: *');
$_GET = array_map('addslashes', $_GET);
$prosmort_id=(int)$_GET['prosmort_id'];if ($prosmort_id==0)exit;

$redis = new Redis();
$redis->pconnect('185.75.90.54', 6379);
$redis->select(4);
$block=$redis->get('c:'.$prosmort_id);
if ($block){$redis->set('c:'.$prosmort_id, 1, 1296000);exit;}else{$redis->set('c:'.$prosmort_id, 1, 1296000);}
$redis->close();

$db = new PDO("mysql:host=185.75.90.54;dbname=corton", 'corton', 'H4x4B2y5', array(PDO::ATTR_PERSISTENT => true));
$dbstat = new PDO("mysql:host=185.75.90.54;dbname=corton-stat", 'corton', 'H4x4B2y5', array(PDO::ATTR_PERSISTENT => true));

$sql= "SELECT `id` FROM `ploshadki` WHERE `domen`='".$_GET['host']."'";
$ploshadka_id = $db->query($sql)->fetch(PDO::FETCH_COLUMN);

$sql = "UPDATE `stat_promo_prosmotr` SET `click` = '1' WHERE `prosmotr_id` = '" . $_GET['prosmort_id'] . "'";
$db->query($sql);

$sql = "UPDATE `stat_promo_day_count` SET `clicking` = `clicking` + 1 WHERE `data`=CURDATE() AND `anons_id`='".$_GET['anons_id']."'";
if (!$dbstat->exec($sql)){$dbstat->query("INSERT INTO `stat_promo_day_count` SET `anons_id` = '".$_GET['anons_id']."', `data` = CURDATE(), `clicking` = 1");
}

$sql ="UPDATE `balans_ploshadki` SET `".$_GET['t']."_promo_click` = `".$_GET['t']."_promo_click` + 1 WHERE `ploshadka_id`='".$ploshadka_id."' AND `date`=CURDATE();";
$dbstat->query($sql);