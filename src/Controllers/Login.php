<?php

namespace App\Controllers;
use App\Core\Authentication;
use App\Model\DatabaseTable;
use App\Validation\LoginValidation;


class Login {
    public function __construct(private DatabaseTable $usersTable, private Authentication $authentication, private LoginValidation $loginValidation) {
        
    }

    public function login(): array {
        return ['template' => 'login.html.php', 'page_title' => 'Log in', 'variables' => []];
    }


    public function loginSubmit() {
        $rawUserData = $_POST['login'] ?? [];
    
        $success = $this->loginValidation->verify($rawUserData);
    }
} 
