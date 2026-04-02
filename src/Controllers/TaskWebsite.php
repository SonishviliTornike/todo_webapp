<?php

namespace App\Controllers;

use App\Core\DatabaseConnection;
use App\Model\DatabaseTable;
use App\Controllers\Tasks;
use App\Controllers\Users;

class TaskWebsite implements \App\Model\Website {
    public function getDefaultRoute(): string {
        return 'tasks/home';
    }   

    public function getController(string $controllerName): ? object {
        $pdo = new DatabaseConnection();
        if ($controllerName == 'tasks') {
            $tasksTable = new DatabaseTable($pdo->getPdoConnection(), 'tasks', 'id');
            $controller = new Tasks($tasksTable);
        } else if ($controllerName == 'users') {
            $usersTable = new DatabaseTable($pdo->getPdoConnection(), 'users', 'id');
            $controller = new Users($usersTable);
        }

        return $controller;
    }
}