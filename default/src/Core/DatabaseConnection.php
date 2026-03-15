<?php 
namespace Default\Src\Core;

use Default\Src\Config\DatabaseConfig;

class DatabaseConnection {
    private $config;
    private $user;
    private $pass;
    private $dsn;

    public function __construct() {
        $this->config = new DatabaseConfig();

        $dsn = $this->config->getDsnConfig();
        $user = $this->config->getUserConfig();
        $pass = $this->config->getPassConfig();
    }

    public function getPdoConnection() {
        try {
            $pdo = new \PDO($this->dsn, $this->user, $this->pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]);

            return $pdo;
        } catch (\PDOException $e) {
            error_log('Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(503);
            die('Database connection couldn\'t be established.');
        }
    }
}