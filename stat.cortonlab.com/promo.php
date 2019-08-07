<?php
header('Access-Control-Allow-Origin: *');
$_GET = array_map('addslashes', $_GET);

# Проверка входящих параметров
if (($_GET['t']!='e') XOR ($_GET['t']!='s') XOR ($_GET['t']!='r')){
    exit;
}

$widget=$_GET['t'];
#  r: recomend
#  e: natpre
#  s: slider

$action=$_GET['a'];
#  l: загрузка
#  s: чтение
#  r: дочитывание
#  c: клик с промо статьи

$prosmort_id=(int)$_GET['prosmort_id'];
$anons_id=(int)$_GET['anons_id'];
if (($anons_id==0) OR ($prosmort_id==0)) exit;

# Защита от повторных запросов
$redis = new Redis();
$redis->pconnect('185.75.90.54', 6379);
$redis->select(4);
$block=$redis->get($action.':'.$_GET['prosmort_id']);
$redis->set($action.':'.$prosmort_id, 1, 1296000);
if (($action=='s') and (!$block))
    $block=$redis->get('r:'.$prosmort_id, 1, 1296000);
if ($block){
    exit;
}

# Подключение к базе
require_once('/var/www/www-root/data/www/panel.cortonlab.com/config/db.php');

# Берём id площадки
$domen = parse_url($_SERVER['HTTP_ORIGIN'], PHP_URL_HOST);
//$domen='okardio.com';

$sql= "SELECT `id`,`otchiclen`,`user_id` FROM `ploshadki` WHERE `domen`='".$domen."'";
$platform = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);

# Берем ставку
if(($action =='s')or($action =='r')) {
    $sql= "SELECT 
               n.stavka,
               n.`persent_platform`,
               a.promo_id
           FROM anons a RIGHT OUTER JOIN anons_index n ON a.promo_id = n.promo_id
           WHERE a.id='".$anons_id."'";
    $promo = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);

    # Изменение баланса рекламодателя (настройка на странице площадок)
    $stavka_advertiser=round($promo['stavka']*$platform['otchiclen']/100,2);
    # Изменение баланса площадки (настройка на странице таргетингов статьи)
    $stavka_ploshadka=round($stavka_advertiser*$promo['persent_platform']/100,2);
}


