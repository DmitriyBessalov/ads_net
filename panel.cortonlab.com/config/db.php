<?php
$p=base64_decode('****');
$GLOBALS['db'] = new PDO('mysql:host=185.75.90.54;dbname=corton', 'corton', $p, array(PDO::ATTR_PERSISTENT => true));
$GLOBALS['dbstat'] = new PDO("mysql:host=185.75.90.54;dbname=corton-stat", 'corton', $p, array(PDO::ATTR_PERSISTENT => true));
unset($p);
