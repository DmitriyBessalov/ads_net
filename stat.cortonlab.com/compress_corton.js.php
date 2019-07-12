<?php
// Сжимать файл corton.js
$data = implode("", file("/var/www/www-root/data/www/api.cortonlab.com/js/corton.js"));
$data = preg_replace("/((^| )\/\/.*$)/m", "", $data); //Удаление коментариев
$data = preg_replace("/\n/", "", $data); //Удаление переносов строк
$data = preg_replace("/\s\s+/", " ", $data); //Удаление двойных пробелов
$gzdata = gzencode($data, 9);
$fp = fopen("/var/www/www-root/data/www/api.cortonlab.com/js/cortonlab.js.gz", "w");
fwrite($fp, $gzdata);
fclose($fp);
unset($gzdata);
echo '1';