<?php
namespace App\DAO;
use PDO;

class UserDAO {
    private $pdo;
    public function __construct(){
        $cfg = require __DIR__ . '/../config.php';
        $db = $cfg['db'];
        $this->pdo = new PDO('mysql:host='.$db['host'].';dbname='.$db['dbname'].';charset=utf8', $db['user'], $db['pass']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function getAll(){
        $stmt = $this->pdo->query('SELECT id, name, email, role FROM users');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create($data){
        $stmt = $this->pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)');
        $stmt->execute([
            ':name'=>$data['name'],
            ':email'=>$data['email'],
            ':password'=>password_hash($data['password'], PASSWORD_DEFAULT),
            ':role'=>$data['role'] ?? 'user'
        ]);
        return $this->pdo->lastInsertId();
    }
}
