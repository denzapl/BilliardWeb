<?php
namespace App\DAO;
use PDO;

class BilliardClubDAO {
    private $pdo;
    public function __construct(){
        $cfg = require __DIR__ . '/../config.php';
        $db = $cfg['db'];
        $this->pdo = new PDO('mysql:host='.$db['host'].';dbname='.$db['dbname'].';charset=utf8', $db['user'], $db['pass']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function getAll(){
        $stmt = $this->pdo->query('SELECT id, owner_name, club_name, club_member_count, address FROM billiard_clubs');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create($data){
        $stmt = $this->pdo->prepare('INSERT INTO billiard_clubs (owner_name, club_name, club_member_count, address) VALUES (:owner_name, :club_name, :club_member_count, :address)');
        $stmt->execute([
            ':owner_name'=>$data['owner_name'] ?? null,
            ':club_name'=>$data['club_name'] ?? null,
            ':club_member_count'=>$data['club_member_count'] ?? 0,
            ':address'=>$data['address'] ?? null
        ]);
        return $this->pdo->lastInsertId();
    }
}
