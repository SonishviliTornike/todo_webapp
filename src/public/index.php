<?php 
require __DIR__ . '/../../vendor/autoload.php';

use App\Core\DatabaseConnection;
use App\Model\DatabaseTable;
use App\Controllers\TasksController;

function loadTemplate($templateFileName, $variables) {
    extract($variables);

    ob_start();

    include __DIR__ . '/../templates/' . $templateFileName;

    return ob_get_clean();
}



$pdo = new DatabaseConnection();


$tasksTable = new DatabaseTable($pdo->getPdoConnection(), 'tasks', 'task_id');

$taskController =  new TasksController($tasksTable);

$action = $_GET['action'] ?? 'home';

$page = $taskController->$action();

$page_title = $page['page_title'];

$variables = $page['variables'];

$output = loadTemplate($page['template'], $variables);


include __DIR__.'/../templates/layout.html.php';