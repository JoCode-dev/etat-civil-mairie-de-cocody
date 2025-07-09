<?php

require_once __DIR__ . '/../models/demande_model.php';
require_once __DIR__ . '/../models/coursier_model.php';

class DemandeRepository {
    private PDO $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    /**
     * Trouve une demande par son ID avec jointures optionnelles
     */
    public function findById(int $id, bool $withJoins = false): ?DemandeModel {
        $select = "d.*";
        $joins = "";
        
        if ($withJoins) {
            $select .= ", c.nom AS citoyen_nom, ta.libelle AS type_acte_libelle, cr.nom AS coursier_nom";
            $joins = "LEFT JOIN citoyens c ON d.citoyen_id = c.id 
                      LEFT JOIN type_actes ta ON d.type_actes_id = ta.id
                      LEFT JOIN coursiers cr ON d.coursier_id = cr.id";
        }

        $stmt = $this->db->prepare("
            SELECT $select 
            FROM demandes d
            $joins
            WHERE d.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        
        return ($data = $stmt->fetch(PDO::FETCH_ASSOC)) 
            ? DemandeModel::fromArray($data) 
            : null;
    }

    public function findByRef(string $ref, bool $withJoins = false): ?DemandeModel {
        $select = "d.*";
        $joins = "";
        
        if ($withJoins) {
            $select .= ", c.nom AS citoyen_nom, ta.libelle AS type_acte_libelle, cr.nom AS coursier_nom";
            $joins = "LEFT JOIN citoyens c ON d.citoyen_id = c.id 
                      LEFT JOIN type_actes ta ON d.type_actes_id = ta.id
                      LEFT JOIN coursiers cr ON d.coursier_id = cr.id";
        }

        $stmt = $this->db->prepare("
            SELECT $select 
            FROM demandes d
            $joins
            WHERE d.reference = :reference
            LIMIT 1
        ");
        $stmt->execute([':reference' => $ref]);
        
        return ($data = $stmt->fetch(PDO::FETCH_ASSOC)) 
            ? DemandeModel::fromArray($data) 
            : null;
    }

    /**
     * Recherche des demandes avec pagination et filtres avancés
     */
    public function search(
        string $searchTerm = '',
        int $page = 1,
        int $perPage = 20,
        string $sortField = 'date_demande',
        string $sortDirection = 'DESC',
        ?int $citoyenId = null,
        ?string $statutFilter = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?string $methodeLivraison = null,
        ?int $coursierId = null
    ): array {
        // Validation des paramètres de tri
        $allowedSortFields = [
            'date_demande', 'statut', 'total_frais', 
            'reference', 'citoyen_nom', 'acte_libelle',
            'date_livraison_prevue', 'date_livraison_effectue'
        ];
        $sortField = in_array($sortField, $allowedSortFields) ? $sortField : 'date_demande';
        $sortDirection = strtoupper($sortDirection) === 'ASC' ? 'ASC' : 'DESC';

        // Construction de la clause WHERE
        $whereClauses = [];
        $params = [];
        
        if (!empty($searchTerm)) {
            $whereClauses[] = "(d.reference LIKE :search OR 
                              d.acte_libelle LIKE :search OR
                              d.citoyen_nom LIKE :search OR
                              c.nom LIKE :search OR
                              d.numero_suivi LIKE :search)";
            $params[':search'] = "%$searchTerm%";
        }
        
        if (!empty($statutFilter)) {
            $whereClauses[] = "d.statut = :statut";
            $params[':statut'] = $statutFilter;
        }

        if (isset($citoyenId)) {
            $whereClauses[] = "d.citoyen_id = :citoyen_id";
            $params[':citoyen_id'] = $citoyenId;
        }
        
        if (!empty($dateFrom)) {
            $whereClauses[] = "d.date_demande >= :date_from";
            $params[':date_from'] = $dateFrom;
        }
        
        if (!empty($dateTo)) {
            $whereClauses[] = "d.date_demande <= :date_to";
            $params[':date_to'] = $dateTo . ' 23:59:59';
        }

        if (!empty($methodeLivraison)) {
            $whereClauses[] = "d.methode_livraison = :methode_livraison";
            $params[':methode_livraison'] = $methodeLivraison;
        }

        if (!empty($coursierId)) {
            $whereClauses[] = "d.coursier_id = :coursier_id";
            $params[':coursier_id'] = $coursierId;
        }

        $where = $whereClauses ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

        // Requête de comptage
        $countStmt = $this->db->prepare("
            SELECT COUNT(*) AS total 
            FROM demandes d
            LEFT JOIN citoyens c ON d.citoyen_id = c.id
            $where
        ");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Requête pour les données
        $offset = ($page - 1) * $perPage;
        $dataStmt = $this->db->prepare("
            SELECT d.*, ta.libelle AS type_acte_libelle, cr.nom AS coursier_nom
            FROM demandes d
            LEFT JOIN citoyens c ON d.citoyen_id = c.id
            LEFT JOIN type_actes ta ON d.type_actes_id = ta.id
            LEFT JOIN coursiers cr ON d.coursier_id = cr.id
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

        $demandes = [];
        while ($row = $dataStmt->fetch(PDO::FETCH_ASSOC)) {
            $demandes[] = DemandeModel::fromArray($row);
        }

        return [
            'data' => $demandes,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => max(1, ceil($total / $perPage))
        ];
    }

    /**
     * Crée une nouvelle demande
     */
    public function create(DemandeModel $demande): DemandeModel {
        if (empty($demande->getReference())) {
            $demande->generateReference();
        }

        $sql = "
            INSERT INTO demandes (
                reference, citoyen_id, acte_libelle, citoyen_nom,
                numero_actes, type_actes_id, date_demande, statut,
                fichier_path, frais_unitaire, frais_livraison, total_frais, 
                nombreActes, adresse_livraison, methode_livraison
            ) VALUES (
                :reference, :citoyen_id, :acte_libelle, :citoyen_nom,
                :numero_actes, :type_actes_id, :date_demande, :statut,
                :fichier_path, :frais_unitaire, :frais_livraison, :total_frais,
                :nombreActes, :adresse_livraison, :methode_livraison
            )
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':reference' => $demande->getReference(),
            ':citoyen_id' => $demande->getCitoyenId(),
            ':acte_libelle' => $demande->getActeLibelle(),
            ':citoyen_nom' => $demande->getCitoyenNom(),
            ':numero_actes' => $demande->getNumeroActes(),
            ':type_actes_id' => $demande->getTypeActesId(),
            ':date_demande' => $demande->getDateDemande(),
            ':statut' => $demande->getStatut(),
            ':fichier_path' => $demande->getFichierPath(),
            ':frais_unitaire' => $demande->getFraisUnitaire(),
            ':frais_livraison' => $demande->getFraisLivraison(),
            ':total_frais' => $demande->getTotalFrais(),
            ':nombreActes' => $demande->getNombreActes(),
            ':adresse_livraison' => $demande->getAdresseLivraison(),
            ':methode_livraison' => $demande->getMethodeLivraison()
        ]);

        $demande->setId($this->db->lastInsertId());
        return $demande;
    }

    /**
     * Met à jour une demande existante
     */
    public function update(DemandeModel $demande): bool {
        $sql = "
            UPDATE demandes SET
                reference = :reference,
                citoyen_id = :citoyen_id,
                acte_libelle = :acte_libelle,
                citoyen_nom = :citoyen_nom,
                numero_actes = :numero_actes,
                type_actes_id = :type_actes_id,
                statut = :statut,
                fichier_path = :fichier_path,
                frais_unitaire = :frais_unitaire,
                frais_livraison = :frais_livraison,
                total_frais = :total_frais,
                nombreActes = :nombreActes,
                adresse_livraison = :adresse_livraison,
                date_livraison_prevue = :date_livraison_prevue,
                date_livraison_effectue = :date_livraison_effectue,
                coursier_id = :coursier_id,
                numero_suivi = :numero_suivi,
                methode_livraison = :methode_livraison
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $demande->getId(),
            ':reference' => $demande->getReference(),
            ':citoyen_id' => $demande->getCitoyenId(),
            ':acte_libelle' => $demande->getActeLibelle(),
            ':citoyen_nom' => $demande->getCitoyenNom(),
            ':numero_actes' => $demande->getNumeroActes(),
            ':type_actes_id' => $demande->getTypeActesId(),
            ':statut' => $demande->getStatut(),
            ':fichier_path' => $demande->getFichierPath(),
            ':frais_unitaire' => $demande->getFraisUnitaire(),
            ':frais_livraison' => $demande->getFraisLivraison(),
            ':total_frais' => $demande->getTotalFrais(),
            ':nombreActes' => $demande->getNombreActes(),
            ':adresse_livraison' => $demande->getAdresseLivraison(),
            ':date_livraison_prevue' => $demande->getDateLivraisonPrevue(),
            ':date_livraison_effectue' => $demande->getDateLivraisonEffectuee(),
            ':coursier_id' => $demande->getCoursierId(),
            ':numero_suivi' => $demande->getNumeroSuivi(),
            ':methode_livraison' => $demande->getMethodeLivraison()
        ]);
    }

    /**
     * Change le statut d'une demande
     */
    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare("
            UPDATE demandes SET
                statut = :statut
            WHERE id = :id
        ");
        return $stmt->execute([':id' => $id, ':statut' => $status]);
    }

    /**
     * Prépare une demande pour la livraison
     */
    public function preparerLivraison(int $demandeId, int $coursierId, string $dateLivraisonPrevue): bool {
        $numeroSuivi = 'LIV-' . strtoupper(bin2hex(random_bytes(4)));
        
        $stmt = $this->db->prepare("
            UPDATE demandes SET
                statut = 'en_livraison',
                coursier_id = :coursier_id,
                date_livraison_prevue = :date_livraison_prevue,
                numero_suivi = :numero_suivi
            WHERE id = :id
        ");
        
        return $stmt->execute([
            ':id' => $demandeId,
            ':coursier_id' => $coursierId,
            ':date_livraison_prevue' => $dateLivraisonPrevue,
            ':numero_suivi' => $numeroSuivi
        ]);
    }

    /**
     * Confirme la livraison d'une demande
     */
    public function confirmerLivraison(int $demandeId): bool {
        $stmt = $this->db->prepare("
            UPDATE demandes SET
                statut = 'livre',
                date_livraison_effectue = NOW()
            WHERE id = :id AND statut = 'en_livraison'
        ");
        return $stmt->execute([':id' => $demandeId]);
    }

    /**
     * Trouve les demandes par ID de citoyen
     */
    public function findByCitoyenId(int $citoyenId): array {
        $stmt = $this->db->prepare("
            SELECT d.*, ta.libelle AS type_acte_libelle, cr.nom AS coursier_nom
            FROM demandes d
            LEFT JOIN type_actes ta ON d.type_actes_id = ta.id
            LEFT JOIN coursiers cr ON d.coursier_id = cr.id
            WHERE d.citoyen_id = :citoyen_id
            ORDER BY d.date_demande DESC
        ");
        $stmt->execute([':citoyen_id' => $citoyenId]);

        $demandes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $demandes[] = DemandeModel::fromArray($row);
        }
        return $demandes;
    }

    /**
     * Trouve les demandes par ID de coursier
     */
    public function findByCoursierId(int $coursierId, ?string $statut = null): array {
        $where = "d.coursier_id = :coursier_id";
        $params = [':coursier_id' => $coursierId];
        
        if ($statut !== null) {
            $where .= " AND d.statut = :statut";
            $params[':statut'] = $statut;
        }

        $stmt = $this->db->prepare(" 
            SELECT d.*, ta.libelle AS type_acte_libelle, c.nom AS citoyen_nom
            FROM demandes d
            LEFT JOIN type_actes ta ON d.type_actes_id = ta.id
            LEFT JOIN citoyens c ON d.citoyen_id = c.id
            WHERE $where
            ORDER BY d.date_livraison_prevue ASC
        ");
        $stmt->execute($params);

        $demandes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $demandes[] = DemandeModel::fromArray($row);
        }
        return $demandes;
    }


  public function statistiques(?int $idCitoyen = null): array
{   
     $where = '';
     if ($idCitoyen !== null) {
          $where = " WHERE citoyen_id = :citoyen_id";
     }

     $requete = "SELECT 
                 COUNT(*) as total,
                SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                SUM(CASE WHEN statut = 'en_traitement' THEN 1 ELSE 0 END) as en_traitement,
                SUM(CASE WHEN statut = 'pret' THEN 1 ELSE 0 END) as pret,
                SUM(CASE WHEN statut = 'recupere' THEN 1 ELSE 0 END) as recupere,
                SUM(CASE WHEN statut = 'annule' THEN 1 ELSE 0 END) as annule,
                SUM(CASE WHEN statut = 'en_livraison' THEN 1 ELSE 0 END) as en_livraison,
                SUM(CASE WHEN statut = 'livre' THEN 1 ELSE 0 END) as livre
          FROM demandes" . $where;

     $params = [];
     if ($idCitoyen !== null) {
          $params[':citoyen_id'] = $idCitoyen;
     }

     $stmt = $this->db->prepare($requete);
     $stmt->execute($params);
     $result = $stmt->fetch(PDO::FETCH_ASSOC);
     $total = (int) ($result['total'] ?? 0);

     $getPourcentage = fn($val) => $total > 0 ? round(($val / $total) * 100, 2) : 0;

     // Statistiques du jour
     $requeteDay = $requete . ($where ? " AND" : " WHERE") . " DATE(date_demande) >= CURRENT_DATE";
     $stmtDay = $this->db->prepare($requeteDay);
     $stmtDay->execute($params);
     $resultDay = $stmtDay->fetch(PDO::FETCH_ASSOC);

     return [
          'total' => $total,

          // Totaux globaux
          'en_attente' => (int) $result['en_attente'],
          'en_traitement' => (int) $result['en_traitement'],
          'pret' => (int) $result['pret'],
          'recupere' => (int) $result['recupere'],
          'annule' => (int) $result['annule'],
          'en_livraison' => (int) $result['en_livraison'],
          'livre' => (int) $result['livre'],

          // Pourcentages globaux
          'pourcentage_en_attente' => $getPourcentage($result['en_attente']),
          'pourcentage_en_traitement' => $getPourcentage($result['en_traitement']),
          'pourcentage_pret' => $getPourcentage($result['pret']),
          'pourcentage_recupere' => $getPourcentage($result['recupere']),
          'pourcentage_annule' => $getPourcentage($result['annule']),
          'pourcentage_en_livraison' => $getPourcentage($result['en_livraison']),
          'pourcentage_livre' => $getPourcentage($result['livre']),

          // Statistiques du jour
          'du_jour' => [
                'total' => (int) ($resultDay['total'] ?? 0),
                'en_attente' => (int) ($resultDay['en_attente'] ?? 0),
                'en_traitement' => (int) ($resultDay['en_traitement'] ?? 0),
                'pret' => (int) ($resultDay['pret'] ?? 0),
                'recupere' => (int) ($resultDay['recupere'] ?? 0),
                'annule' => (int) ($resultDay['annule'] ?? 0),
                'en_livraison' => (int) ($resultDay['en_livraison'] ?? 0),
                'livre' => (int) ($resultDay['livre'] ?? 0),
          ]
     ];
}



public function statparTypeActe(): array
{
    $query = "
        SELECT 
            ta.id,
            ta.code,
            ta.libelle,
            COUNT(d.id) as nombre_demandes
        FROM 
            type_actes ta
        LEFT JOIN 
            demandes d ON ta.id = d.type_actes_id
        GROUP BY 
            ta.id, ta.code, ta.libelle
        ORDER BY 
            nombre_demandes DESC
    ";
    
    $stmt = $this->db->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}