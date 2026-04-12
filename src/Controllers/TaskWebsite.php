<?php

namespace App\Controllers;

use App\Core\DatabaseConnection;
use App\Model\DatabaseTable;
use App\Controllers\Tasks;
use App\Controllers\Users;
use App\Model\TasksTable;

class TaskWebsite implements \App\Model\Website {
    public function getDefaultRoute(): string {
        return 'tasks/home';
    }   

    public function getController(string $controllerName): ? object {
        $pdo = new DatabaseConnection();
        $conn = $pdo->getPdoConnection();
        if ($controllerName == 'tasks') {
            $databaseTable = new DatabaseTable($conn, 'tasks', 'id');
            $tasksTable = new TasksTable($conn, 'tasks');
            $controller = new Tasks($databaseTable, $tasksTable);
        } else if ($controllerName == 'users') {
            $usersTable = new DatabaseTable($pdo->getPdoConnection(), 'users', 'id');
            $controller = new Users($usersTable);
        }

        return $controller;
    }

    private function connect() {
        $pdo = new DatabaseConnection();

    }
}