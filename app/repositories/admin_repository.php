<?php
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'app/models/admin_model.php';


/**
 * Classe de gestion des opérations sur les administrateurs
 */
class AdminRepository
{
    private $db;

    // Constructor
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function findByEmail(string $email): ?AdminModel
    {
        $stmt = $this->db->prepare("
            SELECT a.*, r.titre AS role_name
        FROM administrateurs a
        LEFT JOIN role r ON a.role_id = r.id
            WHERE a.email = :email 
            LIMIT 1
        ");
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? AdminModel::fromArray($data) : null;
    }

    public function findById(int $id): ?AdminModel
    {
        $stmt = $this->db->prepare("
            SELECT a.*, r.titre AS role_name
        FROM administrateurs a
        LEFT JOIN role r ON a.role_id = r.id
            WHERE a.id = :id 
        LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? AdminModel::fromArray($data) : null;
    }

    public function create(AdminModel $admin): AdminModel
    {
        $sql = "
            INSERT INTO administrateurs (
                nom, prenom, email, password_hash, role_id, statut
            ) VALUES (
                :nom, :prenom, :email, :password_hash, :role_id, :statut
            )
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nom' => $admin->getNom(),
            ':prenom' => $admin->getPrenom(),
            ':email' => $admin->getEmail(),
            ':password_hash' => $admin->getPasswordHash(),
            ':role_id' => $admin->getRoleId(),
            ':statut' => $admin->isActive() ? 1 : 0
        ]);

        $admin->setId($this->db->lastInsertId());
        return $admin;
    }

    public function update(AdminModel $admin): bool
    {
        $sql = "
            UPDATE administrateurs SET
                nom = :nom,
                prenom = :prenom,
                email = :email,
                role_id = :role_id,
                statut = :statut,
                updated_at = NOW()
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $admin->getId(),
            ':nom' => $admin->getNom(),
            ':prenom' => $admin->getPrenom(),
            ':email' => $admin->getEmail(),
            ':role_id' => $admin->getRoleId(),
            ':statut' => $admin->isActive() ? 1 : 0
        ]);
    }

    public function updatePassword(int $adminId, string $newPassword): bool
    {
        $sql = "UPDATE administrateurs SET password_hash = :password WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $adminId,
            ':password' => password_hash($newPassword, PASSWORD_BCRYPT)
        ]);
    }

    /**
     * Recherche des administrateurs avec pagination
     */
    public function search(
        int $page = 1,
        string $searchTerm = '',
        string $sortField = 'nom',
        int $perPage = 20,
        string $sortDirection = 'ASC' 
    ): array {
        // Validation des paramètres de tri
        $allowedSortFields = ['nom', 'prenom', 'email', 'created_at', 'role_name'];
        $sortField = in_array($sortField, $allowedSortFields) ? $sortField : 'nom';
        $sortDirection = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';

        // Construction de la clause WHERE
        $whereClause = '';
        $params = [];

        if (!empty($searchTerm)) {
            $whereClause = "WHERE a.nom LIKE :search 
                        OR a.prenom LIKE :search 
                        OR a.email LIKE :search 
                        OR r.titre LIKE :search"; // Recherche dans role_name
            $params[':search'] = "%$searchTerm%";
        }

        // Requête de comptage
        $countSql = "
        SELECT COUNT(*) AS total 
        FROM administrateurs a
        LEFT JOIN role r ON a.role_id = r.id
        $whereClause
    ";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Requête pour récupérer les données
        $offset = ($page - 1) * $perPage;
        $dataSql = "
        SELECT a.*, r.titre AS role_name
        FROM administrateurs a
        LEFT JOIN role r ON a.role_id = r.id
        $whereClause
        ORDER BY $sortField $sortDirection
        LIMIT :limit OFFSET :offset
    ";

        $dataStmt = $this->db->prepare($dataSql);
        foreach ($params as $key => $value) {
            $dataStmt->bindValue($key, $value);
        }
        $dataStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $dataStmt->execute();

        $data = [];
        while ($row = $dataStmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = AdminModel::fromArray($row);
        }

        return [
            'data' => $data,
            'total' => $total,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    public function recordLogin(int $adminId): bool {
        $sql = "UPDATE administrateurs SET last_login = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $adminId]);
    }

    public function findAll(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;

        $countStmt = $this->db->query("SELECT COUNT(*) FROM administrateurs");
        $total = $countStmt->fetchColumn();

        $stmt = $this->db->prepare("
           SELECT a.*, r.titre AS role_name
        FROM administrateurs a
        LEFT JOIN role r ON a.role_id = r.id
        LIMIT :limit OFFSET :offset
        ");

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $admins = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $admin = AdminModel::fromArray($row);
            $admin->setRoleName((new RoleModel())->setTitre($row['role_titre'] ?? 'Rôle inconnu'));
            $admins[] = $admin;
        }

        return [
            'data' => $admins,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    /**
     * Met à jour le token "Se souvenir de moi"
     */
    public function updateRememberToken(
        int $citoyenId,
        string $token,
        string $expiryDate
    ): bool {
        $sql = "
            UPDATE administrateurs 
            SET remember_token = :token,
                remember_token_expires = :expiry,
                updated_at = NOW()
            WHERE id = :id
        ";

        $hashedToken = password_hash($token, PASSWORD_BCRYPT);

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':token' => $hashedToken,
            ':expiry' => $expiryDate,
            ':id' => $citoyenId
        ]);
    }

    /**
     * Compte le nombre total d'administrateurs.
     *
     * @return int Le nombre total d'administrateurs.
     */
    public function countAll(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM administrateurs");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Compte le nombre d'administrateurs par statut.
     *
     * @param string $statut Le statut à filtrer (actif/inactif).
     * @return int Le nombre d'administrateurs correspondant au statut.
     */
    public function countByStatut(string $statut): int
    {
        $isActive = strtolower($statut) === 'actif' ? 1 : 0;
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM administrateurs WHERE statut = :statut");
        $stmt->execute([':statut' => $isActive]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Trouve un citoyen par son token "Se souvenir de moi"
     */
    public function findByRememberToken(string $token): ?AdminModel
    {
        $sql = "
            SELECT * FROM administrateurs 
            WHERE remember_token IS NOT NULL
            AND remember_token_expires > NOW()
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($token, $row['remember_token'])) {
                return AdminModel::fromArray($row);
            }
        }

        return null;
    }

    /**
     * Supprime un administrateur par son ID.
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM administrateurs WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function statistiques(): array
    {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) AS total,
                SUM(CASE WHEN statut = 1 THEN 1 ELSE 0 END) AS actifs,
                SUM(CASE WHEN statut = 0 THEN 1 ELSE 0 END) AS inactifs
            FROM administrateurs
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
}
