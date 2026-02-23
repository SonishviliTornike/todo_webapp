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
            update($pdo, $table, $fields, $values);
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
    $taskId = (int)($_GET['task_id']);
    if($taskId > 0){
        $tasks = get($pdo, $taskId);
        var_dump($tasks);
        $old_tasks = [];
        foreach ($tasks as $task) {
            $old_tasks[] = [
                'task_title' => $task['task_title'],
                'task_description' => $task['task_description'],
                'due_at' => $task['due_At']

            ];
        }
        var_dump($old_tasks);
        ob_start();
        include __DIR__ . '/../templates/update.html.php';
        $output = ob_get_clean();
        }
}


$taskId = (int)($_GET['task_id'] ?? null);


include __DIR__ . '/../templates/layout.html.php';