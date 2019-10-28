<?php
header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
header("Access-Control-Allow-Credentials: true");
require_once('/var/www/www-root/data/www/panel.cortonlab.com/config/db.php');
require_once('/var/www/www-root/data/www/stat.cortonlab.com/postgres.php');
$stat_arr['is_show_preview']=1;

$domen=parse_url ( $_SERVER['HTTP_ORIGIN'], PHP_URL_HOST );

$sql= "SELECT `id`,`otchiclen`,`user_id`,`model_pay`,`CPM_stavka` FROM `ploshadki` WHERE `domen`='".$domen."'";
$platform = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);

$stat_arr['view_id']=addslashes($_GET['prosmort_id']);

$stat_arr['platform_id']=$ploshadka_id = $platform['id'];
if (!$platform){
    $stat_arr['is_baned']=1;
    $stat_arr['preview_id_list']=addslashes($_GET['anons_ids']);
    statpostgres($stat_arr);
    exit;
};

$redis = new Redis();
$redis->pconnect('185.75.90.54', 6379);
$redis->select(1);
$arr=explode(',',addslashes($_GET['anons_ids']));

foreach ($arr as $value) {
    $value=substr($value, 0, -1);
    $redis->incr(date('d').':'.$value);
    $stat_arr2['preview_id_list'][]=$value;
};
$stat_arr['preview_id_list']=implode(',',$stat_arr2['preview_id_list']);

$redis->select(3);
$valueold="";

$stat_arr['recomend']=(int)0;

$platform['CPM_stavka']=$platform['CPM_stavka']/1000;

function cpm($platform){
    # Модель оплаты за показы виджетов
    if($platform['model_pay']=='CPM') {
        $sql = "UPDATE `balans_user` SET `balans` = `balans` + " . $platform['CPM_stavka'] . ", `CPM`= `CPM` + " . $platform['CPM_stavka'] . "  WHERE `date`=CURDATE() AND `user_id`='" . $platform['user_id'] . "'";
    }else{
        $sql = "UPDATE `balans_user` SET `CPM`= `CPM` + " . $platform['CPM_stavka'] . "  WHERE `date`=CURDATE() AND `user_id`='" . $platform['user_id'] . "'";
    }
    if (!$GLOBALS['db']->exec($sql)) {
        $sql = "SELECT `balans` FROM `balans_user` WHERE `user_id` = '" . $platform['user_id'] . "' AND `date` =(SELECT MAX(`date`) FROM `balans_user` WHERE `user_id` = '" . $platform['user_id'] . "')";
        if($platform['model_pay']=='CPM') {
            $oldbalans = $platform['CPM_stavka'] + $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
        }else{
            $oldbalans = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
        }
        $sql = "INSERT INTO `balans_user` SET `user_id` = '" . $platform['user_id'] . "', `date` = CURDATE(), `balans` = '".$oldbalans."',`CPM`='" . $platform['CPM_stavka'] . "'";
        $GLOBALS['db']->query($sql);
    }

    $sql = "UPDATE `balans_ploshadki` SET `cpm`= `cpm` + " . $platform['CPM_stavka'] . "  WHERE `date`=CURDATE() AND `ploshadka_id`='" . $platform['id'] . "'";
    if (!$GLOBALS['dbstat']->exec($sql)) {
        $sql = "INSERT INTO `balans_ploshadki` SET `ploshadka_id` = '" . $platform['id'] . "', `date` = CURDATE(), `cpm`=  '" . $platform['CPM_stavka'] . "'";
        $GLOBALS['dbstat']->query($sql);
    }
};

foreach ($arr as $value) {
    $value=substr($value, -1);
    if ($value=='e'){
        $stat_arr['native']=1;
        cpm($platform);
    }
    if ($value=='r'){
        $stat_arr['recomend']++;
    }

    $redis->incr(date('d').':'.$ploshadka_id.':'.$value);
    if (($value!=$valueold) and ($value=='r')){
        $sql= "INSERT INTO `widget_prosmotr`(`prosmotr_id`, `ploshadka_id`, `date`) VALUES ('".addslashes($_GET['prosmort_id'])."','".$ploshadka_id."',CURDATE())";
        $GLOBALS['dbstat']->query($sql);
        $valueold='r';
    }
};
$redis->close();



if ($_GET['f']==1){
    cpm($platform);
}

statpostgres($stat_arr);