<?php

require_once __DIR__.'/../src/db.php';
require_once __DIR__. '/../src/dbFunctions.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $taskTitle = trim($_POST['task_title'] ?? '');
    $taskDescription= trim($_POST['task_description'] ?? '');
    $priority = trim($_POST['priority'] ?? '2');

    $dueAtRaw = $_POST['due_at'] ?? '';
    $dueAt = new DateTime($dueAtRaw);
    
    if(!ctype_digit((string)$priority) || (int) $priority <= 0) {
        http_response_code(400);
        exit('Invalid priority');
    }

    $priority = (int)$priority;

    $values = [
        'task_title' => $taskTitle,
        'task_description' => $taskDescription,
        'priority' => $priority,
        'due_at' =>$dueAt 
    ];
    
    insert($pdo, 'tasks', $values);

    header('Location: /view_tasks.php');
    exit;
}else {
    $page_title = 'Add task';
    ob_start();

    include __DIR__ .'/../templates/addtask.html.php';

    $output = ob_get_clean();
}

include __DIR__ . '/../templates/layout.html.php';