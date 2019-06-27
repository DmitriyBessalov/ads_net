<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
require_once('/var/www/www-root/data/www/panel.cortonlab.com/config/db.php');//Выдача промо статьи
$sql="SELECT `id`,`title`,`text`,`form_title`,`form_text`,`form_button` FROM `promo` WHERE `main_promo_id`=(SELECT `promo_id` FROM `anons` WHERE `id`='".addslashes($_GET['anons_id'])."') AND `active`=1 ORDER BY RAND() LIMIT 1";
$result = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);

echo json_encode($result ,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);