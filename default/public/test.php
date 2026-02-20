<?php




$taskTitle = trim($_POST['task_title']);
$taskDescription= trim($_POST['task_description'] ?? '');

$dueAtRaw = $_POST['due_at'] ?? '';
$dueAt = new DateTime($dueAtRaw);

echo $dueAt->format('Y-m-d');

$priority = (int)($_POST['priority'] ?? 2);

insert($pdo, $taskTitle, $taskDescription, $dueAt, $priority);