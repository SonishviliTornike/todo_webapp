<?php

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/dbFunctions.php'; 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskIdRaw = $_POST['task_id'] ?? null;
    $isCompletedRaw = $_POST['is_completed'] ?? 0;


    if (!ctype_digit((string)$taskIdRaw) || (int)$taskIdRaw < 0) {
        http_response_code(400);
        exit('Error: Invalid task');
    } 

    if ($isCompletedRaw !== '0' && $isCompletedRaw !== '1'){
        http_response_code(400);
        exit('Error: Toggle must be checked or unchecked');
    }

    $taskId = (int)$taskIdRaw;
    $isCompleted = (int)$isCompletedRaw;
    
    setTaskCompleted($pdo, $taskId, $isCompleted);
        
    header('Location: view_tasks.php');
    exit;
}
http_response_code(405);
exit('Method doesnt allowed');