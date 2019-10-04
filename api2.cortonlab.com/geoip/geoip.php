<?php
require_once '/var/www/www-root/data/www/api2.cortonlab.com/geoip/vendor/autoload.php';
use GeoIp2\Database\Reader;
$reader = new Reader('/var/www/www-root/data/www/api2.cortonlab.com/geoip/GeoLite2-City.mmdb');
$record = $reader->city($_SERVER['REMOTE_ADDR']);
if ($record->mostSpecificSubdivision->isoCode == '') {
    $stat_arr['iso'] = $arr['region'] = $iso = $record->country->isoCode;
} else {
    $stat_arr['iso'] = $arr['region'] = $iso = $record->country->isoCode . '-' . $record->mostSpecificSubdivision->isoCode;
}
