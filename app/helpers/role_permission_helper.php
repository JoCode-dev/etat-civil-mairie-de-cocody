<?php
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'app/model/role_permission_model.php';

class RolePermissionHelper {
    private $pdo;

    // Constructor
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Assign a permission to a role
    public function assignPermissionToRole($role_id, $permission_id): bool {
        $sql = "INSERT INTO role_permission (role_id, permission_id) VALUES (:role_id, :permission_id)";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':role_id', $role_id, PDO::PARAM_INT);
        $stmt->bindValue(':permission_id', $permission_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Remove a permission from a role
    public function removePermissionFromRole($role_id, $permission_id): bool {
        $sql = "DELETE FROM role_permission WHERE role_id = :role_id AND permission_id = :permission_id";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':role_id', $role_id, PDO::PARAM_INT);
        $stmt->bindValue(':permission_id', $permission_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Get all permissions for a role
    public function getPermissionsByRoleId($role_id): array {
        $sql = "SELECT permission.* FROM permission 
                INNER JOIN role_permission ON permission.id = role_permission.permission_id 
                WHERE role_permission.role_id = :role_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':role_id', $role_id, PDO::PARAM_INT);
        $stmt->execute();

        $permissions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $permissions[] = PermissionModel::fromArray($row);
        }

        return $permissions;
    }

    // Get all roles for a permission
    public function getRolesByPermissionId($permission_id): array {
        $sql = "SELECT role.* FROM role 
                INNER JOIN role_permission ON role.id = role_permission.role_id 
                WHERE role_permission.permission_id = :permission_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':permission_id', $permission_id, PDO::PARAM_INT);
        $stmt->execute();

        $roles = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $roles[] = RoleModel::fromArray($row);
        }

        return $roles;
    }

    // Check if a role has a specific permission
    public function roleHasPermission($role_id, $permission_id): bool {
        $sql = "SELECT COUNT(*) FROM role_permission WHERE role_id = :role_id AND permission_id = :permission_id";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':role_id', $role_id, PDO::PARAM_INT);
        $stmt->bindValue(':permission_id', $permission_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }
}