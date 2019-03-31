<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json;');

$db = new PDO("mysql:host=185.75.90.54;dbname=corton", 'www-root', 'Do5aemub0e7893', array(PDO::ATTR_PERSISTENT => true));
$y=0;
$words=str_replace(',', '\',\'', addslashes($_GET['words']));
//Найдем ID статей
$sql="SELECT `promo_ids` FROM `words_index` WHERE `word` IN ('".$words."')";
$result = $db->query($sql)->fetchALL(PDO::FETCH_COLUMN);
$promo_ids = array();
foreach ($result as $i) {
    $promo_ids=array_merge($promo_ids, explode(',',$i));
};
$promo_ids=array_unique($promo_ids);
//Берем ID Анонсов
$promo=implode("','" ,$promo_ids);
$sql="SELECT * FROM `anons_index` WHERE `promo_id` IN ('".$promo."')";
$result2 = $db->query($sql)->fetchALL(PDO::FETCH_ASSOC);
$anons_ids = array();
//Премешивание анонсов внутри статьи
foreach ($result2 as $i) {
    if ($i['anons_ids']==''){
        unset($result2[$y]);
    }else{
        $f=explode(',',$i['anons_ids']);
        shuffle($f);
        $anons_ids=array_merge($anons_ids, $f);
        $result2[$y]['an_count']=count($f);
        $result2[$y]['an']=$f;
        $y++;
    };
};
$count=count($anons_ids);

preg_match('/\/\/(.*?)\//', $_SERVER['HTTP_REFERER'], $referer);

$sql="SELECT `id`,`promo_page`,`recomend_zag_aktiv`,`natpre_zag_aktiv` FROM `ploshadki` WHERE `domen`='".$referer[1]."'";
$result1 = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
$arr['platform_id'] = $result1['id'];

//Алгорим расчета вероятности выдачи анонса
$anons=0;
if ($count==0){
    //анонсы отсутствуют, вывести заглушки
    if (($result1['recomend_zag_aktiv'])AND(isset($_GET['r']))){
        $arr['recomend_zag']=1;
    }
    if (($result1['natpre_zag_aktiv'])AND(isset($_GET['e']))){
        $arr['natpre_zag']=1;
    }
} else {
    $ch=$count_widgets=$_GET['e']+$_GET['r']+$_GET['s'];
    $d=$count_widgets/$count;
    if ($d>1){
        //недостаток виджетов
        if ($count>=(int)$_GET['r']){
            $anons=(int)$_GET['r'];
            $count=$count-(int)$_GET['r'];
        }else{
            if (($result1['natpre_zag_aktiv'])AND(isset($_GET['r']))){
                $arr['recomend_zag']=1;
            }
        }

        if ($count!=0){
            $count_widgets=$count_widgets-1;
            $anons++;$count--;
        }else{
            if ($result1['natpre_zag_aktiv']) {
                $arr['natpre_zag'] = 1;
            }
        }

        if ($count!=0){
            $count_widgets=$count_widgets-1;
            $anons++;
        }
    } else
    if ($d==1){
        //Вывести каждый виджет по 1 разу
        $anons=$count;
    } else
    if ($d<1) {
        //Вывеси самые дорогие
        $anons=$count_widgets;
    }
}

$arr['anons_count']=$anons;
if ($arr['anons_count']!=0) {
    $arr['promo_page']=$result1['promo_page'];
    shuffle($result2);
    //Расчет приоритета выдачи анонса на основе ставки
    usort($result2, function ($a, $b) {
        return $b['stavka'] <=> $a['stavka'];
    });

    $ch = $ch2 = 0;
    $count = count($result2);
    while ($anons != 0) {
        if ($result2[$ch]['an_count'] > 0) {
            $an[] = (int)$result2[$ch]['an'][$ch2];
            $result2[$ch]['an_count']--;
            $anons--;
        }
        $ch++;
        if ($ch == $count) {
            $ch = 0;
            $ch2++;
        }
    }

    $ann = implode("','", $an);
    $sql = "SELECT * FROM `anons` WHERE `id` IN ('" . $ann . "')";
    $result = $db->query($sql)->fetchALL(PDO::FETCH_ASSOC);
    $ch = 0;

    shuffle($result);
    foreach ($result as $i) {
        $arr['anons'][0][] = $result[$ch]['id'];
        $arr['anons'][1][] = $result[$ch]['title'];
        $arr['anons'][2][] = $result[$ch]['snippet'];
        $arr['anons'][3][] = $result[$ch]['img_290x180'];
        $arr['anons'][4][] = $result[$ch]['img_180x180'];
        $arr['anons'][5][] = $result[$ch]['user_id'];
        $ch++;
    }

    $redis = new Redis();
    $redis->connect('185.75.90.54', 6379);
    $arr['prosmotr_id'] = $redis->incr("prosmotr_id");
}
$str= json_encode($arr,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
echo $str;
