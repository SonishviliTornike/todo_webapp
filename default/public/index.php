<?php 

require_once __DIR__ . '/../src/db.php';

$page_title = 'Home Page';

$welcome = 'Welcome';

$sql_query = 'SELECT * FROM `todo_webapp`.`tasks` WHERE `priority` < 3';

$result = $pdo->query($sql_query);

$tasks = [];
foreach($result as $row) {
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

include __DIR__.'/../templates/home.html.php';

$output = ob_get_clean();

include __DIR__.'/../templates/layout.html.php';