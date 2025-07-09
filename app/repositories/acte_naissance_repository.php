<?php
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'app/models/acte_naissance_model.php';

/**
 * Classe de gestion des opérations sur les actes de naissance
 */
class ActeNaissanceRepository {
    private $db;

    // Constructor
    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /**
     * Trouve un acte de naissance par son ID
     */
    public function findById(int $id): ?ActeNaissanceModel {
        $stmt = $this->db->prepare("
            SELECT * FROM actes_naissance 
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? ActeNaissanceModel::fromArray($data) : null;
    }

    /**
     * Recherche des actes de naissance avec pagination
     */
    public function search(
        string $searchTerm = '',
        int $page = 1,
        int $perPage = 20,
        string $sortField = 'created_at',
        string $sortDirection = 'DESC'
    ): array {
        // Validation des paramètres de tri
        $allowedSortFields = ['numero_registre', 'nom', 'prenoms', 'date_naissance', 'created_at'];
        $sortField = in_array($sortField, $allowedSortFields) ? $sortField : 'created_at';
        $sortDirection = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';

        // Construction de la clause WHERE
        $whereClause = '';
        $params = [];

        if (!empty($searchTerm)) {
            $whereClause = "WHERE numero_registre LIKE :search 
                        OR nom LIKE :search 
                        OR prenoms LIKE :search";
            $params[':search'] = "%$searchTerm%";
        }

        // Requête de comptage
        $countSql = "
            SELECT COUNT(*) AS total 
            FROM actes_naissance
            $whereClause
        ";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Requête pour récupérer les données
        $offset = ($page - 1) * $perPage;
        $dataSql = "
            SELECT * 
            FROM actes_naissance
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
            $actes[] = ActeNaissanceModel::fromArray($row);
        }

        return [
            'data' => $actes,
            'total' => $total,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    /**
     * Crée un nouvel acte de naissance
     */
    public function create(ActeNaissanceModel $acte): ActeNaissanceModel {
        $sql = "
            INSERT INTO actes_naissance (
                numero_registre, annee_registre, nom, prenoms, 
                date_naissance_lettre, heure_naissance_lettre,
                date_naissance, heure_naissance, lieu_naissance, 
                nom_pere, profession_pere, nom_mere, profession_mere, 
                mention_mariage, mention_divorce, mention_deces, created_at
            ) VALUES (
                :numero_registre, :annee_registre, :nom, :prenoms, 
                :date_naissance_lettre, :heure_naissance_lettre,
                :date_naissance, :heure_naissance, :lieu_naissance, 
                :nom_pere, :profession_pere, :nom_mere, :profession_mere, 
                :mention_mariage, :mention_divorce, :mention_deces, NOW()
            )
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':numero_registre' => $acte->getNumeroRegistre(),
            ':annee_registre' => $acte->getAnneeRegistre(),
            ':nom' => $acte->getNom(),
            ':prenoms' => $acte->getPrenoms(),
            ':date_naissance_lettre' => $acte->getDateNaissanceLettre(),
            ':heure_naissance_lettre' => $acte->getHeureNaissanceLettre(),
            ':date_naissance' => $acte->getDateNaissance(),
            ':heure_naissance' => $acte->getHeureNaissance(),
            ':lieu_naissance' => $acte->getLieuNaissance(),
            ':nom_pere' => $acte->getNomPere(),
            ':profession_pere' => $acte->getProfessionPere(),
            ':nom_mere' => $acte->getNomMere(),
            ':profession_mere' => $acte->getProfessionMere(),
            ':mention_mariage' => $acte->getMentionMariage(),
            ':mention_divorce' => $acte->getMentionDivorce(),
            ':mention_deces' => $acte->getMentionDeces()
        ]);

        $acte->setId($this->db->lastInsertId());
        return $acte;
    }

    /**
     * Met à jour un acte de naissance existant
     */
    public function update(ActeNaissanceModel $acte): bool {
        $sql = "
            UPDATE actes_naissance SET
                numero_registre = :numero_registre,
                annee_registre = :annee_registre,
                nom = :nom,
                prenoms = :prenoms,
                date_naissance_lettre = :date_naissance_lettre,
                heure_naissance_lettre = :heure_naissance_lettre,
                date_naissance = :date_naissance,
                heure_naissance = :heure_naissance,
                lieu_naissance = :lieu_naissance,
                nom_pere = :nom_pere,
                profession_pere = :profession_pere,
                nom_mere = :nom_mere,
                profession_mere = :profession_mere,
                mention_mariage = :mention_mariage,
                mention_divorce = :mention_divorce,
                mention_deces = :mention_deces,
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
            ':date_naissance_lettre' => $acte->getDateNaissanceLettre(),
            ':heure_naissance_lettre' => $acte->getHeureNaissanceLettre(),
            ':date_naissance' => $acte->getDateNaissance(),
            ':heure_naissance' => $acte->getHeureNaissance(),
            ':lieu_naissance' => $acte->getLieuNaissance(),
            ':nom_pere' => $acte->getNomPere(),
            ':profession_pere' => $acte->getProfessionPere(),
            ':nom_mere' => $acte->getNomMere(),
            ':profession_mere' => $acte->getProfessionMere(),
            ':mention_mariage' => $acte->getMentionMariage(),
            ':mention_divorce' => $acte->getMentionDivorce(),
            ':mention_deces' => $acte->getMentionDeces()
        ]);
    }

    /**
     * Supprime un acte de naissance par son ID
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM actes_naissance WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}