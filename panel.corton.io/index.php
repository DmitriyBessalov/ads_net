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

$_POST = array_map('addslashes', $_POST);
$_GET = array_map('addslashes', $_GET);
$_COOKIE = array_map('addslashes', $_COOKIE);

// Подключение файлов системы
define('PANELDIR', '/var/www/www-root/data/www/panel.corton.io');
define('APIDIR', '/var/www/www-root/data/www/api.corton.io');

require_once(PANELDIR.'/components/Autoload.php');

// Вызов Router
$router = new Router();
$router->run();

?>