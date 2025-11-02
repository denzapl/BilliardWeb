<?php
// dao/BilliardClubDAO.php
require_once __DIR__ . '/../database.php';

class BilliardClubDAO {
  private $pdo;
  public function __construct() {
    $this->pdo = DB::getConnection();
  }

  // Create club
  public function create($data) {
    $sql = "INSERT INTO billiard_clubs (name, address, owner_name) VALUES (:name, :address, :owner_name)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
      ':name' => $data['name'],
      ':address' => $data['address'] ?? null,
      ':owner_name' => $data['owner_name'] ?? null
    ]);
    return $this->pdo->lastInsertId();
  }

  // Read club by id
  public function findById($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM billiard_clubs WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
  }

  // Read all clubs (without member counts)
  public function findAll($limit = 100, $offset = 0) {
    $stmt = $this->pdo->prepare("SELECT * FROM billiard_clubs ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  // Get all clubs with member count
  public function findAllWithMemberCount() {
    $sql = "
      SELECT c.id, c.name, c.address, c.owner_name, c.created_at,
             COUNT(m.id) AS member_count
      FROM billiard_clubs c
      LEFT JOIN club_members m ON m.club_id = c.id
      GROUP BY c.id
      ORDER BY c.name ASC
    ";
    $stmt = $this->pdo->query($sql);
    return $stmt->fetchAll();
  }

  // Update
  public function update($id, $data) {
    $fields = [];
    $params = [':id' => $id];
    foreach (['name','address','owner_name'] as $col) {
      if (isset($data[$col])) {
        $fields[] = "$col = :$col";
        $params[":$col"] = $data[$col];
      }
    }
    if (empty($fields)) return false;
    $sql = "UPDATE billiard_clubs SET " . implode(', ', $fields) . " WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute($params);
  }

  // Delete
  public function delete($id) {
    $stmt = $this->pdo->prepare("DELETE FROM billiard_clubs WHERE id = :id");
    return $stmt->execute([':id' => $id]);
  }
}
