<?php

require __DIR__ . '/../src/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'];
    $is_completed = $_POST['is_completed'];

    if ($task_id > 0) {
        $sql = 'UPDATE `todo_webapp`.`tasks` SET 
        is_completed = :is_completed 
        WHERE task_id = :task_id';

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':task_id' => $task_id,
            ':is_completed' => $is_completed
        ]);
        
        header('Location: view_tasks.php');
    } 
}

include __DIR__ . '/../templates/layout.html.php';