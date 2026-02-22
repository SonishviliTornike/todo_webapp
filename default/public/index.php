<?php 

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/dbFunctions.php';

$page_title = 'Home Page';

$welcome = 'Welcome';

$result = showHighPriorityTasks($pdo);


$tasks = [];
foreach($result as $row) {
    $tasks[] = array(
        'task_title' => $row['task_title'],
        'task_description' => $row['task_description'],
        'due_at' => $row['due_at'],
        'priority' => $row['priority'],  
        'is_completed' => $row['is_completed']
        );
}
        
ob_start();

include __DIR__.'/../templates/home.html.php';

$output = ob_get_clean();

include __DIR__.'/../templates/layout.html.php';