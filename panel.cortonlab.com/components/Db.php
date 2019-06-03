<?php
class Db
{

    public static function getConnection()
    {
        $db = new PDO("mysql:host=185.75.90.54;dbname=corton", 'corton', 'W1w5J7e6', array(PDO::ATTR_PERSISTENT => true));
        return $db;
    }

    public static function getstatConnection()
    {
        $dbstat = new PDO("mysql:host=185.75.90.54;dbname=corton-stat", 'corton', 'W1w5J7e6', array(PDO::ATTR_PERSISTENT => true));
        return $dbstat;
    }

}