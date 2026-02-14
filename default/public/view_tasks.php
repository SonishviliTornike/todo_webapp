<?php

require_once __DIR__ . '/../src/db.php';

include_once __DIR__ . '/../src/totalTasks.php';


$sql_query = 'SELECT `task_id`, `task_title`, `task_description`,
    `due_at`, `priority`, `is_completed` FROM
        `todo_webapp`.`tasks`';

$result = $pdo->query($sql_query);

$tasks = [];
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $tasks[] = [
        'task_id' => $row['task_id'],
        'task_title' => $row['task_title'],
        'task_description' => $row['task_description'],
        'due_at' => $row['due_at'],
        'priority' => $row['priority'],
        'is_completed' => $row['is_completed']
    
    ];
}

$totalTasks = totalTasks($pdo);

ob_start();
         
include __DIR__. '/../templates/view_tasks.html.php';

$output = ob_get_clean();

$page_title = 'Tasks';


include __DIR__ . '/../templates/layout.html.php';