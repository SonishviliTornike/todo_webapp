<?php 

declare(strict_types=1);

namespace App\Controllers;
use App\Model\DatabaseTable;
use App\Validation\RegisterValidation;

class Users {
    public function __construct(private DatabaseTable $usersTable, private RegisterValidation $registerValidation){}

    public function register() {
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

    public function registerSubmit() {
        $rawData = $_POST['users'] ?? [];
        if (!empty($rawData)) {
            if(!$this->registerValidation->processUserRegister($rawData)) {
                $errors = $this->registerValidation->getErrors();
                return ['pageTitle' => 'Register an account', 'template' => 'register.html.php', 'variables' => ['errors' => $errors, 'rawData' => $rawData]];
            }
            
            $cleanData = $this->registerValidation->getData();

            $this->usersTable->save($cleanData);

            header('Location: /users/registersuccess');
            exit();

        } else {
            return ['pageTitle' => 'Register an account', 'template' => 'register.html.php', 'variables' => ['errors' => ['_form' => ['Invalid input']], 'rawData' => $rawData]];
        }

    }



    
}
