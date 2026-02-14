<?php

require_once __DIR__ . '/../src/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $task_id = $_GET['task_id'];
    if ($task_id > 0) {
        $sql = 'SELECT `task_id`, `task_title`, `task_description`, `due_at` FROM `todo_webapp`.`tasks` WHERE task_id = :task_id';

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':task_id', $task_id, PDO::PARAM_INT);

        $stmt->execute();
        $task = $stmt->fetch(PDO::FETCH_ASSOC);

        $due_at = date('Y-m-d\TH:i', strtotime($task['due_at']));
        
        $page_title = 'Update Tasks';
        ob_start();
        include __DIR__ . '/../templates/update.html.php';
        $output = ob_get_clean();

    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){

    $task_id = $_POST['task_id'];
    $task_title = $_POST['task_title'];
    $task_description = $_POST['task_description'];
    $due_at_raw = $_POST['due_at'];

    $due_at = $due_at_raw !== '' ? str_replace('T', ' ', $due_at_raw) . ':00' : null;



    $sql = 'UPDATE `todo_webapp`.`tasks`
    SET task_title = :task_title, 
    task_description = :task_description,
    due_at = :due_at
    WHERE task_id = :task_id
    ';

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':task_id' => $task_id,
        ':task_title' => $task_title,
        ':task_description' => $task_description,
        ':due_at' => $due_at
    ]);

    header('Location: /view_tasks.php');
}

include __DIR__ . '/../templates/layout.html.php';