<?php
namespace App\Services;

use App\DAO\BilliardClubDAO;

class BilliardClubService {
    private $dao;

    public function __construct(BilliardClubDAO $dao) {
        $this->dao = $dao;
    }

    public function getAllClubs() {
        return $this->dao->readAll();
    }
    
    /**
     * Retrieves a single club by ID.
     * @param int $id The ID of the club.
     * @return array The club data.
     * @throws \Exception If the club is not found.
     */
    public function getClubById(int $id): array {
        if ($id <= 0) {
            throw new \Exception("Invalid club ID provided.", 400);
        }
        
        $club = $this->dao->readOne($id);
        
        if (!$club) {
            throw new \Exception("Club with ID {$id} not found.", 404);
        }
        
        return $club;
    }

    /**
     * Validates and creates a new club.
     * @param array $data The club data from the POST request.
     * @return int The ID of the newly created club.
     * @throws \Exception If validation fails.
     */
    public function createClub(array $data): int {
        // --- 1. Basic Validation ---
        if (empty($data['club_name']) || empty($data['owner_name'])) {
            throw new \Exception('Club Name and Owner Name are required.', 400);
        }

        // --- 2. Sanitize and prepare data ---
        $clubData = [
            'club_name' => htmlspecialchars($data['club_name'] ?? ''),
            'owner_name' => htmlspecialchars($data['owner_name'] ?? ''),
            'address' => htmlspecialchars($data['address'] ?? ''),
            // Use 0 if the field is empty
            'club_member_count' => (int)($data['club_member_count'] ?? 0),
            // Default to 0 if category_id is missing or null, cast to int
            'category_id' => (int)($data['category_id'] ?? 0), 
        ];

        // --- 3. Call DAO to persist ---
        return $this->dao->create($clubData);
    }
    
    /**
     * Validates and updates an existing club.
     * @param int $id The ID of the club to update.
     * @param array $data The club data from the PUT request.
     * @return bool True on successful update.
     * @throws \Exception If validation fails or update fails.
     */
    public function updateClub(int $id, array $data): bool {
        if ($id <= 0) {
            throw new \Exception("Invalid club ID provided.", 400);
        }
        
        // --- 1. Basic Validation ---
        if (empty($data['club_name']) || empty($data['owner_name'])) {
            throw new \Exception('Club Name and Owner Name are required.', 400);
        }

        // --- 2. Sanitize and prepare data ---
        $clubData = [
            'club_name' => htmlspecialchars($data['club_name'] ?? ''),
            'owner_name' => htmlspecialchars($data['owner_name'] ?? ''),
            'address' => htmlspecialchars($data['address'] ?? ''),
            'club_member_count' => (int)($data['club_member_count'] ?? 0),
            'category_id' => (int)($data['category_id'] ?? 0), 
        ];
        
        // Check if club exists before attempting update (readOne throws if not found)
        $this->getClubById($id); // Will throw 404 if not found

        // --- 3. Call DAO to persist update ---
        // DAO update handles the query; we just ensure no exceptions are thrown here.
        $this->dao->update($id, $clubData);

        return true;
    }
    
    /**
     * Deletes a club by ID.
     * @param int $id The ID of the club to delete.
     * @return bool True on successful deletion.
     * @throws \Exception If the club ID is invalid or deletion fails.
     */
    public function deleteClub(int $id): bool {
        if ($id <= 0) {
            throw new \Exception("Invalid club ID provided.", 400);
        }
        
        // The DAO returns true if a row was affected.
        if (!$this->dao->delete($id)) {
             throw new \Exception("Club with ID {$id} not found or could not be deleted.", 404);
        }
        
        return true;
    }
}