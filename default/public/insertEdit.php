<?php

require_once __DIR__ . '/../src/Core/db.php';
require_once __DIR__ . '/../Model/DatabaseTable.php';
require_once __DIR__ . '/../src/validation/tasks.php';
require_once __DIR__ . '/../Controllers/TasksController.php';


$tasksTable = new DatabaseTable($pdo, 'tasks', 'tasks_id');

$page = new TasksController($tasksTable);

$page_title = 'Insert task';

ob_start();
include __DIR__ . '/../templates/insertEdit.html.php';
$output = ob_get_clean();

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $result = $page->insertEdit();
    if($result) {
        $errors = $result['errors'];
        $page_title = $result['page_title'];
    }
    header('Location: /view_tasks.php');
    exit;

} else {
    $old_task = $page->insertEdit();
    ob_start();
    include __DIR__ . '/../templates/insertEdit.html.php';
    $output = ob_get_clean();
    
}     


// $taskId = (int)($_GET['task_id'] ?? null);


include __DIR__ . '/../templates/layout.html.php';