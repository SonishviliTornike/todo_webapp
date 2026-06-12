<?php 

namespace App\Validation;
use App\Model\DatabaseTable;

class LoginValidation {
    private $errors = [];
    private $data = [];
    public function __construct(private DatabaseTable $usersTable) {
    }
    public function verify(array $rawData): bool {
        if (!$this->processIdentity($rawData) || !$this->processPassword($rawData)) {
            return false;
        }

        return true;
    }

    public function getData(): array {
        return $this->data;
    }
    public function getErrors(): array {
        return $this->errors;
    }

    private function processIdentity(array $rawData): bool {
        $identity = $rawData['identity'] ?? '';

        if ($identity === '') {
            $this->errors['identity'][] = 'Username or Email can\'t be blank';
            return false;
        }

        if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            if (!$this->usersTable->find($identity, 'email')) {
                $this->errors['identity'][] = 'Invalid credentials';
                return false;
            }

            $this->data['identity'] = $identity;
            $this->data['userColumnName'] = 'email';
    
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
                $this->errors['identity'][] = 'Invalid credentials';
                return false;
            }

            $this->data['identity'] = $identity;
            $this->data['userColumnName'] = 'userName';
            
        }
        return true;  
    }

    private function processPassword(array $rawData): bool {
        $password = $rawData['password'] ?? '';

        if ($password === '') {
            $this->errors['password'][] = 'Password can\'t be blank';
            return false;
        }
        $this->data['password'] = $password;
        return true;

    }
        
}