<?php

namespace App\Database;

use PDO;
use PDOException;

class Database
{
    private $host;
    private $username;
    private $password;
    private $dbname;
    private $port;

    public function __construct()
    {
        $config = require '../config/config.php';
        $db_config = $config['db'];

        $this->host = $db_config['host'];
        $this->username = $db_config['user'];
        $this->password = $db_config['password'];
        $this->dbname = $db_config['dbname'];
        $this->port = $db_config['port'];
    }

    public function getConnection()
    {
        $dsn = "mysql:host=".$this->host.";port=".$this->port.";dbname=".$this->dbname.";";
        
        try {
            $conn = new \PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            return $conn;
        } catch (PDOException $e) {
            die("ERROR DE CONEXIÓN: " . $e->getMessage());
        }
    }
}
