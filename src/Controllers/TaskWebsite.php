<?php

namespace App\Controllers;

use App\Core\Authentication;
use App\Core\DatabaseConnection;
use App\Model\DatabaseTable;
use App\Controllers\Tasks;
use App\Controllers\Users;
use App\Model\TasksTable;
use App\Validation\RegisterValidation;
use App\Validation\LoginValidation;

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
        if ($controllerName === 'tasks') {
            $allowedColumnNames = ['id', 'task_title'];

            $databaseTable = new DatabaseTable($this->conn, 'tasks', 'id', $allowedColumnNames);
            $tasksTable = new TasksTable($this->conn, 'tasks');
            $controller = new Tasks($databaseTable, $tasksTable);

        } elseif ($controllerName === 'users') {
            $allowedColumnNames = ['email', 'userName'];

            $databaseTable = new DatabaseTable($this->conn, 'users', 'id', $allowedColumnNames);
            $registerValidation = new RegisterValidation($databaseTable);
            $controller = new Users($databaseTable, $registerValidation);

        } elseif ($controllerName === 'login') {
            $allowedColumnNames = ['email', 'id', 'userName'];

            $databaseTable = new DatabaseTable($this->conn, 'users', 'id', $allowedColumnNames);
            $authentication = new Authentication($databaseTable, 'passwordHash');
            $loginValidation = new LoginValidation($databaseTable);
            $controller = new Login($databaseTable, $authentication, $loginValidation);

        }

        
        return $controller;
        
            
    }
}