<?php 

namespace App\Core;

class Authentication {
    public function __construct(private \App\Model\DatabaseTable $users, private string $passwordColumn) {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    }


    public function login(array $userData):bool {
        $user = $this->users->find($userData['identity'], $userData['userColumnName']);
        if ($user !== false && password_verify($userData['password'], $user[$this->passwordColumn])) {
            session_regenerate_id();
            $_SESSION['userName'] = $user['userName'];
            $_SESSION['userId'] = $user['id'];

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