<?php
require_once('/var/www/www-root/data/www/panel.cortonlab.com/config/db.php');
$redis = new Redis();
$redis->pconnect('185.75.90.54', 6379);

//Удаляем старые просмотры
$sql="DELETE FROM `stat_promo_prosmotr` WHERE `date`<='".date('Y-m-d', strtotime('-7 day'))."'";
$GLOBALS['dbstat']->query($sql);

// Сжимать файл corton.js
$data = implode("", file("/var/www/www-root/data/www/api.cortonlab.com/js/corton.js"));
$gzdata = gzencode($data, 9);
$fp = fopen("/var/www/www-root/data/www/api.cortonlab.com/js/cortonlab.js.gz", "w");
fwrite($fp, $gzdata);
fclose($fp);
unset($gzdata);

// Перенос статистики из Redis в stat_anons_day_show
$redis->select(1);
for ($i = 1; $i <= 4; $i++) {
    $y=date('d', strtotime( '-'.$i.' day'));
    $y2=date('Y-m-d', strtotime( '-'.$i.' day'));
    $keys = $redis->keys($y.':*');
    foreach ($keys as $key){
        $ch=$redis->get($key);
        preg_match('/(\d{1,2}):(\d{1,5})/', $key, $matches);
        $sql="REPLACE INTO `stat_anons_day_show` SET `date`='".$y2."',`anons_id`='".$matches[2]."', `ch`='".$ch."'";
        $GLOBALS['dbstat']->query($sql);
        $redis->delete($key);
    };
};

$redis->select(3);
for ($i = 1; $i <= 4; $i++) {
    $y=date('d', strtotime( '-'.$i.' day'));
    $y2=date('Y-m-d', strtotime( '-'.$i.' day'));
    $keys = $redis->keys($y.':*');
    foreach ($keys as $key){
        $ch=$redis->get($key);
        preg_match('/^\d{1,2}:(\d{1,5}):([rse])$/', $key, $matches);

        $sql = "UPDATE `balans_ploshadki` SET ".$matches[2]."_show_anons='".$ch."'  WHERE `date`='".$y2."' AND `ploshadka_id`='".$matches[1]."'";
        if (!$GLOBALS['dbstat']->exec($sql)){
            $sql = "INSERT INTO `balans_ploshadki` SET ".$matches[2]."_show_anons='".$ch."'  WHERE `date`='".$y2."' AND `ploshadka_id`='".$matches[1]."'";
            $GLOBALS['dbstat']->query($sql);
        }
        $redis->delete($key);
    };
};

// Сжимать статистику stat_promo_day_count до месяцев с задержкой в месяц

// Сжимать статистику balans_ploshadki до месяцев с задержкой в 3 месяца
//...

// Скидывать статистику по показам анонсов из Redis в базу MySQL
// Сжатие статистики по показам анонсов с задержкой в 3 месяца
// ...


echo 1;

/*
 *  Дополнительные задания по cron
 *
 * 1) обнулять prosmotr_id (в конце каждого месяца)
 *
 * 2) Создавать и удалять таблицы по кликам, чтобы была история за несколько последних дней
 *
 */

$redis->close();
/*
 * Структура Redis
 * db0 счеткик активного id_Просмотра (должен сбрасывается раз в месяц)
 * db1 Счетчики показа анонсов за день (формат: ДеньМесяца:idАнонса)
 * db2 block_ip
 * db3 Статистика показа виджетов по площадкам
 */