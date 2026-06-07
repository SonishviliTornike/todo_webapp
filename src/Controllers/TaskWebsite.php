<?php

namespace App\Controllers;

use App\Core\DatabaseConnection;
use App\Model\DatabaseTable;
use App\Controllers\Tasks;
use App\Controllers\Users;
use App\Model\TasksTable;

class TaskWebsite implements \App\Model\Website {
    private ?DatabaseConnection $pdo = null;
    private ?\PDO $conn = null;
    public function __construct() {
        $this->pdo = new DatabaseConnection();
        $this->conn = $this->pdo->getPdoConnection();
    }
    public function getDefaultRoute(): string {
        return 'tasks/home';
    }   

    public function getController(string $controllerName): ? object {
        $controller = null;

        if ($controllerName == 'tasks') {
            $databaseTable = new DatabaseTable($this->conn, 'tasks', 'id');
            $tasksTable = new TasksTable($this->conn, 'tasks');
            $controller = new Tasks($databaseTable, $tasksTable);
        } elseif ($controllerName == 'users') {
            $databaseTable = new DatabaseTable($this->conn, 'users', 'id');
            $controller = new Users($databaseTable);
        }

        
        return $controller;
        
            
    }
}