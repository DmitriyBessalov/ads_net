<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
$db = new PDO("mysql:host=185.75.90.54;dbname=corton", 'corton', 'W1w5J7e6', array(PDO::ATTR_PERSISTENT => true));

//Получить по анонсу промо статью
$sql="SELECT `promo_id` FROM `anons` WHERE `id`='".addslashes($_GET['anons_id'])."'";
$promo_id = $db->query($sql)->fetch(PDO::FETCH_COLUMN);

$items=Array($promo_id);

//Выдача промо статьи
$sql="SELECT `id`,`title`,`text`,`form_title`,`active`,`form_text`,`form_button` FROM `promo` WHERE `id`='".$items[array_rand($items)]."'";
$result = $db->query($sql)->fetch(PDO::FETCH_ASSOC);

if ($result['active'] or $_SERVER['HTTP_ORIGIN']=='https://demo.cortonlab.com'){
    echo json_encode($result ,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}