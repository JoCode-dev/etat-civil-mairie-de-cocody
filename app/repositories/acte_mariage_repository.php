<?php

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'app/models/acte_mariage_model.php';

/**
 * Classe de gestion des opérations sur les actes de mariage
 */
class ActeMariageRepository {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function findById(int $id): ?ActeMariageModel {
        $stmt = $this->db->prepare("SELECT * FROM actes_mariage WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? ActeMariageModel::fromArray($data) : null;
    }

    public function search(
        string $searchTerm = '',
        int $page = 1,
        int $perPage = 20,
        string $sortField = 'created_at',
        string $sortDirection = 'DESC'
    ): array {
        $allowedSortFields = ['numero_registre', 'annee_registre', 'date_mariage', 'lieu_mariage', 'created_at'];
        $sortField = in_array($sortField, $allowedSortFields) ? $sortField : 'created_at';
        $sortDirection = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';

        $whereClause = '';
        $params = [];

        if (!empty($searchTerm)) {
            $whereClause = "WHERE numero_registre LIKE :search 
                OR nom_prenoms_epoux LIKE :search 
                OR nom_prenoms_epouse LIKE :search
                OR date_mariage_lettre LIKE :search";
            $params[':search'] = "%$searchTerm%";
        }

        $countSql = "SELECT COUNT(*) AS total FROM actes_mariage $whereClause";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $dataSql = "
            SELECT * 
            FROM actes_mariage
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
            $actes[] = ActeMariageModel::fromArray($row);
        }

        return [
            'data' => $actes,
            'total' => $total,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    public function create(ActeMariageModel $acte): ActeMariageModel {
        $sql = "
            INSERT INTO actes_mariage (
                numero_registre, annee_registre, date_mariage_lettre, date_mariage,
                lieu_mariage, nom_prenoms_epoux, nom_prenoms_epouse,
                profession_epoux, profession_epouse,
                date_naissance_epoux, date_naissance_epouse,
                nom_pere_epoux, nom_mere_epoux, nom_pere_epouse, nom_mere_epouse,
                temoin_homme, temoin_femme, mention_divorce, create_by, created_at
            ) VALUES (
                :numero_registre, :annee_registre, :date_mariage_lettre, :date_mariage,
                :lieu_mariage, :nom_prenoms_epoux, :nom_prenoms_epouse,
                :profession_epoux, :profession_epouse,
                :date_naissance_epoux, :date_naissance_epouse,
                :nom_pere_epoux, :nom_mere_epoux, :nom_pere_epouse, :nom_mere_epouse,
                :temoin_homme, :temoin_femme, :mention_divorce, :create_by, NOW()
            )
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':numero_registre' => $acte->getNumeroRegistre(),
            ':annee_registre' => $acte->getAnneeRegistre(),
            ':date_mariage_lettre' => $acte->getDateMariageLettre(),
            ':date_mariage' => $acte->getDateMariage(),
            ':lieu_mariage' => $acte->getLieuMariage(),
            ':nom_prenoms_epoux' => $acte->getNomPrenomsEpoux(),
            ':nom_prenoms_epouse' => $acte->getNomPrenomsEpouse(),
            ':profession_epoux' => $acte->getProfessionEpoux(),
            ':profession_epouse' => $acte->getProfessionEpouse(),
            ':date_naissance_epoux' => $acte->getDateNaissanceEpoux(),
            ':date_naissance_epouse' => $acte->getDateNaissanceEpouse(),
            ':nom_pere_epoux' => $acte->getNomPereEpoux(),
            ':nom_mere_epoux' => $acte->getNomMereEpoux(),
            ':nom_pere_epouse' => $acte->getNomPereEpouse(),
            ':nom_mere_epouse' => $acte->getNomMereEpouse(),
            ':temoin_homme' => $acte->getTemoinHomme(),
            ':temoin_femme' => $acte->getTemoinFemme(),
            ':mention_divorce' => $acte->getMentionDivorce(),
            ':create_by' => $acte->getCreateBy()
        ]);

        $acte->setId($this->db->lastInsertId());
        return $acte;
    }

    public function update(ActeMariageModel $acte): bool {
        $sql = "
            UPDATE actes_mariage SET
                numero_registre = :numero_registre,
                annee_registre = :annee_registre,
                date_mariage_lettre = :date_mariage_lettre,
                date_mariage = :date_mariage,
                lieu_mariage = :lieu_mariage,
                nom_prenoms_epoux = :nom_prenoms_epoux,
                nom_prenoms_epouse = :nom_prenoms_epouse,
                profession_epoux = :profession_epoux,
                profession_epouse = :profession_epouse,
                date_naissance_epoux = :date_naissance_epoux,
                date_naissance_epouse = :date_naissance_epouse,
                nom_pere_epoux = :nom_pere_epoux,
                nom_mere_epoux = :nom_mere_epoux,
                nom_pere_epouse = :nom_pere_epouse,
                nom_mere_epouse = :nom_mere_epouse,
                temoin_homme = :temoin_homme,
                temoin_femme = :temoin_femme,
                mention_divorce = :mention_divorce,
                updated_at = NOW()
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $acte->getId(),
            ':numero_registre' => $acte->getNumeroRegistre(),
            ':annee_registre' => $acte->getAnneeRegistre(),
            ':date_mariage_lettre' => $acte->getDateMariageLettre(),
            ':date_mariage' => $acte->getDateMariage(),
            ':lieu_mariage' => $acte->getLieuMariage(),
            ':nom_prenoms_epoux' => $acte->getNomPrenomsEpoux(),
            ':nom_prenoms_epouse' => $acte->getNomPrenomsEpouse(),
            ':profession_epoux' => $acte->getProfessionEpoux(),
            ':profession_epouse' => $acte->getProfessionEpouse(),
            ':date_naissance_epoux' => $acte->getDateNaissanceEpoux(),
            ':date_naissance_epouse' => $acte->getDateNaissanceEpouse(),
            ':nom_pere_epoux' => $acte->getNomPereEpoux(),
            ':nom_mere_epoux' => $acte->getNomMereEpoux(),
            ':nom_pere_epouse' => $acte->getNomPereEpouse(),
            ':nom_mere_epouse' => $acte->getNomMereEpouse(),
            ':temoin_homme' => $acte->getTemoinHomme(),
            ':temoin_femme' => $acte->getTemoinFemme(),
            ':mention_divorce' => $acte->getMentionDivorce()
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM actes_mariage WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}