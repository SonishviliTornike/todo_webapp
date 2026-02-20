<?php

require_once __DIR__.'/../src/db.php';
require_once __DIR__. '/../src/dbFunctions.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $taskTitle = trim($_POST['task_title'] ?? '');
    $taskDescription= trim($_POST['task_description'] ?? '');
    $priority = trim($_POST['priority'] ?? '2');

    $dueAtRaw = $_POST['due_at'] ?? '';

    if ($taskTitle === '' || mb_strlen($taskTitle) > 100) {
        http_response_code(400);
        exit('Invalid title');
    }
    
    if ($taskDescription === '' || mb_strlen($taskDescription) > 1000){
        http_response_code(400);
        exit('Description too long');
    }

    if (!ctype_digit((string)$priority)) {
        http_response_code(400);
        exit('Invalid priority');
    }
    $priority = (int)$priority;


    if ($priorty < 1 || $priority > 3){
        http_response_code(400);
        exit('Priority out of range');
    }

    $$dueAt = null;
    if ($dueAtRaw !== '') {
        $dt = DateTime::createFromFormat('Y-m-d\TH:i', $dueAtRaw);
        $err = DateTime::getLastErrors();
        if (!$dt || $err['warning_count'] || $err['error_count']) {
            http_response_code(400);
            exit('Invalid due date');
        }
        $dueAt = $dt->format('Y-m-d H:i:s');
}



    $dueAt = new DateTime($dueAtRaw);

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