<?php
namespace App\Services;

use App\DAO\RoleDAO;

class RoleService {
    private $dao;

    public function __construct(RoleDAO $dao) {
        $this->dao = $dao;
    }

    /**
     * Retrieves a single role by ID.
     * @param int $id The ID of the role.
     * @return array The role data.
     * @throws \Exception If the role is not found.
     */
    public function getRoleById(int $id): array {
        if ($id <= 0) {
            throw new \Exception("Invalid role ID provided.", 400);
        }
        
        $role = $this->dao->readOne($id);
        
        if (!$role) {
            throw new \Exception("Role with ID {$id} not found.", 404);
        }
        
        return $role;
    }
}