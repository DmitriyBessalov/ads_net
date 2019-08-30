<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json;');

$interes = addslashes(implode("','",$_GET['c']));
$_GET = array_map('addslashes', $_GET);

#Определение geo
require_once 'geoip/vendor/autoload.php';
use GeoIp2\Database\Reader;
$reader = new Reader('/var/www/www-root/data/www/api2.cortonlab.com/geoip/GeoLite2-City.mmdb');

//$_SERVER['REMOTE_ADDR']='185.75.90.54';

$record = $reader->city($_SERVER['REMOTE_ADDR']);
if ($record->mostSpecificSubdivision->isoCode==''){
    $arr['region']=$iso=$record->country->isoCode;
}else{
    $arr['region']=$iso=$record->country->isoCode.'-'.$record->mostSpecificSubdivision->isoCode;
}

require_once('/var/www/www-root/data/www/panel.cortonlab.com/config/db.php');

$words=str_replace(',', '\',\'', $_GET['words']);
$count_widgets=$_GET['e']+$_GET['r']+$_GET['s'];

if (strlen($iso)==2) {
    $sql = "SELECT c.`promo_id` FROM `promo_category` c JOIN `promo` p ON c.`promo_id`=p.`id` WHERE (FIND_IN_SET('" . $iso . "', p.`region`)) AND c.`category_id` IN ('" . $interes . "') AND p.`active`=1 GROUP BY c.`promo_id`";
    $sql2 = "SELECT `promo_ids` FROM `words_index` WHERE `word` IN ('" . $words . "') AND `region`='" . $iso . "'";
}else{
    $county = substr($iso, 0, 2);
    $sql = "SELECT c.`promo_id` FROM `promo_category` c JOIN `promo` p ON c.`promo_id`=p.`id` WHERE (FIND_IN_SET('" . $county . "', p.`region`) OR FIND_IN_SET('" . $iso . "', p.`region`)) AND c.`category_id` AND p.`active`=1 IN ('" . $interes . "') GROUP BY c.`promo_id`";
    $sql2="SELECT `promo_ids` FROM `words_index` WHERE `word` IN ('".$words."') AND `region` IN ('".$county . "','" . $iso."')";
}

$result0 = $GLOBALS['db']->query($sql)->fetchALL(PDO::FETCH_COLUMN);

$result1 = $GLOBALS['db']->query($sql2)->fetchALL(PDO::FETCH_COLUMN);
$result2 = array();
foreach ($result1 as $i) {
    $result2 = array_merge($result2, explode(',',$i));
};

$promo_ids=array_unique(array_merge($result0, $result2));

# Фильтр статей где обязательное обязательное совпадение по ключу и категория
if (count($promo_ids)){
    $ids = implode("','", $promo_ids);
    $sql="SELECT `id` FROM `promo` WHERE `id` IN ('".$ids."') AND `merge_key_and_categor`=1";
    $result3 = $GLOBALS['db']->query($sql)->fetchALL(PDO::FETCH_COLUMN);
    foreach ($result3 as $i){
        if((in_array($i,$result0)) xor (in_array($i,$result2)))
        {
            $key = array_search($i,$promo_ids);
            unset($promo_ids[$key]);
        }
    }
}

//Берем ID Анонсов
$promo=implode("','" , $promo_ids);
$sql="SELECT promo_id, anons_ids, stavka
        FROM anons_index WHERE promo_id IN ('".$promo."')
        ORDER BY stavka DESC, RAND()";
$anons_all = $GLOBALS['db']->query($sql)->fetchALL(PDO::FETCH_ASSOC);

$anons_ids = array();
$y=0;

$anons_count=0;
//Премешивание анонсов внутри статьи
foreach ($anons_all as $i) {
    if ($i['anons_ids']==''){
        unset($anons_all[$y]);
    }else{
        $f = explode(',',$i['anons_ids']);
        shuffle($f);
        //$anons_ids=array_merge($anons_ids, $f);
        $anons_all[$y]['an_count']=count($f);
        $anons_count=$anons_count+count($f);
        $anons_all[$y]['an']=$f;
        $y++;
    };
};

$ch = $ch2 = 0;
$count = count($anons_all);
while ($anons_count != 0) {
    if ($anons_all[$ch]['an_count'] > 0) {
        $an[] = (int)$anons_all[$ch]['an'][$ch2];
        $anons_all[$ch]['an_count']--;
        $anons_count--;
    }
    $ch++;
    if ($ch == $count) {
        $ch = 0;
        $ch2++;
    }
}


$arr['anons_count'] = count($an);

if (count($an)==0){
    $show=0;
} else{
    $an = array_slice($an, 0, $count_widgets);

    $ann = implode("','", $an);

    $sql = "SELECT * FROM `anons` WHERE `id` IN ('" . $ann . "')";
    $result = $GLOBALS['db']->query($sql)->fetchALL(PDO::FETCH_ASSOC);

    while ($count_widgets>count($result))
        $result=array_merge($result, $result);

    shuffle($result);
    $result = array_slice($result, 0, $count_widgets);

    $arr['anons_count']=$count_widgets;

    $ch = 0;
    foreach ($result as $i) {
        $arr['anons'][0][] = $result[$ch]['id'];
        $arr['anons'][1][] = $result[$ch]['title'];
        $arr['anons'][2][] = $result[$ch]['snippet'];
        $arr['anons'][3][] = $result[$ch]['img_290x180'];
        $arr['anons'][4][] = $result[$ch]['img_180x180'];
        $arr['anons'][5][] = $result[$ch]['user_id'];
        $ch++;
    }
    $show=1;
}

preg_match('/\/\/(.*?)\//', $_SERVER['HTTP_REFERER'], $referer);
$sql="SELECT `id`,`promo_page` FROM `ploshadki` WHERE `domen`='".$referer[1]."'";
$result1 = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);
$arr['p_id'] = $result1['id'];
$arr['promo_page']=$result1['promo_page'];

$redis = new Redis();
$redis->pconnect('185.75.90.54', 6379);
$arr['prosmotr_id'] = $redis->incr("prosmotr_id");

echo json_encode($arr,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

//Cбор статистики слов
$word_arr=explode("','",$words);

foreach ($word_arr as $i){
    $sql = "UPDATE `words` SET `count`=`count`+1 WHERE `platform_id`='".$arr['p_id']."' AND `word`='".$i."'";
    if (!$GLOBALS['dbstat']->exec($sql)){
        $sql = "INSERT INTO `words` SET `platform_id`='".$arr['p_id']."', `word`='".$i."', `count`='1'";
        $GLOBALS['dbstat']->query($sql);
    }
}

$i=parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);

$sql = "UPDATE `words_top10` SET `count`=`count`+1   WHERE `platform_id`='".$arr['p_id']."' AND `uri`='".$i."'";
if (!$GLOBALS['dbstat']->exec($sql)) {
    $sql = "INSERT INTO `words_top10` SET `platform_id`='" . $arr['p_id'] . "', `uri`='".$i."',  `top10`='" . $_GET['words'] . "', `wdget_show`='".$show."',`count`='1'";
    $GLOBALS['dbstat']->query($sql);
}

#$GLOBALS['dbstat']->query("INSERT INTO `geo-stat`(`ip`, `iso`) VALUES ('".$_SERVER['REMOTE_ADDR']."','".$iso."')");
