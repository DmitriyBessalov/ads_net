<?php
if ($_SERVER['REMOTE_ADDR']=='192.168.1.153')$_SERVER['REMOTE_ADDR']='185.68.146.112';

$stat_arr= [
    'view_id'=>'null',
    'words_list'=>'null',
    'category_id_list'=>'null',
    'preview_id_list'=>'null',
    'platform_type'=>'null',
    'is_show_preview'=>'null',
    'is_click_preview'=>'null',
    'is_read_post'=>'null',
    'is_total_read_post'=>'null',
    'is_load_widget'=>'null',
    'is_baned'=>'null',
    'native'=>'null',
    'redirect_type'=>'null',
    'promo_id_list'=>'null',
    'url'=>urldecode($_SERVER['HTTP_REFERER']),
    'iso'=>'null',
    'recomend'=>'null',
    'remote_ip'=>$_SERVER['REMOTE_ADDR'],
    'platform_id'=>'null'
];

if (isset($_COOKIE['SESS_ID'])){
    $stat_arr['unique_user']=$_COOKIE['SESS_ID'];
}else{
    $stat_arr['unique_user']= substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 26);
}
setcookie('SESS_ID', $stat_arr['unique_user'], time() + (86400 * 365), "/",".cortonlab.com");

function statpostgres($stat_arr) {

    if (isset($stat_arr['iso'])) {
        require_once '/var/www/www-root/data/www/api2.cortonlab.com/geoip/geoip.php';
    }

    if (isset($stat_arr['platform_id'])) {
        $sql = "SELECT `type` FROM `ploshadki` WHERE `id`='" . $stat_arr['platform_id'] . "'";
        $stat_arr['platform_type'] = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
    }

    $GLOBALS['postgre'] = new PDO('pgsql:host=185.75.90.54;dbname=corton', 'corton', 'Qwe!23');

    $sql = "insert into tb_platform_stat_request 
        (view_id,
        words_list,
        category_id_list,
        preview_id_list,
        platform_type,
        is_show_preview,
        is_click_preview,
        is_read_post,
        is_total_read_post,
        is_load_widget,
        is_baned,
        native,
        unique_user,
        redirect_link,
        promo_id_list,
        url,
        iso,
        recomend,
        remote_ip,
        platform_id
    ) values(
        '".$stat_arr['view_id']."',
        '{".$stat_arr['words_list']."}',
        '{".$stat_arr['category_id_list']."}',
        '{".$stat_arr['preview_id_list']."}',
        '".$stat_arr['platform_type']."',
        '".$stat_arr['is_show_preview']."',
        '".$stat_arr['is_click_preview']."',
        '".$stat_arr['is_read_post']."',
        '".$stat_arr['is_total_read_post']."',
        '".$stat_arr['is_load_widget']."',
        '".$stat_arr['is_baned']."',
        '".$stat_arr['native']."',
        '".$stat_arr['unique_user']."',
        '".$stat_arr['redirect_link']."',
        '{".$stat_arr['promo_id_list']."}',
        '".$stat_arr['url']."',
        '".$stat_arr['iso']."',
        '".$stat_arr['recomend']."',
        '".$stat_arr['remote_ip']."',
        '".$stat_arr['platform_id']."')";
    $sql=str_replace("'null'","null",$sql);
    $GLOBALS['postgre'] ->query($sql);
};
