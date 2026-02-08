<?php

include __DIR__.'/../src/db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $task_title = trim($_POST['task_title']);
    $description= trim($_POST['task_description'] ?? '');

    $due_at_raw = $_POST['due_at'] ?? '';
    $due_at = $due_at_raw !== '' ? str_replace('T', ' ', $due_at_raw) . ':00' : null;
    
    $priority = (int)($_POST['priority'] ?? 2);

    $stmt = $pdo->prepare('INSERT INTO `todo_webapp`.`tasks` (
        `task_title`, `task_description`, `created_at`, due_at, priority) VALUES (
        :task_title, :task_description, NOW(), :due_at, :priority)');

    

    $stmt->execute([
        ':task_title' => $task_title,
        ':task_description' => $description,
        ':due_at' => $due_at,
        ':priority' => $priority
    ]);

    header('Location: /view_tasks.php');
    exit;
}else {
    $page_title = 'Add task';
    ob_start();

    include __DIR__ .'/../templates/addtask.html.php';

    $output = ob_get_clean();
}

include __DIR__ . '/../templates/layout.html.php';