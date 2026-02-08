<?php

require __DIR__ . '/../src/db.php';

$sql_query = 'SELECT * FROM `todo_webapp`.`tasks`';

$result = $pdo->query($sql_query);



    
$tasks = [];
foreach ($result as $row) {
    $tasks[] = array(
        'task_id' =>  $row['task_id'],
         'task_title' => $row['task_title'],
         'task_description' => $row['task_description'],
         'due_at' => $row['due_at'],
         'priority' => $row['priority'],
         'is_completed' => $row['is_completed']
    );
}



ob_start();
         
include __DIR__. '/../templates/view_tasks.html.php';

$output = ob_get_clean();

$page_title = 'Tasks';


include __DIR__ . '/../templates/layout.html.php';