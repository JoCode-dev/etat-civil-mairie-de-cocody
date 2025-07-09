<?php

require_once __DIR__ . '/../models/transaction_model.php';

class TransactionHelper {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Créer une transaction
    public function create(TransactionModel $transaction): bool {
        $sql = "INSERT INTO transactions 
                (demande_id, citoyen_id, montant, methode_paiement, reference, statut, donnees_paiement, date_transaction) 
                VALUES (:demande_id, :citoyen_id, :montant, :methode_paiement, :reference, :statut, :donnees_paiement, NOW())";
        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':demande_id', $transaction->getDemandeId(), PDO::PARAM_INT);
        $stmt->bindValue(':citoyen_id', $transaction->getCitoyenId(), PDO::PARAM_INT);
        $stmt->bindValue(':montant', $transaction->getMontant());
        $stmt->bindValue(':methode_paiement', $transaction->getMethodePaiement());
        $stmt->bindValue(':reference', $transaction->getReference());
        $stmt->bindValue(':statut', $transaction->getStatut());
        $stmt->bindValue(':donnees_paiement', json_encode($transaction->getDonneesPaiement()));

        return $stmt->execute();
    }

    // Trouver une transaction par ID
    public function findById(int $id): ?TransactionModel {
        $sql = "SELECT * FROM transactions WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? TransactionModel::fromArray($row) : null;
    }

    // Trouver les transactions d'un citoyen
    public function findByCitoyenId(int $citoyenId): array {
        $sql = "SELECT * FROM transactions WHERE citoyen_id = :citoyen_id ORDER BY date_transaction DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':citoyen_id', $citoyenId, PDO::PARAM_INT);
        $stmt->execute();

        $transactions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $transactions[] = TransactionModel::fromArray($row);
        }

        return $transactions;
    }

    // Mettre à jour le statut d'une transaction
    public function updateStatut(int $id, string $statut): bool {
        $sql = "UPDATE transactions SET statut = :statut, date_mise_a_jour = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':statut', $statut);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Supprimer une transaction
    public function delete(int $id): bool {
        $sql = "DELETE FROM transactions WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Vérifier si une transaction existe
    public function exists(int $id): bool {
        $sql = "SELECT COUNT(*) FROM transactions WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return (bool)$stmt->fetchColumn();
    }
    // Récupérer le montant total des transactions d'un citoyen
    public function getTotalMontantByCitoyenId(int $citoyenId): float {
        $sql = "SELECT SUM(montant) FROM transactions WHERE citoyen_id = :citoyen_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':citoyen_id', $citoyenId, PDO::PARAM_INT);
        $stmt->execute();

        return (float)$stmt->fetchColumn();
    }
}