<?php

namespace App\Model;
use App\Model\Website;
use PDOException;

class EntryPoint {
    public function __construct(private Website $website) {}

    private function loadTemplate(string $templateFileName, array $variables = []): string {
        extract($variables);

        ob_start();

        include __DIR__ . '/../Templates/' . $templateFileName;

        return ob_get_clean();
    }

    private function checkUri(string $uri) {
        if ($uri != strtolower($uri)) {
            http_response_code(301);
            header('Location: /' . strtolower($uri));
        }
    }


    public function run(string $uri, string $method) {
        try {
            if ($uri == '') {
                $uri = $this->website->getDefaultRoute();
            }

            $this->checkUri($uri);
            $route = explode('/', $uri);

            $controllerName = array_shift($route);

            $action = array_shift($route);

            $this->website->checkLogin($controllerName);
            if ($method === 'POST') {
                $action .= 'Submit';
            }
            $controller = $this->website->getController($controllerName);
            $isLoggedIn = $this->website->getAuthentication();

            if (is_callable([$controller, $action])) { 
                $page = $controller->$action(...$route);
                
                $pageTitle = $page['pageTitle'] ?? 'Untitled';
    
                $variables = $page['variables'] ?? [];
                
                $output = $this->loadTemplate($page['template'], $variables);
            } else {
                http_response_code(404);
                $pageTitle = 'Not found';
                $output = '<h2>Sorry, the page you are looking for could not be found.</h2>';
            }


        } catch (PDOException $e) {
            error_log('Error:' . $e->getMessage()  . ' in ' . $e->getFile() . ':' . $e->getLine());
            $pageTitle = 'Error';
            $output = 'Service is unavailable';
        }

        include __DIR__ . '/../Templates/layout.html.php';
    }
}