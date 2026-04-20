<?php 

namespace App\Controllers;
use App\Model\DatabaseTable;
use App\Validation\UserValidation;

class Users {
    public function __construct(private DatabaseTable $databaseTable){}

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
        if (isset($_POST)) {
            $userHandler = new UserValidation();
            $rawData = $_POST;

            if($userHandler->processUserRegister($_POST) === false) {
                $errors = $userHandler->getErrors();
                return ['page_title' => 'Error', 'template' => 'register.html.php', 'variables' => ['errors' => $errors, 'rawData' => $rawData]];
            }


            $cleanData = $userHandler->getData();

            $this->databaseTable->save($cleanData);
            //problem
            header('Location: /users/registersuccess');
            exit;

        } else {
            
        }

    }



    
}
