<?php
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'app/models/citoyen_model.php';

/**
 * Classe de gestion des opérations sur les citoyens
 */
class CitoyenRepository {
    private $db;

    // Constructor
    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /**
     * Trouve un citoyen par son ID
     */
    public function findById(int $id): ?CitoyenModel {
        $stmt = $this->db->prepare("
            SELECT * FROM citoyens 
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? CitoyenModel::fromArray($data) : null;
    }

    /**
     * Recherche des citoyens avec pagination
     */
    public function search(
        string $searchTerm = '',
        int $page = 1,
        string $sortField = 'nom',
        int $perPage = 20,
        string $sortDirection = 'DESC'
    ): array {
        // Validation des paramètres de tri
        $allowedSortFields = ['nom', 'prenom', 'email', 'created_at'];
        $sortField = in_array($sortField, $allowedSortFields) ? $sortField : 'created_at';
        $sortDirection = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';

        // Construction de la clause WHERE
        $whereClause = '';
        $params = [];

        if (!empty($searchTerm)) {
            $whereClause = "WHERE nom LIKE :search 
                        OR prenom LIKE :search 
                        OR email LIKE :search";
            $params[':search'] = "%$searchTerm%";
        }

        // Requête de comptage
        $countSql = "
            SELECT COUNT(*) AS total 
            FROM citoyens
            $whereClause
        ";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Requête pour récupérer les données
        $offset = ($page - 1) * $perPage;
        $dataSql = "
            SELECT * 
            FROM citoyens
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

        $citoyens = [];
        while ($row = $dataStmt->fetch(PDO::FETCH_ASSOC)) {
            $citoyens[] = CitoyenModel::fromArray($row);
        }

        return [
            'data' => $citoyens,
            'total' => $total,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    /**
     * Crée un nouveau citoyen
     */
    public function create(CitoyenModel $citoyen): CitoyenModel {
        $sql = "
            INSERT INTO citoyens (
                nom, prenom, email, mobile, adresse, password_hash, is_active, created_at
            ) VALUES (
                :nom, :prenom, :email, :mobile, :adresse, :password_hash, :is_active, NOW()
            )
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nom' => $citoyen->getNom(),
            ':prenom' => $citoyen->getPrenom(),
            ':email' => $citoyen->getEmail(),
            ':mobile' => $citoyen->getMobile(),
            ':adresse' => $citoyen->getAdresse(),
            ':password_hash' => $citoyen->getPasswordHash(),
            ':is_active' => $citoyen->getStatut()
        ]);

        $citoyen->setId($this->db->lastInsertId());
        return $citoyen;
    }

    /**
     * Met à jour un citoyen existant
     */
    public function update(CitoyenModel $citoyen): bool {
        $sql = "
            UPDATE citoyens SET
                nom = :nom,
                prenom = :prenom,
                email = :email,
                mobile = :mobile,
                adresse = :adresse,
                password_hash = :password_hash,
                is_active = :is_active,
                updated_at = NOW()
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $citoyen->getId(),
            ':nom' => $citoyen->getNom(),
            ':prenom' => $citoyen->getPrenom(),
            ':email' => $citoyen->getEmail(),
            ':mobile' => $citoyen->getMobile(),
            ':adresse' => $citoyen->getAdresse(),
            ':password_hash' => $citoyen->getPasswordHash(),
            ':is_active' => $citoyen->getStatut()
        ]);
    }

    /**
     * Supprime un citoyen par son ID
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM citoyens WHERE id = :id");
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