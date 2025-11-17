<?php
namespace App\Services;

use App\DAO\CategoryDAO;

class CategoryService {
    private $dao;

    public function __construct(CategoryDAO $dao) {
        $this->dao = $dao;
    }

    public function getAllCategories(): array {
        return $this->dao->readAll();
    }
    
    /**
     * Retrieves a single category by ID.
     * @param int $id The ID of the category.
     * @return array The category data.
     * @throws \Exception If the category is not found.
     */
    public function getCategoryById(int $id): array {
        if ($id <= 0) {
            throw new \Exception("Invalid category ID provided.", 400);
        }
        
        $category = $this->dao->readOne($id);
        
        if (!$category) {
            throw new \Exception("Category with ID {$id} not found.", 404);
        }
        
        return $category;
    }

    /**
     * Validates and creates a new category.
     * @param array $data The category data from the POST request.
     * @return int The ID of the newly created category.
     * @throws \Exception If validation fails.
     */
    public function createCategory(array $data): int {
        // --- 1. Basic Validation ---
        if (empty($data['category_name'])) {
            throw new \Exception('Category Name is required.', 400);
        }

        // --- 2. Sanitize and prepare data ---
        $categoryData = [
            'category_name' => htmlspecialchars($data['category_name']),
        ];

        // --- 3. Call DAO to persist ---
        return $this->dao->create($categoryData);
    }
    
    /**
     * Validates and updates an existing category.
     * @param int $id The ID of the category to update.
     * @param array $data The category data from the PUT request.
     * @return bool True on successful update.
     * @throws \Exception If validation fails or update fails.
     */
    public function updateCategory(int $id, array $data): bool {
        if ($id <= 0) {
            throw new \Exception("Invalid category ID provided.", 400);
        }
        
        // --- 1. Basic Validation ---
        if (empty($data['category_name'])) {
            throw new \Exception('Category Name is required.', 400);
        }

        // --- 2. Sanitize and prepare data ---
        $categoryData = [
            'category_name' => htmlspecialchars($data['category_name']),
        ];

        // Check if category exists before attempting update (getCategoryById throws if not found)
        $this->getCategoryById($id); 
        
        // --- 3. Call DAO to persist update ---
        $this->dao->update($id, $categoryData); 
        
        return true;
    }

    /**
     * Deletes a category by ID.
     * @param int $id The ID of the category to delete.
     * @return bool True on successful deletion.
     * @throws \Exception If the category ID is invalid or deletion fails.
     */
    public function deleteCategory(int $id): bool {
        if ($id <= 0) {
            throw new \Exception("Invalid category ID provided.", 400);
        }
        
        if (!$this->dao->delete($id)) {
             throw new \Exception("Category with ID {$id} not found or could not be deleted.", 404);
        }
        
        return true;
    }
}