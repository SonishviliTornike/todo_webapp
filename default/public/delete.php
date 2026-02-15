<?php 

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/dbFunctions.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $taskId = (int)($_POST['task_id'] ?? null);
    if($taskId > 0){
        deleteTask($pdo, $taskId);

    }
}
        
        
        
header('Location: view_tasks.php');
exit;