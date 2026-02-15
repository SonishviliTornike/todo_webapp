<?php

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/dbFunctions.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $taskId = (int)($_GET['task_id'] ?? null);

    if($taskId > 0){
        $tasks = getTask($pdo, $taskId);
        
        $taskTitle = $tasks['task_title'];
    
        $taskDescription = $tasks['task_description'];
    
        $dueAt = date('Y-m-d\TH:i', strtotime($tasks['due_at']));
        
        $page_title = 'Update Tasks';
    
        ob_start();
        include __DIR__ . '/../templates/update.html.php';
        $output = ob_get_clean();
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){

    $taskId = (int)($_POST['task_id'] ?? null);
    $taskTitle = trim($_POST['task_title'] ?? '');
    $taskDescription = trim($_POST['task_description'] ?? '');
    $dueAtRaw = trim($_POST['due_at'] ?? '');

    $dueAt= $dueAtRaw !== '' ? str_replace('T', ' ', $dueAtRaw) . ':00' : null;

    if($taskId > 0){
        updateTask($pdo, $taskId, $taskTitle, $taskDescription, $dueAt);
    
        header('Location: /view_tasks.php');
    }
}

include __DIR__ . '/../templates/layout.html.php';