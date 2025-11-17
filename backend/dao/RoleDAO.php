<?php
namespace App\DAO;
use PDO;
use PDOException; 

class RoleDAO {
    private $pdo;
    
    public function __construct(array $db) {
        try {
            $dsn = 'mysql:host='.$db['host'].';dbname='.$db['dbname'].';charset=utf8';
            $this->pdo = new PDO($dsn, $db['user'], $db['pass']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception("Database connection failed for RoleDAO.", 0, $e);
        }
    }

    /**
     * Reads a single role entry by ID.
     * @param int $id The ID of the role to read.
     * @return array|null The role data or null if not found.
     */
    public function readOne(int $id): ?array {
        $sql = 'SELECT id, role_name FROM roles WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}