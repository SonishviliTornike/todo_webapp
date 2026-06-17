<?php 

namespace App\Controllers;
use App\Model\DatabaseTable;
use App\Validation\RegisterValidation;

class Users {
    public function __construct(private DatabaseTable $usersTable, private RegisterValidation $registerValidation){}

    public function registrationForm() {
        return [
            'template' => 'register.html.php',
            'pageTitle' => 'Register an account',
            'variables' => ['']
        ];
    }    

    public function registerSuccess() {
        return [
            'template' => 'registersuccess.html.php',
            'pageTitle' => 'Registration Successful',
            'variables' => ['']
        ];
    }

    public function registrationFormSubmit() {
        $rawData = $_POST['users'] ?? [];
        if (!empty($rawData)) {
            if(!$this->registerValidation->processUserRegister($rawData)) {
                $errors = $this->registerValidation->getErrors();
                return ['pageTitle' => 'Error', 'template' => 'register.html.php', 'variables' => ['errors' => $errors, 'rawData' => $rawData]];
            }
            
            $cleanData = $this->registerValidation->getData();

            $this->usersTable->save($cleanData);

            header('Location: /users/registersuccess');
            exit;

        } else {
            return ['pageTitle' => 'Error', 'template' => 'register.html.php', 'variables' => ['errors' => 'Error occured invalid input', 'rawData' => $rawData]];
        }

    }



    
}
