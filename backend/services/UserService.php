<?php
namespace App\Services;

use App\DAO\UserDAO;

class UserService {
    private $dao;

    public function __construct(UserDAO $dao) {
        $this->dao = $dao;
    }

    /**
     * Retrieves a single user profile, including their role name.
     * @param int $id The ID of the user.
     * @return array The user profile data.
     * @throws \Exception If the user is not found.
     */
    public function getUserProfile(int $id): array {
        if ($id <= 0) {
            // For the mock profile, we default to ID 1 to ensure a profile exists.
            $id = 1;
        }
        
        $user = $this->dao->readUserProfile($id);
        
        if (!$user) {
            throw new \Exception("User profile with ID {$id} not found.", 404);
        }
        
        return $user;
    }
}