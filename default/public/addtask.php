<?php

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/dbFunctions.php';

$page_title = 'Add task';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $errors = [];

    $taskTitle = trim($_POST['task_title'] ?? '');

    $taskDescription= trim($_POST['task_description'] ?? '');

    $priority = trim($_POST['priority'] ?? '2');
    
    $dueAtRaw = trim($_POST['due_at'] ?? '');

    if ($taskTitle === '' || mb_strlen($taskTitle) > 100) {
        $errors['title'] = ['Title is required and must be <= 100 chars.'];
    }
    
    if (mb_strlen($taskDescription) > 1000){
        $errors['task_description'] = ['Description must be <= 1000 chars.'];
    }


    if($taskDescription === '') {
        $taskDescription = null;
    }


    if (!ctype_digit((string)$priority)) {
        $errors['priority'] = ['Invalid priority value'];
        $priority = null;
    }else {
        $priority = (int)$priority;
    
        $allowedPriorities = [1, 2, 3];
    
        if(!in_array($priority, $allowedPriorities, true)){
            $errors['priority'] = ['Invalid priority value'];
        }

    } 

    $dueAt = null;
    if ($dueAtRaw !== '') {
        $dt = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $dueAtRaw);
        $err = DateTimeImmutable::getLastErrors() ?: ['warning_count' => 0, 'error_count' => 0];
        if (!$dt || $err['warning_count'] || $err['error_count']) {
            $errors['due_at'] = ['Invalid deadline'];
        }else {
            $dueAt = $dt->format('Y-m-d H:i:s');
        }
    }

    if(!empty($errors)){
        http_response_code(400);
        ob_start();
        include __DIR__ . '/../templates/addtask.html.php';
        $output = ob_get_clean();
    } else {
        $values = [
            'task_title' => $taskTitle,
            'task_description' => $taskDescription,
            'priority' => $priority,
            'due_at' =>$dueAt 
        ];
    
        insert($pdo, 'tasks', $values);
    
        header('Location: /view_tasks.php');
        exit;
    }

}else {
    ob_start();

    include __DIR__ .'/../templates/addtask.html.php';

    $output = ob_get_clean();
}


include __DIR__ . '/../templates/layout.html.php';