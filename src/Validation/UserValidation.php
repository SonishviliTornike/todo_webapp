<?php
namespace App\Validation;

class UserValidation {
    private $data = [];
    private $errors = [];
    
    public function __construct() {}

    public function processUserRegister(array $input){
        $this->proccessFlow($input);

        if (!empty($this->errors)) {
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
    private function proccessFlow(array $input) {
        $this->processId($input);
        
        $this->processUserName($input);

        $this->processEmail($input);

        $this->processFullName($input);

        $this->processPassword($input);
    }


    private function processId(array $input) {
        $id = trim($input['id'] ?? '');

        if ($id === '') {
            return;
        }
        
        if (!ctype_digit($id)) {
           $this->errors['id'][] = 'User information can\'t be updated:string detected.'; 
           return;
        }

        if((int)$id <= 0) {
            $this->errors['id'][] = 'User information can\'t be updated:Invalid id.';
            return;
        }

        $this->data['id'] = (int)$id;
        
    }


    private function processUserName(array $input) {
        $userName = trim($input['userName'] ?? '');

        
        if (empty($userName)){
            $this->errors['userName'][] = 'User name can\'t be blank.';
            return;
        }
             
            
    
        if(strlen($userName) < 3) {
            $this->errors['userName'][] = 'User name must be min 3 characters long.';
            return;

        } 
        
        if (strlen($userName) < 3) {
            $this->errors['userName'][] = 'User name must be max 3 characters long.';
        }

        $this->data['userName'] = $userName;
    }

    private function processEmail(array $input) {
        $email = trim($input['email'] ?? '');
    
        if ($email === '') {
            $this->errors['email'][] = 'Email cant be blank.';
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'][] = 'Invalid email address.';
            return;
        }
        
        if (strlen($email) > 254) {
            $this->errors['email'][] = 'Email must be max 254 characters long.';
            return;
        }
        
        $splittedEmail = explode('@', $email);

        $hostname = '@' . strtolower($splittedEmail[1]);

        if (dns_get_record($hostname) === false) {
            $this->errors['email'][] = 'Invalid email domain address.';
            return;
        }

        $this->data['email'] = $email;
    }

    private function processFullName(array $input) {
        $fullName = trim($input['fullName'] ?? '');
        
        if(strlen($fullName) > 100) {
            $this->errors['fullName'][] = 'Full name can\'t be more than 100 characters long.';
            return;
        } 

        if (strlen($fullName) < 3 ) {
            $this->errors['fullName'][] = 'Full name can\'t be less than 3 characters long';
            return;
        }

        $this->data['fullName'] = $fullName;
    }

    private function processPassword(array $input) {
        $password = trim($input['password'] ?? '');
        

        if($password === '') {
            $this->errors['password'][] = 'Password cant be blank.';
            return;
        }

        if(strlen($password) <= 10) {
            $this->errors['password'][] = 'Password must be minimum 11 characters long.';
            return;
        }

       $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

       $this->data['passwordHash'] = $hashedPassword;

    }


}