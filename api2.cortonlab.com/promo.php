<?php
header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json; charset=UTF-8');
require_once('/var/www/www-root/data/www/panel.cortonlab.com/config/db.php');
//require_once('/var/www/www-root/data/www/stat.cortonlab.com/postgres.php');
$stat_arr['is_click_preview']=1;
$stat_arr['view_id']=$_GET['prosmort_id'];
$stat_arr['preview_id_list']=$_GET['anons_id'];
$stat_arr['platform_id']=$_GET['p_id'];
if ($_GET['t']=='r') $stat_arr['recomend']=1;
if ($_GET['t']=='e') $stat_arr['native']=1;

//Выдача промо статьи
$sql="SELECT @main_promo_id:=`promo_id` FROM `anons` WHERE `id`='".addslashes($_GET['anons_id'])."';";
$GLOBALS['db']->query($sql);
$sql="SELECT * FROM (SELECT `id`,`title`,`text`,`istochnic` FROM `promo` WHERE `main_promo_id`=@main_promo_id AND `active`=1 ORDER BY RAND() LIMIT 1) as x,
                    (SELECT `form_title`,`form_text`,`form_button`,`scroll2site`,`scroll2site_text`,`scroll2site_url`,`scroll2site_url_text`,`scroll2site_img_desktop`,`scroll2site_img_mobile` FROM `promo` WHERE `id`=@main_promo_id) as y";
$result = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);

echo json_encode($result ,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if ($result){
    $stat_arr['promo_id_list']=$result['id'];
}else{
    $stat_arr['is_baned']=1;
}
//statpostgres($stat_arr);