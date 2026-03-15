<?php

namespace Default\Src\Config;

class DatabaseConfig {
    private $db;
    private $user;
    private $pass;
    private $host;
    private $dsn;

    public function __construct() {
        $this->db = getenv('MYSQL_DATABASE');

        $this->user = getenv('MYSQL_USER');

        $this->pass = getenv('MYSQL_PASSWORD');

        $this->host = getenv('DB_HOST');

        foreach ([
            'DB_HOST' => $this->host, 'MYSQL_DATABASE' => $this->db, 'MYSQL_USER' => $this->user, 'MYSQL_PASSWORD' => $this->pass
        ] as $name => $values) {
            if (empty($values)) throw new \Exception('Missing config: ' . $name);
        }

        $this->dsn = "mysql:host=$this->host;port=3306;dbname=$this->db;charset=utf8mb4";
    }

    public function getUserConfig() {
        return $this->user;
    }

    public function getPassConfig() {
        return $this->pass;
    }

    public function getDsnConfig() {
        return $this->dsn;
    }

}