<?php 

function loadTemplate($templateFileName, $variables) {
    extract($variables);

    ob_start();

    include __DIR__ . '/../templates/' . $templateFileName;

    return ob_get_clean();
}

require_once __DIR__ . '/../src/Core/db.php';
require_once __DIR__ . '/../Model/DatabaseTable.php';
require_once __DIR__ . '/../Controllers/TasksController.php';




$tasksTable = new DatabaseTable($pdo, 'tasks', 'task_id');

$taskController =  new TasksController($tasksTable);

$action = $_GET['action'] ?? 'home';

$page = $taskController->$action();

$page_title = $page['page_title'];

$variables = $page['variables'];

$output = loadTemplate($page['template'], $variables);


include __DIR__.'/../templates/layout.html.php';