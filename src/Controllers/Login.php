<?php

namespace App\Controllers;
use App\Core\Authentication;
use App\Model\DatabaseTable;
use App\Validation\LoginValidation;


class Login {
    public function __construct(private DatabaseTable $usersTable, private Authentication $authentication, private LoginValidation $loginValidation) {
        
    }

    public function login(): array {
        return ['template' => 'login.html.php', 'pageTitle' => 'Log in'];
    }


    public function loginSubmit() {
        $rawData = $_POST['login'] ?? [];

        if (!$this->loginValidation->verify($rawData)) {
            $errors = $this->loginValidation->getErrors();
            return ['template' => 'login.html.php', 'pageTitle' => 'Log in', 'variables' => ['errors' => $errors, 'identity' => $rawData['identity']] ];
        }
        
        $validData = $this->loginValidation->getData();
        

        if (!$this->authentication->login($validData['identity'], $validData['userColumnName'], $validData['password'])) {
            return ['template' => 'login.html.php', 'pageTitle' => 'Log in', 'variables' => ['identity' => $rawData['identity'], 'errors' => [['Invalid credentials']]]];
        }

        header('Location: /tasks/home');
        exit();

    }


    public function logoutSubmit() {
        $this->authentication->logout();
        header('location: /login/login');
        exit();
    }

} 
