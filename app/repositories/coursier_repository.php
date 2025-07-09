<?php
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'app/models/coursier_model.php';

/**
 * Classe de gestion des opérations sur les coursiers
 */
class CoursierRepository
{
    private $db;

    // Constructor
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Trouve un coursier par son email
     */
    public function findByEmail(string $email): ?CoursierModel
    {
        $stmt = $this->db->prepare("
            SELECT * FROM coursiers 
            WHERE email = :email 
            LIMIT 1
        ");
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? CoursierModel::fromArray($data) : null;
    }

    /**
     * Trouve un coursier par son ID
     */
    public function findById(int $id): ?CoursierModel
    {
        $stmt = $this->db->prepare("
            SELECT * FROM coursiers 
            WHERE id = :id 
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? CoursierModel::fromArray($data) : null;
    }

    /**
     * Trouve un coursier par son numéro d'identifiant
     */
    public function findByIdentifiant(string $numeroIdentifiant): ?CoursierModel
    {
        $stmt = $this->db->prepare("
            SELECT * FROM coursiers 
            WHERE numero_identifiant = :numero_identifiant 
            LIMIT 1
        ");
        $stmt->execute([':numero_identifiant' => $numeroIdentifiant]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? CoursierModel::fromArray($data) : null;
    }

    /**
     * Crée un nouveau coursier
     */
    public function create(CoursierModel $coursier): CoursierModel
    {
        if (empty($coursier->getNumeroIdentifiant())) {
            $coursier->generateIdentifiant();
        }

        $sql = "
            INSERT INTO coursiers (
                nom, prenom, entreprise, email, telephone, 
                numero_identifiant, moyen_transport, statut,
                password_hash, zone_couverture, date_embauche
            ) VALUES (
                :nom, :prenom, :entreprise, :email, :telephone,
                :numero_identifiant, :moyen_transport, :statut,
                :password_hash, :zone_couverture, :date_embauche
            )
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nom' => $coursier->getNom(),
            ':prenom' => $coursier->getPrenom(),
            ':entreprise' => $coursier->getEntreprise(),
            ':email' => $coursier->getEmail(),
            ':telephone' => $coursier->getTelephone(),
            ':numero_identifiant' => $coursier->getNumeroIdentifiant(),
            ':moyen_transport' => $coursier->getMoyenTransport(),
            ':statut' => $coursier->getStatut(),
            ':password_hash' => $coursier->getPasswordHash(),
            ':zone_couverture' => $coursier->getZoneCouverture(),
            ':date_embauche' => $coursier->getDateEmbauche()
        ]);

        $coursier->setId($this->db->lastInsertId());
        return $coursier;
    }

    /**
     * Met à jour un coursier
     */
    public function update(CoursierModel $coursier): bool
    {
        $sql = "
            UPDATE coursiers SET
                nom = :nom,
                prenom = :prenom,
                entreprise = :entreprise,
                email = :email,
                telephone = :telephone,
                moyen_transport = :moyen_transport,
                statut = :statut,
                zone_couverture = :zone_couverture,
                date_embauche = :date_embauche,
                updated_at = NOW()
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $coursier->getId(),
            ':nom' => $coursier->getNom(),
            ':prenom' => $coursier->getPrenom(),
            ':entreprise' => $coursier->getEntreprise(),
            ':email' => $coursier->getEmail(),
            ':telephone' => $coursier->getTelephone(),
            ':moyen_transport' => $coursier->getMoyenTransport(),
            ':statut' => $coursier->getStatut(),
            ':zone_couverture' => $coursier->getZoneCouverture(),
            ':date_embauche' => $coursier->getDateEmbauche()
        ]);
    }

    /**
     * Met à jour le mot de passe d'un coursier
     */
    public function updatePassword(int $coursierId, string $newPassword): bool
    {
        $sql = "UPDATE coursiers SET password_hash = :password WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $coursierId,
            ':password' => password_hash($newPassword, PASSWORD_BCRYPT)
        ]);
    }

    /**
     * Recherche des coursiers avec pagination
     */
    public function search(
        int $page = 1,
        string $searchTerm = '',
        string $sortField = 'nom',
        int $perPage = 20,
        string $sortDirection = 'ASC',
        ?string $statutFilter = null,
        ?string $transportFilter = null
    ): array {
        // Validation des paramètres de tri
        $allowedSortFields = ['nom', 'prenom', 'email', 'date_embauche', 'statut'];
        $sortField = in_array($sortField, $allowedSortFields) ? $sortField : 'nom';
        $sortDirection = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';

        // Construction de la clause WHERE
        $whereClause = '';
        $params = [];

        if (!empty($searchTerm)) {
            $whereClause = "WHERE nom LIKE :search 
                        OR prenom LIKE :search 
                        OR email LIKE :search 
                        OR telephone LIKE :search
                        OR numero_identifiant LIKE :search";
            $params[':search'] = "%$searchTerm%";
        }

        if (!empty($statutFilter)) {
            $whereClause .= $whereClause ? " AND statut = :statut" : "WHERE statut = :statut";
            $params[':statut'] = $statutFilter;
        }

        if (!empty($transportFilter)) {
            $whereClause .= $whereClause ? " AND moyen_transport = :transport" : "WHERE moyen_transport = :transport";
            $params[':transport'] = $transportFilter;
        }

        // Requête de comptage
        $countSql = "SELECT COUNT(*) AS total FROM coursiers $whereClause";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Requête pour récupérer les données
        $offset = ($page - 1) * $perPage;
        $dataSql = "
            SELECT * FROM coursiers
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
            $data[] = CoursierModel::fromArray($row);
        }

        return [
            'data' => $data,
            'total' => $total,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage),
            'page' => $page
        ];
    }

    /**
     * Enregistre la dernière connexion d'un coursier
     */
    public function recordLogin(int $coursierId): bool
    {
        $sql = "UPDATE coursiers SET last_login = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $coursierId]);
    }

    /**
     * Liste tous les coursiers (avec pagination)
     */
    public function findAll(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;

        $countStmt = $this->db->query("SELECT COUNT(*) FROM coursiers");
        $total = $countStmt->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT * FROM coursiers
            ORDER BY nom ASC
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $coursiers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $coursiers[] = CoursierModel::fromArray($row);
        }

        return [
            'data' => $coursiers,
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
        int $coursierId,
        string $token,
        string $expiryDate
    ): bool
    {
        $sql = "
            UPDATE coursiers 
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
            ':id' => $coursierId
        ]);
    }

    /**
     * Compte le nombre total de coursiers
     */
    public function countAll(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM coursiers");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Compte les coursiers par statut
     */
    public function countByStatut(string $statut): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM coursiers WHERE statut = :statut");
        $stmt->execute([':statut' => $statut]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Trouve un coursier par son token "Se souvenir de moi"
     */
    public function findByRememberToken(string $token): ?CoursierModel
    {
        $sql = "
            SELECT * FROM coursiers 
            WHERE remember_token IS NOT NULL
            AND remember_token_expires > NOW()
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($token, $row['remember_token'])) {
                return CoursierModel::fromArray($row);
            }
        }

        return null;
    }

    /**
     * Supprime un coursier
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM coursiers WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Trouve les coursiers disponibles pour livraison
     */
    public function findAvailableForDelivery(): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM coursiers 
            WHERE statut = 'actif' 
            AND (last_login IS NULL OR last_login > DATE_SUB(NOW(), INTERVAL 1 DAY))
            ORDER BY RAND()
        ");
        $stmt->execute();

        $coursiers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $coursiers[] = CoursierModel::fromArray($row);
        }

        return $coursiers;
    }
}