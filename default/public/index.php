<?php 

require_once __DIR__ . '/../src/Core/db.php';
require_once __DIR__ . '/../Model/DatabaseTable.php';

$page_title = 'Home Page';

$welcome = 'Welcome';

$tasksTable = new DatabaseTable($pdo, 'tasks', 'tasks_id');

$result = $tasksTable->showHighPriortyTasks();

$tasks = [];
foreach($result as $row) {
    $tasks[] = array(
        'task_title' => $row['task_title'],
        'task_description' => $row['task_description'],
        'due_at' => $row['due_at'],
        'priority' => $row['priority'],  
        );
}
        
ob_start();

include __DIR__.'/../templates/home.html.php';

$output = ob_get_clean();

include __DIR__.'/../templates/layout.html.php';