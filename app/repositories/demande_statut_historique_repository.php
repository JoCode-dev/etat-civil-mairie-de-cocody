<?php
require_once __DIR__ . '/../models/demande_statut_historique_model.php';

class DemandeStatutHistoriqueRepository {
    private PDO $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    /**
     * Crée une nouvelle entrée dans l'historique des statuts
     */
    public function create(int $demandeId, int $officierId, string $statut, ?string $commentaire = null): DemandeStatutHistoriqueModel {
        $sql = "
            INSERT INTO demande_statut_historique (
                demande_id, 
                officier_etat_civil_id, 
                statut, 
                commentaire
            ) VALUES (
                :demande_id, 
                :officier_id, 
                :statut, 
                :commentaire
            )
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':demande_id' => $demandeId,
            ':officier_id' => $officierId,
            ':statut' => $statut,
            ':commentaire' => $commentaire
        ]);

        return $this->findById($this->db->lastInsertId());
    }

    /**
     * Récupère un historique par son ID
     */
    public function findById(int $id): ?DemandeStatutHistoriqueModel {
        $sql = "
            SELECT h.*, 
                   CONCAT(o.prenom, ' ', o.nom) AS officier_nom
            FROM demande_statut_historique h
            JOIN administrateurs o ON h.officier_etat_civil_id = o.id
            WHERE h.id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? DemandeStatutHistoriqueModel::fromArray($data) : null;
    }

    /**
     * Récupère l'historique complet d'une demande
     */
    public function getHistoriqueByDemandeId(int $demandeId): array {
        $sql = "
            SELECT h.*, 
                   CONCAT(o.prenom, ' ', o.nom) AS officier_nom,
                   CASE 
                     WHEN h.statut = 'en_attente' THEN 'En attente'
                     WHEN h.statut = 'en_traitement' THEN 'En traitement'
                     WHEN h.statut = 'pret' THEN 'Prêt'
                     WHEN h.statut = 'recupere' THEN 'Récupéré'
                     WHEN h.statut = 'annule' THEN 'Annulé'
                     ELSE h.statut
                   END AS statut_libelle
            FROM demande_statut_historique h
            JOIN administrateurs o ON h.officier_etat_civil_id = o.id
            WHERE h.demande_id = :demande_id
            ORDER BY h.date_modification DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':demande_id' => $demandeId]);

        $historique = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $historique[] = DemandeStatutHistoriqueModel::fromArray($row);
        }
        return $historique;
    }

    /**
     * Récupère le dernier statut d'une demande
     */
    public function getCurrentStatut(int $demandeId): ?DemandeStatutHistoriqueModel {
        $sql = "
            SELECT h.*, 
                   CONCAT(o.prenom, ' ', o.nom) AS officier_nom
            FROM demande_statut_historique h
            JOIN administrateurs o ON h.officier_etat_civil_id = o.id
            WHERE h.demande_id = :demande_id
            ORDER BY h.date_modification DESC
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':demande_id' => $demandeId]);
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? DemandeStatutHistoriqueModel::fromArray($data) : null;
    }

    /**
     * Met à jour un commentaire dans l'historique
     */
    public function updateCommentaire(int $id, string $commentaire): bool {
        $sql = "
            UPDATE demande_statut_historique 
            SET commentaire = :commentaire
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':commentaire' => $commentaire
        ]);
    }

    /**
     * Supprime une entrée d'historique
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM demande_statut_historique WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Vérifie si un statut existe déjà pour une demande
     */
    public function statutExists(int $demandeId, string $statut): bool {
        $sql = "
            SELECT COUNT(*) 
            FROM demande_statut_historique 
            WHERE demande_id = :demande_id 
            AND statut = :statut
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':demande_id' => $demandeId,
            ':statut' => $statut
        ]);

        return (bool) $stmt->fetchColumn();
    }
}