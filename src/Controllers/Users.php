<?php 

namespace App\Controllers;
use App\Model\DatabaseTable;

class Users {
    public function __construct(private DatabaseTable $usersTable){}

    public function registrationForm() {
        return [
            'template' => 'register.html.php',
            'page_title' => 'Register an account',
            'variables' => ['']
        ];
    }    

    public function success() {
        return [
            'template' => 'registerSuccess.html.php',
            'page_title' => 'Registration Successful',
            'variables' => ['']
        ];
    }

    public function registrationFormSubmit() {
        
    }



    
}
