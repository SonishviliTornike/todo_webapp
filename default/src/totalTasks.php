<?php 

function totalTasks($pdo){
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM `tasks`');

    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_NUM);

    return $row[0];
}