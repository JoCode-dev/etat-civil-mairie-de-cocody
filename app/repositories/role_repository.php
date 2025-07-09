<?php

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'app/models/role_model.php';

/**
 * Classe de gestion des opérations sur les rôles
 */
class RoleRepository {
    private $db;

    // Constructor
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Trouve un rôle par son ID
     */
    public function findById(int $id): ?RoleModel {
        $stmt = $this->db->prepare("
            SELECT * FROM role 
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? RoleModel::fromArray($data) : null;
    }

    public function search(
    string $searchTerm = '',
    int $page = 1,
    string $sortField = 'titre',
    ?bool $isActive = null,
    string $sortDirection = 'ASC',
    int $perPage = 20
): array {
    // Validation des paramètres de tri
    $allowedSortFields = ['titre', 'description', 'created_at'];
    $sortField = in_array($sortField, $allowedSortFields) ? $sortField : 'titre';
    $sortDirection = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';

    // Construction de la clause WHERE
    $whereClause = [];
    $params = [];

    if (!empty($searchTerm)) {
        $whereClause[] = "(titre LIKE :search OR description LIKE :search)";
        $params[':search'] = "%$searchTerm%";
    }

    if ($isActive !== null) {
        $whereClause[] = "statut = :statut";
        $params[':statut'] = $isActive ? 1 : 0;
    }

    $whereSql = !empty($whereClause) ? 'WHERE ' . implode(' AND ', $whereClause) : '';

    // Requête de comptage
    $countSql = "
        SELECT COUNT(*) AS total 
        FROM role
        $whereSql
    ";
    $countStmt = $this->db->prepare($countSql);
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    // Requête pour récupérer les données
    $offset = ($page - 1) * $perPage;
    $dataSql = "
        SELECT * 
        FROM role
        $whereSql
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

    $roles = [];
    while ($row = $dataStmt->fetch(PDO::FETCH_ASSOC)) {
        $roles[] = RoleModel::fromArray($row);
    }

    return [
        'data' => $roles,
        'total' => $total,
        'perPage' => $perPage,
        'totalPages' => ceil($total / $perPage)
    ];
}

    /**
     * Trouve tous les rôles
     */
    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM role");
        $role = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $role[] = RoleModel::fromArray($row);
        }
        return $role;
    }

    /**
     * Crée un nouveau rôle
     */ 
    public function create(RoleModel $role): RoleModel {
        $sql = "
            INSERT INTO role (titre, description) 
            VALUES (:titre, :description)
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':titre' => $role->getTitre(),
            ':description' => $role->getDescription()
        ]);

        $role->setId($this->db->lastInsertId());
        return $role;
    }

    /**
     * Met à jour un rôle existant
     */
    public function update(RoleModel $role): bool {
        $sql = "
            UPDATE role SET
                titre = :titre,
                description = :description
            WHERE id = :id
        ";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $role->getId(),
            ':titre' => $role->getTitre(),
            ':description' => $role->getDescription()
        ]);
    }

    /**
     * Supprime un rôle par son ID
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM role WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

     public function statistiques(): array
    {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) AS total_roles,
                SUM(CASE WHEN is_active  THEN 1 ELSE 0 END) AS active_roles,
                SUM(CASE WHEN !is_active THEN 1 ELSE 0 END) AS inactive_roles
            FROM role
        ");
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'total_roles' => (int)$data['total_roles'],
            'active_roles' => (int)$data['active_roles'],
            'inactive_roles' => (int)$data['inactive_roles']
        ];
    }
    
}