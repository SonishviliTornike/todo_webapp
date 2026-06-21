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
    private ?Authentication $authentication = null;
    private ?DatabaseTable $usersTable = null;
    public function __construct() {
        $this->pdo = new DatabaseConnection();
        $this->conn = $this->pdo->getPdoConnection();
        $this->usersTable = new  DatabaseTable($this->conn, 'users', 'id', ['email', 'id', 'user_name']);
        $this->authentication = new Authentication($this->usersTable, 'password_hash');
    }
    public function getDefaultRoute(): string {
        return 'tasks/index';
    }   

    public function getController(string $controllerName): ? object {
        $controller = null;
        if ($controllerName === 'tasks') {
            $allowedColumnNames = ['id', 'task_title'];

            $databaseTable = new DatabaseTable($this->conn, 'tasks', 'id', $allowedColumnNames);
            $tasksTable = new TasksTable($this->conn, 'tasks');
            $controller = new Tasks($databaseTable, $tasksTable);

        } elseif ($controllerName === 'users') {
            $registerValidation = new RegisterValidation($this->usersTable);
            $controller = new Users($this->usersTable, $registerValidation);

        } elseif ($controllerName === 'login') {
            $loginValidation = new LoginValidation();
            $controller = new Login($this->usersTable, $this->authentication, $loginValidation);

        }

        
        return $controller;
        
            
    }

    public function getAuthentication(): bool {
        return $this->authentication->isLoggedIn();
    }

    public function checkLogin(string $uri): string {
        $restrictedPages = ['tasks/home', 'tasks/insertedit', 'tasks/delete', 'tasks/list'];
        if (in_array($uri, $restrictedPages) && !$this->authentication->isLoggedIn()) {
            header('location: /login/login');
            exit();
        }
        return $uri;
    }
}