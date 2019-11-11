<?php
echo exec('cd /var/www/www-root/data/www && git reset --hard origin/master && git pull git@github.com:DmitriyBessalov/corton.git');

$redis = new Redis();
$redis->pconnect('185.75.90.54', 6379);
$redis->select(0);
$cache=$redis->get('cdn_cache_update');
if ($cache) $cache_arr=json_decode($cache, true);
$cache_arr['paths'][]='/js/cortonlab.js.gz';
$cache_arr['paths']=array_unique($cache_arr['paths']);
$cache=json_encode($cache_arr);
$redis->set('cdn_cache_update',$cache, 1296000);
$redis->close();

exec('wget -q https://api2.cortonlab.com/update_cache_cdn.php -O - >/dev/null 2>&1');