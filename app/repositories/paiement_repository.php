<?php
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'app/models/paiement_model.php';

class PaiementRepository {
    private PDO $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    /**
     * Trouve une transaction par son ID
     */
    public function findById(int $id): ?PaiementModel {
        $stmt = $this->db->prepare("
            SELECT t.*, 
                   d.reference AS demande_reference,
                   CONCAT(c.prenom, ' ', c.nom) AS citoyen_nom
            FROM paiements t
            LEFT JOIN demandes d ON t.demande_id = d.id
            LEFT JOIN citoyens c ON t.citoyen_id = c.id
            WHERE t.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        
        return ($data = $stmt->fetch(PDO::FETCH_ASSOC)) 
            ? PaiementModel::fromArray($data) 
            : null;
    }

    /**
     * Recherche avancée des paiements avec filtres et pagination
     */
    public function search(
        ?string $searchTerm = null,
        int $page = 1,
        int $perPage = 20,
        string $sortField = 'date_transaction',
        string $sortDirection = 'DESC',
        ?string $statut = null,
        ?int $citoyenId = null,
        ?int $demandeId = null,
        ?string $methodePaiement = null,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ): array {
        // Validation des paramètres
        $allowedSortFields = [
            'date_transaction', 'montant', 'methode_paiement', 
            'statut', 'demande_reference', 'citoyen_nom'
        ];
        $sortField = in_array($sortField, $allowedSortFields) ? $sortField : 'date_transaction';
        $sortDirection = strtoupper($sortDirection) === 'ASC' ? 'ASC' : 'DESC';

        // Construction de la requête
        $whereClauses = [];
        $params = [];
        $joins = "LEFT JOIN demandes d ON t.demande_id = d.id
                  LEFT JOIN citoyens c ON t.citoyen_id = c.id";

        if (!empty($searchTerm)) {
            $whereClauses[] = "(t.reference LIKE :search OR 
                              d.reference LIKE :search OR
                              CONCAT(c.prenom, ' ', c.nom) LIKE :search)";
            $params[':search'] = "%$searchTerm%";
        }

        if ($statut !== null) {
            $whereClauses[] = "t.statut = :statut";
            $params[':statut'] = $statut;
        }

        if ($citoyenId !== null) {
            $whereClauses[] = "t.citoyen_id = :citoyen_id";
            $params[':citoyen_id'] = $citoyenId;
        }

        if ($demandeId !== null) {
            $whereClauses[] = "t.demande_id = :demande_id";
            $params[':demande_id'] = $demandeId;
        }

        if ($methodePaiement !== null) {
            $whereClauses[] = "t.methode_paiement = :methode_paiement";
            $params[':methode_paiement'] = $methodePaiement;
        }

        if ($dateFrom !== null) {
            $whereClauses[] = "t.date_transaction >= :date_from";
            $params[':date_from'] = $dateFrom;
        }

        if ($dateTo !== null) {
            $whereClauses[] = "t.date_transaction <= :date_to";
            $params[':date_to'] = $dateTo . ' 23:59:59';
        }

        $where = $whereClauses ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

        // Comptage total
        $countStmt = $this->db->prepare("
            SELECT COUNT(*) AS total 
            FROM paiements t
            $joins
            $where
        ");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Récupération des données
        $offset = ($page - 1) * $perPage;
        $dataStmt = $this->db->prepare("
            SELECT t.*, 
                   d.reference AS demande_reference,
                   CONCAT(c.prenom, ' ', c.nom) AS citoyen_nom
            FROM paiements t
            $joins
            $where
            ORDER BY $sortField $sortDirection
            LIMIT :limit OFFSET :offset
        ");

        foreach ($params as $key => $value) {
            $dataStmt->bindValue($key, $value);
        }
        $dataStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $dataStmt->execute();

        $paiements = [];
        while ($row = $dataStmt->fetch(PDO::FETCH_ASSOC)) {
            $paiements[] = PaiementModel::fromArray($row);
        }

        return [
            'data' => $paiements,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => max(1, ceil($total / $perPage))
        ];
    }

    /**
     * Crée une nouvelle transaction
     */
    public function create(PaiementModel $transaction): PaiementModel {
        $sql = "
            INSERT INTO paiements (
                reference, demande_id, citoyen_id, montant, 
                methode_paiement, statut
            ) VALUES (
                :reference, :demande_id, :citoyen_id, :montant, 
                :methode_paiement, :statut
            )
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':reference' => $transaction->getReference() ?? $this->generateReference(),
            ':demande_id' => $transaction->getDemandeId(),
            ':citoyen_id' => $transaction->getCitoyenId(),
            ':montant' => $transaction->getMontant(),
            ':methode_paiement' => $transaction->getMethodePaiement(),
            ':statut' => $transaction->getStatut() ?? 'pending',
        ]);

        $transaction->setId($this->db->lastInsertId());
        return $transaction;
    }

    /**
     * Met à jour le statut d'une transaction
     */
    public function updateStatus(int $id, string $status): bool {
        $sql = "
            UPDATE paiements SET
                statut = :statut,
                date_mise_a_jour = NOW()
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':statut' => $status
        ]);
    }

    /**
     * Trouve les paiements d'un citoyen
     */
    public function findByCitoyenId(int $citoyenId, int $limit = 10): array {
        $stmt = $this->db->prepare("
            SELECT t.*, d.reference AS demande_reference
            FROM paiements t
            JOIN demandes d ON t.demande_id = d.id
            WHERE t.citoyen_id = :citoyen_id
            ORDER BY t.date_transaction DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':citoyen_id', $citoyenId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $paiements = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $paiements[] = PaiementModel::fromArray($row);
        }
        return $paiements;
    }

    public function findByDemandeId(int $demandeId): ?PaiementModel {
        $stmt = $this->db->prepare("
            SELECT *FROM paiements 
            WHERE demande_id = :demande_id
            LIMIT 1
        ");
        $stmt->execute([':demande_id' => $demandeId]);
        
        return ($data = $stmt->fetch(PDO::FETCH_ASSOC)) 
            ? PaiementModel::fromArray($data) 
            : null;
    }

    /**
     * Génère une référence de transaction unique
     */
    private function generateReference(): string {
        $prefix = 'TRX-' . date('Ymd') . '-';
        $stmt = $this->db->query("SELECT COUNT(*) FROM paiements WHERE reference LIKE '$prefix%'");
        $count = $stmt->fetchColumn() + 1;
        return $prefix . strtoupper(substr(uniqid(), -6)) . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

     public function statistiques(): array {
        $sql = "
            SELECT 
                COUNT(*) AS total_transactions,
                SUM(CASE WHEN statut = 'success' THEN 1 ELSE 0 END) AS successful_transactions,
                SUM(CASE WHEN statut = 'pending' THEN 1 ELSE 0 END) AS pending_transactions,
                SUM(CASE WHEN statut = 'failed' THEN 1 ELSE 0 END) AS failed_transactions,
                SUM(montant) AS total_amount
            FROM paiements
        ";

        $stmt = $this->db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
     }
}