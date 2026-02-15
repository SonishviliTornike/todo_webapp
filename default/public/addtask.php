<?php

require_once __DIR__.'/../src/db.php';
require_once __DIR__. '/../src/dbFunctions.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $taskTitle = trim($_POST['task_title']);
    $taskDescription= trim($_POST['task_description'] ?? '');

    $dueAtRaw = $_POST['due_at'] ?? '';
    $dueAt = $dueAtRaw !== '' ? str_replace('T', ' ', $dueAtRaw) . ':00' : null;
    
    $priority = (int)($_POST['priority'] ?? 2);

    insertTask($pdo, $taskTitle, $taskDescription, $dueAt, $priority);

    header('Location: /view_tasks.php');
    exit;
}else {
    $page_title = 'Add task';
    ob_start();

    include __DIR__ .'/../templates/addtask.html.php';

    $output = ob_get_clean();
}

include __DIR__ . '/../templates/layout.html.php';