# Обновляет информацию по переходу на статью
if ($action =='l') {
    if ($_GET['ref']=="") exit;
    $sql = "INSERT INTO 
        `stat_promo_prosmotr`
    SET
        `date` = '" . date('Y-m-d') . "',
        `ploshadka_id` = " . $platform['id'] . ",
        `prosmotr_id` = '" . $prosmort_id . "',
        `anon_id` = '" . $anons_id . "',
        `url_ref` = '" . $_GET['ref'] . "',
        `ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
        `tizer` = '" . $widget . "',
        `user-agent`= '" . $_SERVER['HTTP_USER_AGENT'] . "',
        `timestamp` = '" . date('H:i:s') . "'";
}else{
    # Узнаём оплачивался ли данный просмотр
    $sql = "SELECT `pay`,`read` FROM `stat_promo_prosmotr` WHERE `prosmotr_id` = '".$_GET['prosmort_id']."'";
    $pay = $GLOBALS['dbstat']->query($sql)->fetch(PDO::FETCH_ASSOC);
    if ($pay==false){
        exit;
    }

    if($action =='s'){
        $sql = "UPDATE `stat_promo_prosmotr` SET `pay` = '".$stavka_advertiser."' WHERE  `prosmotr_id` = '".$_GET['prosmort_id']."'";
    }elseif($action =='r'){
        $sql = "UPDATE `stat_promo_prosmotr` SET `pay` = '".$stavka_advertiser."', `read` = '1' WHERE `prosmotr_id` = '" . $_GET['prosmort_id'] . "'";
    }elseif($action =='c'){
        $sql = "UPDATE `stat_promo_prosmotr` SET `click` = '1' WHERE `prosmotr_id` = '" . $_GET['prosmort_id'] . "'";
    }else{
        exit;
    }
}
$GLOBALS['dbstat']->query($sql);

# Антифрод
if(($action =='s')or($action =='r')) {
    $redis->select(2);
    $block_ip=$redis->get($platform['id'].':'.$_SERVER['REMOTE_ADDR']);
    if ($block_ip) {
        $redis->set($platform['id'].':'.$_SERVER['REMOTE_ADDR'], 1, 1296000);
        $antifrod=1;
    }else{
        $redis->set($platform['id'].':'.$_SERVER['REMOTE_ADDR'], 1, 86400);
    }
}
$redis->close();

# Обновлеем информацию по статье с анонсами
if(!isset($antifrod)){
    switch ($action){
        case 'l':
            $sql = "UPDATE `stat_promo_day_count` SET `perehod` = `perehod` + 1 WHERE `data`=CURDATE() AND `anons_id`='".$_GET['anons_id']."' AND `promo_variant`='".$_GET['p_id']."'";
            if (!$GLOBALS['dbstat']->exec($sql)){
                $GLOBALS['dbstat']->query("INSERT INTO `stat_promo_day_count` SET `anons_id` = '".$_GET['anons_id']."', `data` = CURDATE(), `promo_variant`='".$_GET['p_id']."', `perehod` = 1");
            }
            break;
        case 's':
            if ($pay['pay']==0) {
                $sql = "UPDATE `stat_promo_day_count` SET `st` = `st` + 1, `pay` = `pay` + " . $stavka_advertiser . "  WHERE `data`=CURDATE() AND `anons_id`='" . $_GET['anons_id'] . "' AND `promo_variant`='" . $_GET['p_id'] . "'";
                if (!$GLOBALS['dbstat']->exec($sql)){
                    $sql = "INSERT INTO `stat_promo_day_count` SET `anons_id` = '".$_GET['anons_id']."', `data` = CURDATE(), `promo_variant`='".$_GET['p_id']."', `st` = 1, `pay` = ".$stavka_advertiser;
                    $GLOBALS['dbstat']->query($sql);
                }
            }
            break;
        case 'r':
            if ($pay['pay']==0) {
                $sql = "UPDATE `stat_promo_day_count` SET `reading` = `reading` + 1, `st` = `st` + 1, `pay` = `pay` + " . $stavka_advertiser . " WHERE `data`=CURDATE() AND `anons_id`='" . $_GET['anons_id'] . "' AND `promo_variant`='" . $_GET['p_id'] . "'";
            }else{
                $sql = "UPDATE `stat_promo_day_count` SET `reading` = `reading` + 1 WHERE `data`=CURDATE() AND `anons_id`='" . $_GET['anons_id'] . "' AND `promo_variant`='" . $_GET['p_id'] . "'";
            }
            if (!$GLOBALS['dbstat']->exec($sql)){
                $sql = "INSERT INTO `stat_promo_day_count` SET `anons_id` = '".$_GET['anons_id']."', `data` = CURDATE(), `promo_variant`='".$_GET['p_id']."', `st` = 1, `reading` = 1, `pay` = ".$stavka_advertiser;
                $GLOBALS['dbstat']->query($sql);
            }
            break;
        case 'c':
            $sql = "UPDATE `stat_promo_day_count` SET `clicking` = `clicking` + 1 WHERE `data`=CURDATE() AND `anons_id`='".$_GET['anons_id']."' AND `promo_variant`='".$_GET['p_id']."'";
            if (!$GLOBALS['dbstat']->exec($sql)){
                $GLOBALS['dbstat']->query("INSERT INTO `stat_promo_day_count` SET `anons_id` = '".$_GET['anons_id']."', `promo_variant`='".$_GET['p_id']."', `data` = CURDATE(), `clicking` = 1");
            }
    }
}elseif ($action=='r') {
    if ($pay['read']==0){
        $sql = "UPDATE `stat_promo_day_count` SET `reading` = `reading` + 1 WHERE `data`=CURDATE() AND `anons_id`='" . $_GET['anons_id'] . "' AND `promo_variant`='" . $_GET['p_id'] . "'";
        $GLOBALS['dbstat']->query($sql);
    }
    exit;
}

# Обновляем статистику по площадке
switch ($action) {
    case 'l':
        $sql = "UPDATE `balans_ploshadki` SET `".$widget."_promo_load`=`".$widget."_promo_load`+1 WHERE `date`=CURDATE() AND `ploshadka_id`='".$platform['id']."'";
        if (!$GLOBALS['dbstat']->exec($sql)){
            $sql = "INSERT INTO `balans_ploshadki` SET `".$widget."_promo_load`=1, `date`=CURDATE(), `ploshadka_id`='".$platform['id']."'";
            $GLOBALS['dbstat']->query($sql);
        }
        break;
    case 's':
    case 'r':
        if ($pay['pay']!=0) {
            $stavka=0;
        }
        $sql = "UPDATE `balans_ploshadki` SET `".$widget."`=".$widget."+1, `".$widget."_balans`=".$widget."_balans+".$stavka_ploshadka."  WHERE `date`=CURDATE() AND `ploshadka_id`='".$platform['id']."'";
        if (!$GLOBALS['dbstat']->exec($sql)){
            $sql = "INSERT INTO `balans_ploshadki` SET `ploshadka_id` = '".$platform['id']."', `date` = CURDATE(), `".$widget."`=".$widget."+1, `".$widget."_balans`=".$widget."_balans+".$stavka_ploshadka;
            $GLOBALS['dbstat']->query($sql);
        }
        break;
    case 'с':
        $sql ="UPDATE `balans_ploshadki` SET `".$widget."_promo_click` = `".$widget."_promo_click` + 1 WHERE `ploshadka_id`='".$platform['id']."' AND `date`=CURDATE();";
        $GLOBALS['dbstat']->query($sql);


        # Добавление статистики пререходов со статей
       /* $sql ="INSERT INTO `promo_perehod`
                SET `promo_id` = [ VALUE -2 ],
                    `date` = CURDATE(),
                    `numlink` = [ VALUE -4 ],
                    `ancor` = [ VALUE -5 ],
                    `href` = [ VALUE -6 ],
                    `count` = 1";
        if (!$GLOBALS['dbstat']->exec($sql)){
            $sql ="UPDATE `promo_perehod` 
                   SET `count` =`count` + 1
                   WHERE `promo_id` = [ VALUE -2 ],
                         `date` = CURDATE(),
                         `numlink` = [ VALUE -4 ],
                         `ancor` = [ VALUE -5 ],
                         `href` = [ VALUE -6 ]";
            $GLOBALS['dbstat']->query($sql);
        }
*/
}


if((($action =='s')or($action =='r')) and ($pay['pay']==0)) {
    # Изменение баланса плошадки
    $sql = "UPDATE `balans_user` SET `balans` = `balans` + " . $stavka_ploshadka . " WHERE `date`=CURDATE() AND `user_id`='" . $platform['user_id'] . "'";
    if (!$GLOBALS['db']->exec($sql)) {
        $sql = "SELECT `balans` FROM `balans_user` WHERE `user_id` = '" . $platform['user_id'] . "' AND `date` =(SELECT MAX(`date`) FROM `balans_user` WHERE `user_id` = '" . $platform['user_id'] . "')";
        $oldbalans = $stavka_ploshadka + $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);

        $sql = "INSERT INTO `balans_user` SET `user_id` = '" . $platform['user_id'] . "', `date` = CURDATE(), `balans` = " . $oldbalans;
        $GLOBALS['db']->query($sql);
    }

    # Изменение баланса рекламодателя
    $sql = "SELECT `id_user_advertiser` FROM `promo` WHERE `id`=" . $promo['promo_id'] . ";";
    $id_user_advertiser = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);

    $sql = "UPDATE `balans_rekl` SET `balans` = `balans` - " . $stavka_advertiser . " WHERE `date`=CURDATE() AND `user_id`='" . $id_user_advertiser . "';";
    if (!$GLOBALS['db']->exec($sql)) {
        $sql = "SELECT `balans` FROM `balans_rekl` WHERE `user_id` = '" . $id_user_advertiser . "' AND `date` =(SELECT MAX(`date`) FROM `balans_rekl` WHERE `user_id` = '" . $id_user_advertiser . "')";
        $oldbalans = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
        $oldbalans = $oldbalans - $stavka_advertiser;

        $sql = "INSERT INTO `balans_rekl` SET `user_id` = '" . $id_user_advertiser . "', `date` = CURDATE(), `balans` = " . $oldbalans;
        $GLOBALS['db']->query($sql);
    }
}






