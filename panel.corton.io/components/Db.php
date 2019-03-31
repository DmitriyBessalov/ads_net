<?php
class Db
{
    public static function getConnection()
    {
        $db = new PDO("mysql:host=185.75.90.54;dbname=corton", 'www-root', 'Do5aemub0e7893', array(PDO::ATTR_PERSISTENT => true));
        return $db;
    }
    public static function getstatConnection()
    {
        $dbstat = new PDO("mysql:host=185.75.90.54;dbname=corton-stat", 'www-root', 'Do5aemub0e7893', array(PDO::ATTR_PERSISTENT => true));
        return $dbstat;
    }
}
