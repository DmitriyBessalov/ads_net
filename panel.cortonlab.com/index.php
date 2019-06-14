<?php

if ($_SERVER["SERVER_PORT"]==80){
    header("HTTPS/1.1 301 Moved Permanently");
    header( "Location: https://".$_SERVER["HTTP_HOST"]);
    exit;
}

header('X-XSS-Protection: 1');

// Общие настройки
ini_set('display_errors',1);
error_reporting(E_ALL & ~E_WARNING & ~E_DEPRECATED & ~E_NOTICE); 

session_start();

function ecran(&$item) {$item = addslashes($item);}
array_walk_recursive($_POST,'ecran');
array_walk_recursive($_GET,'ecran');
array_walk_recursive($_COOKIE,'ecran');

// Подключение файлов системы
define('PANELDIR', '/var/www/www-root/data/www/panel.cortonlab.com');
define('APIDIR', '/var/www/www-root/data/www/api.cortonlab.com');

require_once('/var/www/www-root/data/www/panel.cortonlab.com/config/db.php');
require_once(PANELDIR.'/components/Autoload.php');

// Вызов Router
$router = new Router();
$router->run();
