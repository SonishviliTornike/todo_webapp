<?php

namespace App\Controllers;
use App\Core\Authentication;
use App\Model\DatabaseTable;
use App\Validation\LoginValidation;


class Login {
    public function __construct(private DatabaseTable $usersTable, private Authentication $authentication, private LoginValidation $loginValidation) {
        
    }

    public function login(): array {
        return ['template' => 'login.html.php', 'page_title' => 'Log in'];
    }


    public function loginSubmit() {
        $rawUserData = $_POST['login'] ?? [];

        if (!$this->loginValidation->verify($rawUserData)) {
            $errors = $this->loginValidation->getErrors();
            return ['template' => 'login.html.php', 'page_title' => 'Log in', 'variables' => ['errors' => $errors, 'identity' => $rawUserData['identity']] ];
        }
        
        $validUserData = $this->loginValidation->getData();

        if (!$this->authentication->login($validUserData)) {
            return ['template' => 'login.html.php', 'page_title' => 'Log in', 'variables' => ['errors' => ['Invalid Password']]];
        }

        header('Location: /tasks/home');
        exit();



    }
} 
