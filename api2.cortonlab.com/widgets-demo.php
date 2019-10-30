<?php
exit;


header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json;');
$_GET = array_map('addslashes', $_GET);
require_once('/var/www/www-root/data/www/panel.cortonlab.com/config/db.php');preg_match('/host=(.*?);/', $_GET['host'], $referer);
preg_match('/scheme=(.*?);/', $_GET['sheme'], $referer2);
$sql="SELECT `id`,`demo-annons` FROM `ploshadki` WHERE `domen`='".$referer[1]."';";
$anons = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);

preg_match_all('/\d{1,6}/',$anons['demo-annons'],$matches);
$annons=implode("','",$matches[0]);

$arr['anons_count']=(int)$_GET['r']+(int)$_GET['e']+(int)$_GET['s'];

$sql="SELECT * FROM `anons` WHERE `id` IN ('".$annons."') LIMIT ".$arr['anons_count'];
$result = $GLOBALS['db']->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$i=$ch=0;
while (true) {
    if ($i==$arr['anons_count'])break;
    shuffle($result);
    $ch=0;
    foreach ($result as $y) {
        if ($i==$arr['anons_count'])break;
        $arr['anons'][0][] = $result[$ch]['id'];
        $arr['anons'][1][] = $result[$ch]['title'];
        $arr['anons'][2][] = $result[$ch]['snippet'];
        $arr['anons'][3][] = $result[$ch]['img_290x180'];
        $arr['anons'][4][] = $result[$ch]['img_180x180'];
        $arr['anons'][5][] = $result[$ch]['user_id'];
        $ch++;
        $i++;
    }
}

$redis = new Redis();
$redis->pconnect('185.75.90.54', 6379);
$arr['prosmotr_id'] = $redis->incr("prosmotr_id");

$arr['promo_page']='/promo';

$arr['platform_id'] = $anons['id'];
$str= json_encode($arr,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
echo $str;
