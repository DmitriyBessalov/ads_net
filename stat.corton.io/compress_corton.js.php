<?php
// Сжимать файл corton.js
$data = implode("", file("/var/www/www-root/data/www/api.corton.io/js/corton.js"));
$gzdata = gzencode($data, 9);
$fp = fopen("/var/www/www-root/data/www/api.corton.io/js/corton.js.gz", "w");
fwrite($fp, $gzdata);
fclose($fp);
unset($gzdata);
echo '1';
