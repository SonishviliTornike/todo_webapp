<?php
namespace App\Validation;

class UserValidation {
    private $data = [];
    private $errors = [];
    
    public function __construct(private array $input) {}

    public function processUserRegister(){
        $this->proccessFlow();

        return [$this->data, $this->errors];
    }


    private function proccessFlow() {
        $this->processId();
        
        $this->processUserName();

        $this->processEmail();

        $this->processFullName();

        $this->processPassword();
    }


    private function processId() {
        $id = trim($this->input['id'] ?? '');

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


    private function processUserName() {
        $userName = trim($this->input['userName'] ?? '');

        
        if (empty($userName) || strlen($userName) > 55  || strlen($userName) < 3) {
            $this->errors['userName'][] = 'User name must be min 3 characters long and max  55 characters long';
            return;
        }

        $this->data['userName'] = $userName;
    }

    private function processEmail() {
        $email = trim($this->input['email'] ?? '');
    
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
        $hostname = strtolower($splittedEmail[1]);

        if (!dns_get_record($hostname)) {
            $this->errors['email'][] = 'Invalid email domain address.';
            return;
        }

        $this->data['email'] = $email;
    }

    private function processFullName() {
        $fullName = trim($this->input['fullName'] ?? '');
        
        if(strlen($fullName) > 100 || strlen($fullName) < 3 ) {
            $this->errors['fullName'][] = 'Full name can\'t be more than 100 characters or less than 3.';
            return;
        } 

        $this->data['fullName'] = $fullName;
    }

    private function processPassword() {
        $password = trim($this->input['password'] ?? '');
        

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