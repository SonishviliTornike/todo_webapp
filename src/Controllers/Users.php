<?php 

namespace App\Controllers;
use App\Model\DatabaseTable;
use App\Validation\UserValidation;

class Users {
    public function __construct(private DatabaseTable $usersTable){}

    public function registrationForm() {
        return [
            'template' => 'register.html.php',
            'page_title' => 'Register an account',
            'variables' => ['']
        ];
    }    

    public function registerSuccess() {
        return [
            'template' => 'registersuccess.html.php',
            'page_title' => 'Registration Successful',
            'variables' => ['']
        ];
    }

    public function registrationFormSubmit() {
        $rawData = $_POST['users'];
        if (!empty($rawData)) {
            $userHandler = new UserValidation($this->usersTable);

            if($userHandler->processUserRegister($rawData) === false) {
                $errors = $userHandler->getErrors();
                return ['page_title' => 'Error', 'template' => 'register.html.php', 'variables' => ['errors' => $errors, 'rawData' => $rawData]];
            }
            
            $cleanData = $userHandler->getData();

            $this->usersTable->save($cleanData);
            //problem
            header('Location: /users/registersuccess');
            exit;

        } else {
            return ['page_title' => 'Error', 'template' => 'register.html.php', 'variables' => ['errors' => 'Error occured invalid input', 'rawData' => $rawData]];
        }

    }



    
}
