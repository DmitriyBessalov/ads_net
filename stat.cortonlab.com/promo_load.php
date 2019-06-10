<?php
header('Access-Control-Allow-Origin: *');
$_GET = array_map('addslashes', $_GET);

$prosmort_id=(int)$_GET['prosmort_id'];
if (($prosmort_id==0) OR ($_GET['ref']=="") OR (!isset($_GET['anons_id'])) OR ($_GET['anons_id']==""))exit;

$redis = new Redis();
$redis->pconnect('185.75.90.54', 6379);
$redis->select(4);
$block=$redis->get('l:'.$_GET['prosmort_id']);
if ($block){$redis->set('l:'.$prosmort_id, 1, 1296000);exit;}else{$redis->set('l:'.$prosmort_id, 1, 1296000);}
$redis->close();

$domen=parse_url($_SERVER['HTTP_ORIGIN'], PHP_URL_HOST);

require_once('/var/www/www-root/data/db.php');

$sql= "SELECT `id` FROM `ploshadki` WHERE `domen`='".$domen."'";
$ploshadka_id = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);

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
$GLOBALS['dbstat']->query($sql);

$sql = "UPDATE `stat_promo_day_count` SET `perehod` = `perehod` + 1 WHERE `data`=CURDATE() AND `anons_id`='".$_GET['anons_id']."' AND `promo_variant`='".$_GET['p_id']."'";
if (!$GLOBALS['dbstat']->exec($sql)){$GLOBALS['dbstat']->query("INSERT INTO `stat_promo_day_count` SET `anons_id` = '".$_GET['anons_id']."', `data` = CURDATE(), `promo_variant`='".$_GET['p_id']."', `perehod` = 1");
}

$sql = "UPDATE `balans_ploshadki` SET `".$_GET['t']."_promo_load`=`".$_GET['t']."_promo_load`+1  WHERE `date`=CURDATE() AND `ploshadka_id`='".$ploshadka_id."'";
if (!$GLOBALS['dbstat']->exec($sql)){
    $sql = "INSERT INTO `balans_ploshadki` SET `".$_GET['t']."_promo_load`=1, `date`=CURDATE(), `ploshadka_id`='".$ploshadka_id."'";
    $GLOBALS['dbstat']->query($sql);
}

/* Структура Redis
 * db0 id_Просмотра
 * db1 Счетчики показа анонсов по id анонса за день
 * db2 block_ip
 * db3 Счетчики показа анонсов по площадкам за день
 * db4 Блокировка по просморт ID
 */

