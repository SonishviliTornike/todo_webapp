<?php

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/dbFunctions.php';
require_once __DIR__ . '/../src/validation/tasks.php';

$output = '';

$page_title = 'Edit Task';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    [$values, $errors] = taskCreateValidation($_POST);
    if(!empty($errors)) {
        $old_values = $values;
        http_response_code(400);

        ob_start();
        include __DIR__ . '/../templates/update.html.php';
        $output = ob_get_clean();
    } else {
        try {
            $table = 'tasks';
            $fields = ['task_title', 'task_description', 'priority', 'due_at'];

            update($pdo, $table, $fields, $values, 'task_id');

            header('Location: /view_tasks.php');
            exit;

        } catch(PDOException $e) {
            $errors['form'] = ['Server error. Please try again.'];
            http_response_code(500);
            ob_start();
            include __DIR__ . '/../templates/update.html.php';
            $output = ob_get_clean();
        }
    } 

} else {
    $taskId = (int)($_GET['task_id'] ?? 0);
    if($taskId > 0){
        $table = 'tasks';
        $field = 'task_id';
        $tasks = get($pdo, $table, $field, $taskId);
        $old_tasks = [];
        foreach ($tasks as $task) {
            $old_tasks[] = [
                'task_id' => $taskId,
                'task_title' => $task['task_title'],
                'task_description' => $task['task_description'],
                'due_at' => $task['due_at'],
                'priority' => $task['priority']

            ];
        }
        ob_start();
        include __DIR__ . '/../templates/update.html.php';
        $output = ob_get_clean();
        }
}


$taskId = (int)($_GET['task_id'] ?? null);


include __DIR__ . '/../templates/layout.html.php';