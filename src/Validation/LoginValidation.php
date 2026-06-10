<?php 

namespace App\Validation;
use App\Model\DatabaseTable;
use App\Core\Authentication;

class LoginValidation {
    private $errors = [];
    public function __construct(private DatabaseTable $usersTable, private Authentication $authentication, private array $rawData) {
    }



    private function processIdentity(): bool {
        $identity = $this->rawData['identity'] ?? '';

        if ($identity === '') {
            $this->errors['identity'][] = 'Username or Email can\'t be blank';
            return false;
        }

        if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            if (!$this->usersTable->find($identity, 'email')) {
                $this->errors['identity'][] = 'Email or passsword was not found, please try again.';
                return false;
            }

            $this->data[] = $identity;

        } else {
            if (strlen($identity) < 3) {
                $this->errors['identity'][]  = 'Username can\'t be less than 3 characters long';
                return false;
            }

            if (strlen($identity) > 55) {
                $this->errors['identity'][] = 'Username can\'t be more than 55 characters long';
                return false;
            }

            if (!$this->usersTable->find($identity, 'userName')) {
                $this->errors['identity'][] = 'Username or password was not found, please try again.';
                return false;
            }

            
        }        
        return true;
    }

    private function processPassword(): bool {
        $password = $this->rawData['password'] ?? '';

        if ($password === '') {
            $this->errors['password'][] = 'Password can\'t be blank';
            return false;
        }

        if (strlen($password) < 11) {
            $this->errors['password'][] = 'Password can\t be less than 11 characters';
            return false;
        }

        if (strlen($password) > 15) {
            $this->errors['password'][] = 'Password can\'t be more than 15 characters';
            return false;
        }
        return true;

    }
        
}