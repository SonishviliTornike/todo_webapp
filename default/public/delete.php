<?php 

include __DIR__ . '/../src/db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $task_id = (int)($_POST['task_id'] ?? 0);    

    if ($task_id > 0){
        $sql=  'DELETE FROM todo_webapp.tasks WHERE task_id = :task_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['task_id' => $task_id]);
    
    }
        
}
        
        
        
header('Location: view_tasks.php');
exit;