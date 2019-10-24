<?php
header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
header("Access-Control-Allow-Credentials: true");
$_GET = array_map('addslashes', $_GET);
require_once('/var/www/www-root/data/www/stat.cortonlab.com/postgres.php');

$widget=$_GET['t'];
#  r: recomend
#  e: natpre
#  s: slider

$action=$_GET['a'];
#  l: загрузка
#  s: чтение
#  r: дочитывание
#  c: клик с промо статьи

switch ($action){
    case 's':$stat_arr['is_read_post']=1;break;
    case 'r':$stat_arr['is_total_read_post']=1;break;
    case 'c':$stat_arr['redirect_link']=-2;
}

$stat_arr['view_id']=$prosmort_id=(int)$_GET['prosmort_id'];
$stat_arr['preview_id_list']=$anons_id=(int)$_GET['anons_id'];
if ($_GET['t']=='r') $stat_arr['recomend']=1;
if ($_GET['t']=='e') $stat_arr['native']=1;

# Проверка входящих параметров
if (!(($_GET['t']=='e') OR ($_GET['t']=='s') OR ($_GET['t']=='r'))){
    $stat_arr['is_baned']=1;
    statpostgres($stat_arr);
    exit;
}

if (($anons_id==0) OR ($prosmort_id==0)) {
    $stat_arr['is_baned']=1;
    statpostgres($stat_arr);
    exit;
}

# Защита от повторных запросов
$redis = new Redis();
$redis->pconnect('185.75.90.54', 6379);
$redis->select(4);
$block=$redis->get($action.':'.$_GET['prosmort_id']);
$redis->set($action.':'.$prosmort_id, 1, 1296000);
if (($action=='s') and (!$block))
    $block=$redis->get('r:'.$prosmort_id, 1, 1296000);
if ($block){
    $stat_arr['is_baned']=1;
    statpostgres($stat_arr);
    exit;
}

# Подключение к базе
require_once('/var/www/www-root/data/www/panel.cortonlab.com/config/db.php');

# Берём id площадки
$domen = parse_url($_SERVER['HTTP_ORIGIN'], PHP_URL_HOST);

$sql= "SELECT `id`,`otchiclen`,`user_id`,`model_pay` FROM `ploshadki` WHERE `domen`='".$domen."'";
$platform = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);
$stat_arr['platform_id']=$platform['id'];

# Берем ставку и id статьи
if ($action !='l') {
    $sql= "SELECT 
               n.stavka,
               n.`persent_platform`,
               a.promo_id
           FROM anons a RIGHT OUTER JOIN anons_index n ON a.promo_id = n.promo_id WHERE a.id='".$anons_id."'";
    $promo = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);
}
$stat_arr['promo_id_list']=$promo['promo_id'];

