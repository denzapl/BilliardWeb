<?php
namespace App\Services;
use Firebase\JWT\JWT;
class AuthService {
    // Set a strong secret in production and store in env
    private $secret = 'CHANGE_ME_SET_A_STRONG_SECRET';
    public function createToken($user){
        $payload = [
            'sub'=>$user['id'],
            'name'=>$user['name'],
            'role'=>$user['role'],
            'iat'=>time(),
            'exp'=>time()+3600*24
        ];
        return JWT::encode($payload, $this->secret, 'HS256');
    }
}
