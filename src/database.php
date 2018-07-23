<?php

namespace importer;

use PDO;
/**
 * This class is responbile for establishing DB connection using PDO
 */
class Database {

    public static function getDatabase() {
        $host = "localhost";
        $dbname = "infinity";
        $user = "uner";
        $pass = "pass";
        $charset = "UTF8MB4";
        $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
        $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES=> false
        ];
 
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        throw new PDOException ($e);
    }
        return $pdo;
    }
}
