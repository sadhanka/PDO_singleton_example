<?php

/**
 * Created by PhpStorm.
 * User: Lyubov Zhurba
 * Date: 14.07.2017
 * Time: 20:39
 */
abstract class ConfigRegistry
{
    const DBHOST = 'db_host';
    const DBNAME = 'db_name';
    const DBUSER = 'db_user';
    const DBPASS = 'db_pass';
    const SOMECONF = 'conf'; // some other value if needed

    /**
     * this introduces global state in your application which can not be mocked up for testing
     * and is therefor considered an anti-pattern! Use dependency injection instead!
     *
     * @var array
     */
    private static $storedValues = [];

    /**
     * @var array
     */
    private static $allowedKeys = [
        self::DBHOST,
        self::DBNAME,
        self::DBUSER,
        self::DBPASS,
        self::SOMECONF,
    ];

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public static function set($key, $value)
    {
        if (!in_array($key, self::$allowedKeys)) {
            throw new \InvalidArgumentException('Invalid key given');
        }

        self::$storedValues[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public static function get($key)
    {
        if (!in_array($key, self::$allowedKeys) || !isset(self::$storedValues[$key])) {
            throw new \InvalidArgumentException('Invalid key given');
        }

        return self::$storedValues[$key];
    }
}