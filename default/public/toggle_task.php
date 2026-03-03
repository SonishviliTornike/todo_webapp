<?php

require_once __DIR__ . '/../src/Core/db.php';
require_once __DIR__ . '/../Model/DatabaseTable.php'; 
require_once __DIR__ . '/../Controllers/TasksController.php';




if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tasksTable = new DatabaseTable($pdo, 'tasks', 'task_id');
    
    $page = new TasksController($tasksTable);
    
    $page->setTaskCompleted();
    header('Location: /view_tasks.php');
    exit;
}

http_response_code(405);
exit('Method doesnt allowed.');