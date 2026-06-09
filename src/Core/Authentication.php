<?php 

namespace App\Core;

class Authentication {
    public function __construct(private \App\Model\DatabaseTable $users, private string $userNameColumn, private string $passwordColumn) {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    }


    public function login(string $userName, string $password):bool {
        $user = $this->users->find($this->userNameColumn, $userName);
        if ($user !== false && password_verify($password, $user[$this->passwordColumn])) {
            session_regenerate_id();
            $_SESSION['userName'] = $userName;
            $_SESSION['userId'] = $user['userId'];

            return true;
        }
        return false;
    }

    public function isLoggedIn(): bool {
      return isset($_SESSION['userId']);
    }

    public function logOut(): void {
        session_unset();

        session_destroy();
    }
}