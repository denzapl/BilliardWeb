<?php
namespace App\DAO;
use PDO;
use PDOException; 

class BilliardClubDAO {
    private $pdo;
    
    public function __construct(array $db) {
        try {
            // ... (Your existing connection logic) ...
            $dsn = 'mysql:host='.$db['host'].';dbname='.$db['dbname'].';charset=utf8';
            $this->pdo = new PDO($dsn, $db['user'], $db['pass']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Set default fetch mode
        } catch (PDOException $e) {
            throw new \Exception("Database connection failed. Verify MySQL is running and database exists.", 0, $e);
        }
    }

    // Existing readAll method
    public function readAll(): array {
        $stmt = $this->pdo->query('SELECT * FROM billiard_clubs');
        return $stmt->fetchAll();
    }
    
    /**
     * Reads a single club entry by ID.
     * @param int $id The ID of the club to read.
     * @return array|null The club data or null if not found.
     */
    public function readOne(int $id): ?array {
        $sql = 'SELECT * FROM billiard_clubs WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null; // Return null if not found
    }

    /**
     * Creates a new club entry in the database.
     * @param array $data Sanitized club data.
     * @return int The ID of the last inserted row.
     */
    public function create(array $data): int {
        $sql = "INSERT INTO billiard_clubs (club_name, owner_name, address, club_member_count, category_id)
                VALUES (:club_name, :owner_name, :address, :club_member_count, :category_id)";
        
        $stmt = $this->pdo->prepare($sql);
        
        // Ensure values are correctly bound, including the default 0 for category_id
        $stmt->execute([
            ':club_name' => $data['club_name'],
            ':owner_name' => $data['owner_name'],
            ':address' => $data['address'],
            ':club_member_count' => $data['club_member_count'],
            ':category_id' => $data['category_id']
        ]);

        return (int)$this->pdo->lastInsertId();
    }
    
    /**
     * Updates an existing club entry.
     * @param int $id The ID of the club to update.
     * @param array $data Sanitized club data.
     * @return bool True if the club was updated or if data was identical, false only on error.
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE billiard_clubs SET 
                    club_name = :club_name, 
                    owner_name = :owner_name, 
                    address = :address, 
                    club_member_count = :club_member_count, 
                    category_id = :category_id
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->execute([
            ':id' => $id,
            ':club_name' => $data['club_name'],
            ':owner_name' => $data['owner_name'],
            ':address' => $data['address'],
            ':club_member_count' => $data['club_member_count'],
            ':category_id' => $data['category_id']
        ]);

        // Returns true if one or more rows were affected (updated), or 0 if data was identical.
        return true; 
    }
    
    /**
     * Deletes a club from the database by ID.
     * @param int $id The ID of the club to delete.
     * @return bool True if one row was deleted, false otherwise.
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM billiard_clubs WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        // Returns true if 1 or more rows were affected (deleted)
        return $stmt->rowCount() > 0; 
    }
}