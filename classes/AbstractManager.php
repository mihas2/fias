<?php

namespace Fias;


abstract class AbstractManager
{
    /** @var \PDO */
    protected $db;

    /**
     * AddressManager constructor.
     */
    public function __construct()
    {
        $this->db = static::getDb();
    }

    /**
     * @return \PDO
     */
    public static function getDb()
    {
        $pdo = new \PDO(
            "mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME') . ";charset=" . getenv('DB_CHARSET'),
            getenv('DB_USER'),
            getenv('DB_PASSWORD')
        );

        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->query("SET collation_connection = utf8_unicode_ci");

        return $pdo;

    }
}
