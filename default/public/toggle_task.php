<?php

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/dbFunctions.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = (int)($_POST['task_id'] ?? null);
    $isCompleted = (int)($_POST['is_completed'] ?? 0);

    if ($task_id > 0) {
        toggleTask($pdo,$taskId, $isCompleted);
        
        header('Location: view_tasks.php');
    } 
}

include __DIR__ . '/../templates/layout.html.php';