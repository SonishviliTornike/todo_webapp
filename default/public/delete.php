<?php 

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/dbFunctions.php';


$errors = [];



if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $taskId = (int)($_POST['task_id'] ?? 0);
    if($taskId > 0){
        $success = delete($pdo, 'tasks', 'task_id', $taskId);
        if ($success) {
            header('Location: /view_tasks.php');
            exit;

        } else {
            $errors['task_id'][] = 'Cannot delete task!';
        }
    }
} else {
    header('Location: /view_tasks.php');
    exit;
}
    