# Обновляет информацию по переходу на статью
if ($action =='l') {
    if ($_GET['ref']=="") {
        $stat_arr['is_baned']=1;
        statpostgres($stat_arr);
        exit;
    };
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
    if($action !='c') {
        # Узнаём оплачивался ли данный просмотр
        $sql = "SELECT `pay`,`read` FROM `stat_promo_prosmotr` WHERE `prosmotr_id` = '" . $_GET['prosmort_id'] . "'";
        $pay = $GLOBALS['dbstat']->query($sql)->fetch(PDO::FETCH_ASSOC);
        if ($pay == false) {
            $stat_arr['is_baned']=1;
            statpostgres($stat_arr);
            exit;
        }

        $sql="SELECT `scroll2site` FROM `promo` p
                JOIN `style_promo` s
                ON p.`scroll2site` = s.`scroll2site_activ`
                WHERE p.`id`='".$promo['promo_id']."' AND s.`id`='".$platform['id']."';";

        if($GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN)) {
            $s2s=1.3;
        }else{
            $s2s=1;
        }

        # Изменение баланса рекламодателя (настройка на странице площадок)
        $stavka_advertiser = round($promo['stavka'] * $platform['otchiclen']*$s2s / 100, 2);
        # Изменение баланса площадки (настройка на странице таргетингов статьи)
        $stavka_ploshadka = round($stavka_advertiser * $promo['persent_platform'] / 100, 2);
    }

    if($action =='s'){
        $sql = "UPDATE `stat_promo_prosmotr` SET `pay` = '".$stavka_advertiser."', `pay_platform`='".$stavka_ploshadka."' WHERE  `prosmotr_id` = '".$_GET['prosmort_id']."'";
    }elseif($action =='r'){
        $sql = "UPDATE `stat_promo_prosmotr` SET `pay` = '".$stavka_advertiser."', `pay_platform`='".$stavka_ploshadka."', `read` = '1' WHERE `prosmotr_id` = '" . $_GET['prosmort_id'] . "'";
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
    if ((int)$block_ip<3) {
        $redis->set($platform['id'].':'.$_SERVER['REMOTE_ADDR'], $block_ip+1, 86400);
    }else{
        $redis->set($platform['id'].':'.$_SERVER['REMOTE_ADDR'], 3,86400);
        $antifrod=1;
        $stat_arr['is_baned']=1;
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
}else{
    if ($action=='r') {
        if ($pay['read']==0){
            $sql = "UPDATE `stat_promo_day_count` SET `reading` = `reading` + 1 WHERE `data`=CURDATE() AND `anons_id`='" . $_GET['anons_id'] . "' AND `promo_variant`='" . $_GET['p_id'] . "'";
            $GLOBALS['dbstat']->query($sql);
        }
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
        if ($pay['pay']==0) {
            $sql = "UPDATE `balans_ploshadki` SET `" . $widget . "`=" . $widget . "+1, `" . $widget . "_balans`=" . $widget . "_balans+" . $stavka_ploshadka . "  WHERE `date`=CURDATE() AND `ploshadka_id`='" . $platform['id'] . "'";
            if (!$GLOBALS['dbstat']->exec($sql)) {
                $sql = "INSERT INTO `balans_ploshadki` SET `ploshadka_id` = '" . $platform['id'] . "', `date` = CURDATE(), `" . $widget . "`=" . $widget . "+1, `" . $widget . "_balans`=" . $widget . "_balans+" . $stavka_ploshadka;
                $GLOBALS['dbstat']->query($sql);
            }
        }
        break;
    case 'c':
        $sql ="UPDATE `balans_ploshadki` SET `".$widget."_promo_click` = `".$widget."_promo_click` + 1 WHERE `ploshadka_id`='".$platform['id']."' AND `date`=CURDATE();";
        $GLOBALS['dbstat']->query($sql);

        # Добавление статистики пререходов со статей

        $url_components = parse_url($_GET['href']);
        parse_str($url_components['query'], $params);

        if (!isset($_GET['ancor'])){
               $_GET['ancor']=parse_url($_GET['href'], PHP_URL_HOST);
               $params['sub_id1']='-1';
        }else{
            $_GET['href']=substr($_GET['href'], 0, strpos($_GET['href'], "sub_id1="));
            $char=substr($_GET['href'],-1);
            if (($char=='&')or($char=='?')){
                $_GET['href'] = substr($_GET['href'], 0, -1);
            }
        }
        $char=substr($_GET['href'],-1);
        if ($char=='/'){
            $_GET['href'] = substr($_GET['href'], 0, -1);
        }

        $stat_arr['redirect_link']=$params['sub_id1'];

        $sql ="INSERT INTO `promo_perehod`
                SET `promo_id` = '".$promo['promo_id']."',
                    `date` = CURDATE(),
                    `numlink` = '".$params['sub_id1']."',
                    `ancor` = '".$_GET['ancor']."',
                    `href` = '".$_GET['href']."',
                    `count` = 1";
        if (!$GLOBALS['dbstat']->exec($sql)){
            $sql ="UPDATE `promo_perehod` 
                   SET `count` =`count` + 1
                   WHERE `promo_id` = '".$promo['promo_id']."' AND
                         `date` = CURDATE() AND
                         `numlink` = '".$params['sub_id1']."' AND
                         `ancor` = '".$_GET['ancor']."' AND
                         `href` = '".$_GET['href']."'";
            $GLOBALS['dbstat']->query($sql);
        }
}

if ($action !='l')statpostgres($stat_arr);

if((($action =='s')or($action =='r')) and ($pay['pay']==0)) {
    # Изменение баланса плошадки
    $sql = "UPDATE `balans_user` SET `balans` = `balans` + " . $stavka_ploshadka . " WHERE `date`=CURDATE() AND `user_id`='" . $platform['user_id'] . "'";
    if (!$GLOBALS['db']->exec($sql)) {
        $sql = "SELECT `balans` FROM `balans_user` WHERE `user_id` = '" . $platform['user_id'] . "' AND `date` =(SELECT MAX(`date`) FROM `balans_user` WHERE `user_id` = '" . $platform['user_id'] . "')";
        $oldbalans = $stavka_ploshadka + $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);

        $sql = "INSERT INTO `balans_user` SET `user_id` = '" . $platform['user_id'] . "', `date` = CURDATE(), `balans` = " . $oldbalans;
        $GLOBALS['db']->query($sql);
    }
    # Модель оплаты за прочтения статей

    if($platform['model_pay']=='CPG'){
        $sql = "UPDATE `balans_user` SET `balans` = `balans` + " . $stavka_ploshadka . ", `CPG`= `CPG` + " . $stavka_ploshadka . "  WHERE `date`=CURDATE() AND `user_id`='" . $platform['user_id'] . "'";
    }else {
        $sql = "UPDATE `balans_user` SET `CPG`= `CPG` + " . $stavka_ploshadka . "  WHERE `date`=CURDATE() AND `user_id`='" . $platform['user_id'] . "'";
    }
    if (!$GLOBALS['db']->exec($sql)) {
        $sql = "SELECT `balans` FROM `balans_user` WHERE `user_id` = '" . $platform['user_id'] . "' AND `date` =(SELECT MAX(`date`) FROM `balans_user` WHERE `user_id` = '" . $platform['user_id'] . "')";
        if($platform['model_pay']=='CPG') {
            $oldbalans = $stavka_ploshadka + $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
        }else{
            $oldbalans = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
        }
        $sql = "INSERT INTO `balans_user` SET `user_id` = '" . $platform['user_id'] . "', `date` = CURDATE(), `balans` = '".$oldbalans."', `CPG`=  '".$stavka_ploshadka."'";
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

    #Остановка при достижении нуля на балансе
    $sql = "SELECT `balans` FROM `balans_rekl` WHERE `user_id` = '" . $id_user_advertiser . "' AND `date` =(SELECT MAX(`date`) FROM `balans_rekl` WHERE `user_id` = '" . $id_user_advertiser . "')";
    $oldbalans = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
    if ($oldbalans<=0){

        require_once('/var/www/www-root/data/www/panel.cortonlab.com/controllers/NotificationsController.php');
        require_once('/var/www/www-root/data/www/panel.cortonlab.com/controllers/ArticleController.php');

        $sql = "SELECT `email` FROM `users` WHERE `id`=". $id_user_advertiser;
        $user_email = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
        NotificationsController::addNotification(0,'Рекламная кампания пользователя '.$user_email.' остановлена');

        $sql = "SELECT `id` FROM `promo` WHERE `id_user_advertiser`=". $id_user_advertiser;
        $promo_ids = $GLOBALS['db']->query($sql)->fetchALL(PDO::FETCH_COLUMN);
        foreach ($promo_ids as $i){
            $_GET['id']=$i;
            ArticleController::actionStop();
        }
    }
}