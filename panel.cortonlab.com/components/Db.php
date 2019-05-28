<?php
class Db
{

    public static function getConnection()
    {
        $db = new PDO("mysql:host=185.75.90.54;dbname=corton", 'corton', 'H4x4B2y5', array(PDO::ATTR_PERSISTENT => true));
        return $db;
    }

    public static function getstatConnection()
    {
        $dbstat = new PDO("mysql:host=185.75.90.54;dbname=corton-stat", 'corton', 'H4x4B2y5', array(PDO::ATTR_PERSISTENT => true));
        return $dbstat;
    }

}