<?php
namespace App\DAO;
use PDO;
use PDOException; 

class CategoryDAO {
    private $pdo;
    
    // The constructor is injected with the PDO connection established in index.php
    public function __construct(array $db) {
        try {
            $dsn = 'mysql:host='.$db['host'].';dbname='.$db['dbname'].';charset=utf8';
            $this->pdo = new PDO($dsn, $db['user'], $db['pass']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Re-throw exception for the application layer to catch
            throw new \Exception("Database connection failed for CategoryDAO.", 0, $e);
        }
    }

    public function readAll(): array {
        $stmt = $this->pdo->query('SELECT id, category_name FROM categories ORDER BY category_name ASC');
        return $stmt->fetchAll();
    }
    
    /**
     * Reads a single category entry by ID.
     * @param int $id The ID of the category to read.
     * @return array|null The category data or null if not found.
     */
    public function readOne(int $id): ?array {
        $sql = 'SELECT id, category_name FROM categories WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null; // Return null if not found
    }

    /**
     * Creates a new category entry.
     * @param array $data Contains 'category_name'.
     * @return int The ID of the new category.
     */
    public function create(array $data): int {
        $sql = "INSERT INTO categories (category_name) VALUES (:category_name)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':category_name' => $data['category_name']
        ]);
        return (int)$this->pdo->lastInsertId();
    }
    
    /**
     * Updates an existing category entry.
     * @param int $id The ID of the category to update.
     * @param array $data Sanitized category data.
     * @return bool True if the category was updated or if data was identical, false only on error.
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE categories SET 
                    category_name = :category_name
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->execute([
            ':id' => $id,
            ':category_name' => $data['category_name']
        ]);

        return true;
    }
    
    /**
     * Deletes a category by ID.
     * @param int $id The ID of the category to delete.
     * @return bool True if a row was deleted, false otherwise.
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0; 
    }
}