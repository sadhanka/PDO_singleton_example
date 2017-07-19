<?php

/**
 * Created by PhpStorm.
 * User: Lyubov Zhurba
 * Date: 19.07.2017
 * Time: 12:44
 */
final class PDOSingle
{
    static private $instance = null;

    static public $dbConn;

    private function __construct()  // new Singleton
    {
        self::$dbConn = new PDO('mysql:host=' . ConfigRegistry::get('db_host') . ';dbname=' . ConfigRegistry::get('db_name'), ConfigRegistry::get('db_user'), ConfigRegistry::get('db_pass'), array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::ATTR_EMULATE_PREPARES => false));
//        self::$dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$dbConn->beginTransaction();
    }

    private function __clone() { /* ... @return Singleton */ }  // clone
    private function __wakeup() { /* ... @return Singleton */ }  //  unserialize

    static public function getInstance()
    {
        return
            self::$instance===null
                ? self::$instance = new static()//new self()
                : self::$instance;
    }

    public function query($sql)
    {
        return self::$dbConn->query($sql);
    }

    public function prepare($sql)
    {
        return self::$dbConn->prepare($sql);
    }

    public function exec($sql)
    {
        return self::$dbConn->exec($sql);
    }

    public function rollBack()
    {
        return self::$dbConn->rollBack();
    }

    public function commit()
    {
        return self::$dbConn->commit();
    }

    public function lastInsertId()
    {
        return self::$dbConn->lastInsertId();
    }
}