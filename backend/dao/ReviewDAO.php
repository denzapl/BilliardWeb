<?php
namespace App\DAO;

use PDO;

class ReviewDAO {
    private $pdo;

    public function __construct() {
        $cfg = require __DIR__ . '/../config.php';
        $db = $cfg['db'];
        $this->pdo = new PDO(
            'mysql:host='.$db['host'].';dbname='.$db['dbname'].';charset=utf8mb4',
            $db['user'],
            $db['pass']
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Fetch all reviews
    public function getAll() {
        $stmt = $this->pdo->query('
            SELECT r.id, r.club_id, r.user_id, r.rating, r.comment, r.created_at,
                   u.name AS user_name, bc.club_name
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            JOIN billiard_clubs bc ON r.club_id = bc.id
        ');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch review by ID
    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM reviews WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create a new review
    public function create($data) {
        $stmt = $this->pdo->prepare('
            INSERT INTO reviews (club_id, user_id, rating, comment)
            VALUES (:club_id, :user_id, :rating, :comment)
        ');
        $stmt->execute([
            ':club_id' => $data['club_id'],
            ':user_id' => $data['user_id'],
            ':rating' => $data['rating'] ?? null,
            ':comment' => $data['comment'] ?? null
        ]);
        return $this->pdo->lastInsertId();
    }

    // Update a review
    public function update($id, $data) {
        $stmt = $this->pdo->prepare('
            UPDATE reviews SET rating = :rating, comment = :comment WHERE id = :id
        ');
        return $stmt->execute([
            ':rating' => $data['rating'] ?? null,
            ':comment' => $data['comment'] ?? null,
            ':id' => $id
        ]);
    }

    // Delete a review
    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM reviews WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
