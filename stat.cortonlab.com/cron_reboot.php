<?php
require_once('/var/www/www-root/data/www/panel.cortonlab.com/config/db.php');
$sql= "INSERT INTO `corton`.`platforms_domen_memory`(`domen`, `id`) SELECT `ploshadki`.`domen`, `ploshadki`.`id` FROM `corton`.`ploshadki`";
$GLOBALS['db']->query($sql);