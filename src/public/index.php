<?php 

require __DIR__ . '/../../vendor/autoload.php';

session_set_cookie_params([
    'lifetime' => 1800,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$uri = strtok(ltrim($_SERVER['REQUEST_URI'], '/'), '?');

$tasksWebsite = new \App\Controllers\TaskWebsite();

$entryPoint = new \App\Model\EntryPoint($tasksWebsite, $tasksWebsite->getCsrf());

$entryPoint->run($uri, $_SERVER['REQUEST_METHOD']);