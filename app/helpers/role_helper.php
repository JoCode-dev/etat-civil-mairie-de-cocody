<?php
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'app/model/role_model.php';

class RoleHelper
{
    private $pdo;

    // Constructor
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Create Role
    public function create(RoleModel $role): bool
    {
        $sql = "INSERT INTO role (titre, description, is_active) VALUES (:titre, :description, :is_active)";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':titre', $role->getTitre());
        $stmt->bindValue(':description', $role->getDescription());
        $stmt->bindValue(':is_active', $role->getIsActive(), PDO::PARAM_BOOL);

        return $stmt->execute();
    }

    // Read Role by ID
    public function getById($id): ?RoleModel
    {
        $sql = "SELECT * FROM role WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? RoleModel::fromArray($row) : null;
    }

    // Update Role
    public function update(RoleModel $role): bool
    {
        $sql = "UPDATE role SET titre = :titre, description = :description, is_active = :is_active WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':titre', $role->getTitre());
        $stmt->bindValue(':description', $role->getDescription());
        $stmt->bindValue(':is_active', $role->getIsActive(), PDO::PARAM_BOOL);
        $stmt->bindValue(':id', $role->getId(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Delete Role
    public function delete($id): bool
    {
        $sql = "DELETE FROM role WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Get all Roles
    public function getAll(): array
    {
        $sql = "SELECT * FROM role";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $roles = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $roles[] = RoleModel::fromArray($row);
        }

        return $roles;
    }

    // Count total Roles
    public function countTotal(): int
    {
        $sql = "SELECT COUNT(*) AS total FROM role";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] ?? 0;
    }
}