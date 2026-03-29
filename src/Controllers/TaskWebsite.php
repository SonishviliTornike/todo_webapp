<?php

namespace App\Controllers;

use App\Core\DatabaseConnection;
use App\Model\DatabaseTable;
use App\Controllers\TasksController;

class TaskWebsite implements \App\Model\Website {
    public function getDefaultRoute(): string {
        return 'tasks/home';
    }   

    public function getController(string $controllerName): ? object {
        $pdo = new DatabaseConnection();
        $tasksTable = new DatabaseTable($pdo->getPdoConnection(), 'tasks', 'task_id');
        if ($controllerName == 'tasks') {
            $controller = new TasksController($tasksTable);
        }

        return $controller;
    }
}