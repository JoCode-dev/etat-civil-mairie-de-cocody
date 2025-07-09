<?php
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'app/models/type_acte_model.php';

/**
 * Classe de gestion des opérations sur les types d'actes
 */
class TypeActeRepository {
    private $db;

    // Constructor
    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /**
     * Trouve un type d'acte par son ID
     */
    public function findById(int $id): ?TypeActeModel {
        $stmt = $this->db->prepare("
            SELECT * FROM type_actes 
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? TypeActeModel::fromArray($data) : null;
    }

    /**
     * Crée un nouveau type d'acte
     */
    public function create(TypeActeModel $typeActe): TypeActeModel {
        $sql = "
            INSERT INTO type_actes (
                code, libelle, description, delai_traitement, frais, statut, fichier_path, created_at
            ) VALUES (
                :code, :libelle, :description, :delai_traitement, :frais, :fichier_path, :statut, NOW()
            )
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':code' => $typeActe->getCode(),
            ':libelle' => $typeActe->getLibelle(),
            ':description' => $typeActe->getDescription(),
            ':delai_traitement' => $typeActe->getDelaiTraitement(),
            ':frais' => $typeActe->getFrais(),
             ':fichier_path' => $typeActe->getFichierPath(),
            ':statut' => $typeActe->isStatut() ? 1 : 0
        ]);

        $typeActe->setId($this->db->lastInsertId());
        return $typeActe;
    }

    /**
     * Met à jour un type d'acte existant
     */
    public function update(TypeActeModel $typeActe): bool {
        $sql = "
            UPDATE type_actes SET
                code = :code,
                libelle = :libelle,
                description = :description,
                delai_traitement = :delai_traitement,
                frais = :frais,
                fichier_path = :fichier_path,
                statut = :statut,
                updated_at = NOW()
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $typeActe->getId(),
            ':code' => $typeActe->getCode(),
            ':libelle' => $typeActe->getLibelle(),
            ':description' => $typeActe->getDescription(),
            ':delai_traitement' => $typeActe->getDelaiTraitement(),
            ':frais' => $typeActe->getFrais(),
            ':fichier_path' => $typeActe->getFichierPath(),
            ':statut' => $typeActe->isStatut() ? 1 : 0
        ]);
    }

    /**
     * Recherche des types d'actes avec pagination
     */
    public function search(
        int $page = 1,  
        string $searchTerm = '',
        string $sortField = 'libelle',
        int $perPage = 20,
        string $sortDirection = 'ASC'
    ): array {
        // Validation des paramètres de tri
        $allowedSortFields = ['code', 'libelle', 'frais', 'created_at'];
        $sortField = in_array($sortField, $allowedSortFields) ? $sortField : 'libelle';
        $sortDirection = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';

        // Construction de la clause WHERE
        $whereClause = '';
        $params = [];

        if (!empty($searchTerm)) {
            $whereClause = "WHERE code LIKE :search 
                        OR libelle LIKE :search 
                        OR description LIKE :search";
            $params[':search'] = "%$searchTerm%";
        }

        // Requête de comptage
        $countSql = "
            SELECT COUNT(*) AS total 
            FROM type_actes
            $whereClause
        ";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Requête pour récupérer les données
        $offset = ($page - 1) * $perPage;
        $dataSql = "
            SELECT * 
            FROM type_actes
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

        $typeActes = [];
        while ($row = $dataStmt->fetch(PDO::FETCH_ASSOC)) {
            $typeActes[] = TypeActeModel::fromArray($row);
        }

        return [
            'data' => $typeActes,
            'total' => $total,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    /**
     * Supprime un type d'acte par son ID
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM type_actes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function statistiques(): array {
        $sql = "
            SELECT 
                COUNT(*) AS total_types,
                SUM(CASE WHEN statut = 1 THEN 1 ELSE 0 END) AS active_types,
                SUM(CASE WHEN statut = 0 THEN 1 ELSE 0 END) AS inactive_types
            FROM type_actes
        ";

        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'total_types' => (int)$result['total_types'],
            'active_types' => (int)$result['active_types'],
            'inactive_types' => (int)$result['inactive_types']
        ];
    }
}