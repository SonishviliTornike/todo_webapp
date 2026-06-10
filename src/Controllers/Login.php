<?php

namespace App\Controllers;
use App\Core\Authentication;
use App\Model\DatabaseTable;
use App\Validation\LoginValidation;


class Login {
    public function __construct(private DatabaseTable $usersTable) {
        
    }

    public function login() {
        return ['template' => 'login.html.php', 'page_title' => 'Log in', 'variables' => []];
    }


    public function loginSubmit() {
        $rawUserData = $_POST['login'] ?? [];
        
        $authentication =  new Authentication($this->usersTable, 'userName', 'passwordHash');
        $userData = new LoginValidation($this->usersTable, $authentication, $_POST['login']);
    }
} 
