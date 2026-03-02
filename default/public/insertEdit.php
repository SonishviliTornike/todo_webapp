<?php

require_once __DIR__ . '/../src/Core/db.php';
require_once __DIR__ . '/../Model/DatabaseTable.php';
require_once __DIR__ . '/../src/validation/tasks.php';


$tasksTable = new DatabaseTable($pdo, 'tasks', 'tasks_id');

$output = '';

$page_title = 'Insert task';

ob_start();
include __DIR__ . '/../templates/insertEdit.html.php';
$output = ob_get_clean();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$values, $errors] = taskValidation($_POST['task']);
    if(!isset($errors)) {
        $old_values = $values;
        http_response_code(400);

        ob_start();
        include __DIR__ . '/../templates/insertEdit.html.php';
        $output = ob_get_clean();
    } else {
        try {

            $taskTable->save($_POST['task']);
            header('Location: /view_tasks.php');
            exit;

        } catch(PDOException $e) {
            $errors['form'] = ['Server error. Please try again.'];
            http_response_code(500);
            ob_start();
            include __DIR__ . '/../templates/insertEdit.html.php';
            $output = ob_get_clean();
        }
    } 

} else {
    
    $page_title = 'Edit task';
    $taskId = (int)($_GET['task_id'] ?? 0);
    if($taskId > 0){
        $table = 'tasks';
        $field = 'task_id';
        $task = $tasksTable->find($taskId);

        if ($task) {
            $old_task = [
                'task_id' => $task['task_id'],
                'task_title' => $task['task_title'],
                'task_description' => $task['task_description'],
                'due_at' => $task['due_at'],
                'priority' => $task['priority']

            ];
        }

        ob_start();
        include __DIR__ . '/../templates/insertEdit.html.php';
        $output = ob_get_clean();
        }
}


$taskId = (int)($_GET['task_id'] ?? null);


include __DIR__ . '/../templates/layout.html.php';