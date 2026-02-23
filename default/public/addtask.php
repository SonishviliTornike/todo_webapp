<?php

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/dbFunctions.php';
require_once __DIR__ . '/../src/validation/tasks.php';

$page_title = 'Add task';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$values, $errors] = taskCreateValidation($_POST);
    if(!empty($errors)){
        $old_values = $values;
        http_response_code(400);

        ob_start();

        include __DIR__ . '/../templates/addtask.html.php';

        $output = ob_get_clean();
    } else {
        try {
            $table = 'tasks';
            $fields = ['task_id', 'task_title', 'task_description', 'priority', 'due_at'];
            insert($pdo, $table, $fields, $values);
        
            header('Location: /view_tasks.php');

            exit;
        } catch(PDOException $e) {
            $errors['form'] = ['Server error. Please try again.'];
            http_response_code(500);
            ob_start();

            include __DIR__ .'/../templates/addtask.html.php';

            $output = ob_get_clean();
        }

    }

}else {
    ob_start();

    include __DIR__ .'/../templates/addtask.html.php';

    $output = ob_get_clean();
}


include __DIR__ . '/../templates/layout.html.php';
exit;