<?php
namespace App\DAO;
use PDO;
use PDOException; 

class UserDAO {
    private $pdo;
    
    public function __construct(array $db) {
        try {
            $dsn = 'mysql:host='.$db['host'].';dbname='.$db['dbname'].';charset=utf8';
            $this->pdo = new PDO($dsn, $db['user'], $db['pass']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception("Database connection failed for UserDAO.", 0, $e);
        }
    }

    /**
     * Reads a single user profile entry by ID, joining with the role name.
     * @param int $id The ID of the user to read.
     * @return array|null The user profile data with role name or null if not found.
     */
    public function readUserProfile(int $id): ?array {
        $sql = 'SELECT 
                    u.id, 
                    u.username, 
                    u.email, 
                    u.member_since, 
                    u.total_clubs_managed, 
                    u.role_id, 
                    r.role_name
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE u.id = :id';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}