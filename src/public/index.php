<?php 

require __DIR__ . '/../../vendor/autoload.php';



$uri = strtok(ltrim($_SERVER['REQUEST_URI'], '/'), '?');

$tasksWebsite = new \App\Controllers\TaskWebsite;

$entryPoint = new \App\Model\EntryPoint($tasksWebsite);

$entryPoint->run($uri, $_SERVER['REQUEST_METHOD']);