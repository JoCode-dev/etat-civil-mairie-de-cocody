<?php
// filepath: c:\wamp64\www\etatcivil\app\Repositories\acte_deces_repository.php
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'app/models/acte_deces_model.php';

/**
 * Classe de gestion des opérations sur les actes de décès
 */
class ActeDecesRepository {
    private $db;

    // Constructor
    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /**
     * Trouve un acte de décès par son ID
     */
    public function findById(int $id): ?ActeDecesModel {
        $stmt = $this->db->prepare("
            SELECT * FROM actes_deces 
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? ActeDecesModel::fromArray($data) : null;
    }

    /**
     * Recherche des actes de décès avec pagination
     */
    public function search(
        string $searchTerm = '',
        int $page = 1,
        int $perPage = 20,
        string $sortField = 'created_at',
        string $sortDirection = 'DESC'
    ): array {
        // Validation des paramètres de tri
        $allowedSortFields = ['numero_registre', 'nom', 'prenoms', 'date_deces', 'created_at'];
        $sortField = in_array($sortField, $allowedSortFields) ? $sortField : 'created_at';
        $sortDirection = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';

        // Construction de la clause WHERE
        $whereClause = '';
        $params = [];

        if (!empty($searchTerm)) {
            $whereClause = "WHERE numero_registre LIKE :search 
                        OR nom LIKE :search 
                        OR prenoms LIKE :search
                        OR date_deces_lettre LIKE :search";
            $params[':search'] = "%$searchTerm%";
        }

        // Requête de comptage
        $countSql = "
            SELECT COUNT(*) AS total 
            FROM actes_deces
            $whereClause
        ";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Requête pour récupérer les données
        $offset = ($page - 1) * $perPage;
        $dataSql = "
            SELECT * 
            FROM actes_deces
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

        $actes = [];
        while ($row = $dataStmt->fetch(PDO::FETCH_ASSOC)) {
            $actes[] = ActeDecesModel::fromArray($row);
        }

        return [
            'data' => $actes,
            'total' => $total,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    /**
     * Crée un nouvel acte de décès
     */
    public function create(ActeDecesModel $acte): ActeDecesModel {
        $sql = "
            INSERT INTO actes_deces (
                numero_registre, annee_registre, nom, prenoms, 
                date_deces_lettre, date_deces, lieu_deces
            ) VALUES (
                :numero_registre, :annee_registre, :nom, :prenoms, 
                :date_deces_lettre, :date_deces, :lieu_deces
            )
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':numero_registre' => $acte->getNumeroRegistre(),
            ':annee_registre' => $acte->getAnneeRegistre(),
            ':nom' => $acte->getNom(),
            ':prenoms' => $acte->getPrenoms(),
            ':date_deces_lettre' => $acte->getDateDecesLettre(),
            ':date_deces' => $acte->getDateDeces(),
            ':lieu_deces' => $acte->getLieuDeces()
        ]);

        $acte->setId($this->db->lastInsertId());
        return $acte;
    }

    /**
     * Met à jour un acte de décès existant
     */
    public function update(ActeDecesModel $acte): bool {
        $sql = "
            UPDATE actes_deces SET
                numero_registre = :numero_registre,
                annee_registre = :annee_registre,
                nom = :nom,
                prenoms = :prenoms,
                date_deces_lettre = :date_deces_lettre,
                date_deces = :date_deces,
                lieu_deces = :lieu_deces,
                updated_at = NOW()
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $acte->getId(),
            ':numero_registre' => $acte->getNumeroRegistre(),
            ':annee_registre' => $acte->getAnneeRegistre(),
            ':nom' => $acte->getNom(),
            ':prenoms' => $acte->getPrenoms(),
            ':date_deces_lettre' => $acte->getDateDecesLettre(),
            ':date_deces' => $acte->getDateDeces(),
            ':lieu_deces' => $acte->getLieuDeces()
        ]);
    }

    /**
     * Supprime un acte de décès par son ID
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM actes_deces WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}