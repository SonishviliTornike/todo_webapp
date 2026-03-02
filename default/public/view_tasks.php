<?php

require_once __DIR__ . '/../src/Core/db.php';
require_once __DIR__ . '/../Model/DatabaseTable.php';


$tasksTable = new DatabaseTable($pdo, 'tasks', 'task_id');




$tasks = $tasksTable->findAll();


$totalTasks = $tasksTable->totalTasks();

var_dump($totalTasks);  

ob_start();
         
include __DIR__. '/../templates/view_tasks.html.php';

$output = ob_get_clean();

$page_title = 'Tasks';


include __DIR__ . '/../templates/layout.html.php';