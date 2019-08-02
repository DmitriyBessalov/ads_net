<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json;');

$interes = addslashes(implode("','",$_GET['c']));
$_GET = array_map('addslashes', $_GET);


require_once('/var/www/www-root/data/www/api2.cortonlab.com/geoip/SxGeo.php');
require_once('/var/www/www-root/data/www/panel.cortonlab.com/config/db.php');

$words=str_replace(',', '\',\'', $_GET['words']);

function get_anons($iso, $interes, $words, $block_promo_id)
{
    switch (strlen($iso)){
        case 0:
            $sql = "SELECT promo_id FROM promo_category WHERE category_id IN ('" . $interes . "')";
            $sql2 = "SELECT promo_ids FROM words_index WHERE word IN ('".$words."')";
            break;
        case 2:
            $arr['region'] = "ALL','" . $iso;
            $sql = "SELECT c.`promo_id` FROM `promo_category` c JOIN `promo` p WHERE (FIND_IN_SET('" . $iso . "', p.`region`) OR FIND_IN_SET('ALL', p.`region`)) AND c.`category_id` IN ('" . $interes . "') GROUP BY c.`promo_id`";
            $sql2="SELECT `promo_ids` FROM `words_index` WHERE `word` IN ('".$words."') AND `region` IN ('".$arr['region']."')";
            break;
        default:
            $county = substr($iso, 0, 2);
            $arr['region'] = "ALL','" . $county . "','" . $iso;
            $sql = "SELECT c.`promo_id` FROM `promo_category` c JOIN `promo` p WHERE (FIND_IN_SET('" . $county . "', p.`region`) OR FIND_IN_SET('" . $iso . "', p.`region`) OR FIND_IN_SET('ALL', p.`region`)) AND c.`category_id` IN ('" . $interes . "') GROUP BY c.`promo_id`";
            $sql2="SELECT `promo_ids` FROM `words_index` WHERE `word` IN ('".$words."') AND `region` IN ('".$arr['region']."')";
    }

    $result0 = $GLOBALS['db']->query($sql)->fetchALL(PDO::FETCH_COLUMN);
    $result1 = $GLOBALS['db']->query($sql2)->fetchALL(PDO::FETCH_COLUMN);
    $promo_ids = array();

    foreach ($result0 as $i) {
        $promo_ids = array_merge($promo_ids, explode(',', $i));
    };

    foreach ($result1 as $i) {
        $promo_ids=array_merge($promo_ids, explode(',',$i));
    };

    $promo_ids=array_unique($promo_ids);

    # Поиск статей где обязательное обязательное совпадение по ключу и площадке
    if (count($promo_ids)){
        $ids = implode("','", $promo_ids);
        $sql="SELECT `id` FROM `promo` WHERE `id` IN ('".$ids."') AND `merge_key_and_categor`=1";
        $result2 = $GLOBALS['db']->query($sql)->fetchALL(PDO::FETCH_COLUMN);
        foreach ($result2 as $i){
            if((in_array($i,$result0)) xor (in_array($i,$result1)))
            {
                $block_promo_id[]=$i;
            }
        }
    }

    # Очистка от повторов promo_id
    foreach ($block_promo_id as $i){
        if(($key = array_search($i,$promo_ids)) !== FALSE){
            unset($promo_ids[$key]);
        }
    }

    //Берем ID Анонсов
    $promo=implode("','" , $promo_ids);
    $sql="SELECT promo_id, anons_ids, stavka
            FROM anons_index WHERE promo_id IN ('".$promo."')
            ORDER BY stavka DESC, RAND()";
    $result2 = $GLOBALS['db']->query($sql)->fetchALL(PDO::FETCH_ASSOC);

    $anons_ids = array();
    $y=0;

    $anons_count=0;
    //Премешивание анонсов внутри статьи
    foreach ($result2 as $i) {
        if ($i['anons_ids']==''){
            unset($result2[$y]);
        }else{
            $f = explode(',',$i['anons_ids']);
            shuffle($f);
            //$anons_ids=array_merge($anons_ids, $f);
            $result2[$y]['an_count']=count($f);
            $anons_count=$anons_count+count($f);
            $result2[$y]['an']=$f;
            $y++;
        };
    };

    $ch = $ch2 = 0;
    $count = count($result2);
    while ($anons_count != 0) {
        if ($result2[$ch]['an_count'] > 0) {
            $an[] = (int)$result2[$ch]['an'][$ch2];
            $result2[$ch]['an_count']--;
            $anons_count--;
        }
        $ch++;
        if ($ch == $count) {
            $ch = 0;
            $ch2++;
        }
    }

    $result2['a']=$an;

    return $result2;
}
$arr['region']=$iso;
$count_widgets=$_GET['e']+$_GET['r']+$_GET['s'];
# условие если есть виджеты c гео
$block_promo_id=array();
$arr['anons_count']=0;
if ($iso != ""){
    $result2=get_anons($iso,$interes,$words,$block_promo_id);
    $arr['anons_count'] = count($result2['a']);
}
# условие при недостатке виджетов берем без
if ($count_widgets>$arr['anons_count']){
    $iso = '';
    foreach ($result2 as $i){
        if (isset($i['promo_id']))
            $block_promo_id[]=$i['promo_id'];
    }
    $result3=get_anons($iso,$interes,$words,$block_promo_id);
}

if (!isset($result2['a'])){
    $result2['a']=array();
};
if (!isset($result3['a'])){
    $result3['a']=array();
};

$anons_all=array_merge($result2['a'], $result3['a']);

if (count($anons_all)==0){
    $arr['anons_count']=0;
    $show=0;
} else{
    $anons_all = array_slice($anons_all, 0, $count_widgets);

    $ann = implode("','", $anons_all);

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