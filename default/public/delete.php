<?php 

require_once __DIR__ . '/../src/Core/db.php';
require_once __DIR__ . '/../Model/DatabaseTable.php';


$errors = [];

$tasksTable = new DatabaseTable($pdo, 'tasks', 'task_id');

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $taskId = (int)($_POST['task_id'] ?? 0);
    if($taskId > 0){
        $success = $tasksTable->delete($taskId);
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
    