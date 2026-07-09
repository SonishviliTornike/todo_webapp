<?php

namespace App\Model;
use App\Model\Website;

class EntryPoint {
    public function __construct(private Website $website) {}

    private function loadTemplate(string $templateFileName, array $variables = [], bool $isLoggedIn = false): string {
        extract($variables);

        ob_start();

        include __DIR__ . '/../Templates/' . $templateFileName;

        return ob_get_clean();
    }

    private function checkUri(string $uri) {
        if ($uri != strtolower($uri)) {
            http_response_code(301);
            header('Location: /' . strtolower($uri));
            exit(); 
        }
    }

    /** 
        * Executes a controller action and ensures a valid response is produced.
        *
        * A controller action must adhere to one of the following two outcomes:
        * 1. Return an array: This array will be used as the context to render 
        * the appropriate view template.
        * 2. Action sends its response and terminates via exit(), so control never
        * returns to run()
        * If an action fails to return an array or complete a response, this method 
        * will throw a RuntimeException to prevent the application from hanging 
        * or returning an empty, incomplete response.
    */
    public function run(string $uri, string $method) {
        try {
            if ($uri == '') {
                $uri = $this->website->getDefaultRoute();
            }
            $this->checkUri($uri);
            $route = explode('/', $uri);

            $controllerName = array_shift($route);
            $action = array_shift($route);

            $this->website->checkLogin($controllerName . '/' . $action);
            if ($method === 'POST') {
                $action .= 'Submit';
            }
            $controller = $this->website->getController($controllerName);
            if (is_callable([$controller, $action])) { 
                $isLoggedIn = $this->website->getAuthentication();

                $page = $controller->$action(...$route);
                if (!is_array($page)) {
                    throw new \RuntimeException('In ' . $controllerName . '/'. $action . ' array was expected but ' . get_debug_type($page) . ' was returned');
                }
                
                $pageTitle = $page['pageTitle'] ?? 'Untitled';
    
                $variables = $page['variables'] ?? [];
                
                $output = $this->loadTemplate($page['template'], $variables, $isLoggedIn);
            } else {
                $isLoggedIn = $this->website->getAuthentication();
                http_response_code(404);
                $pageTitle = 'Not found';
                $output = '<h2>Sorry, the page you are looking for could not be found.</h2>';
            }


        } catch (\PDOException $e) {
            error_log('DatabaseError:' . $e->getMessage()  . ' in ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(503);
            $pageTitle = 'Error';
            $output = 'Service is unavailable';
        }catch (\RuntimeException $e) {
            error_log('RuntimeError: ' . $e->getMessage()  . ' in ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            $pageTitle = 'Error';
            $output = 'Service is unavailable';
        }

        include __DIR__ . '/../Templates/layout.html.php';
    }
}