<?php

class DemandeStatutHistoriqueModel {
    private int $id;
    private int $demandeId;
    private int $officierId;
    private string $officierNom;
    private string $statut;
    private ?string $commentaire;
    private string $dateModification;

    public function __construct(
        int $id,
        int $demandeId,
        int $officierId,
        string $officierNom,
        string $statut,
        ?string $commentaire,
        string $dateModification
    ) {
        $this->id = $id;
        $this->demandeId = $demandeId;
        $this->officierId = $officierId;
        $this->officierNom = $officierNom;
        $this->statut = $statut;
        $this->commentaire = $commentaire;
        $this->dateModification = $dateModification;
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getDemandeId(): int { return $this->demandeId; }
    public function getOfficierId(): int { return $this->officierId; }
    public function getOfficierNom(): string { return $this->officierNom; }
    public function getStatut(): string { return $this->statut; }
    public function getCommentaire(): ?string { return $this->commentaire; }
    public function getDateModification(): string { return $this->dateModification; }

    // Dans la classe DemandeStatutHistoriqueModel

/**
 * Setters avec typage strict et retour fluent
 */

public function setId(int $id): self {
    $this->id = $id;
    return $this;
}

public function setDemandeId(int $demandeId): self {
    $this->demandeId = $demandeId;
    return $this;
}

public function setOfficierId(int $officierId): self {
    $this->officierId = $officierId;
    return $this;
}

public function setOfficierNom(string $officierNom): self {
    $this->officierNom = $officierNom;
    return $this;
}

public function setStatut(string $statut): self {
    $this->statut = $statut;
    return $this;
}

public function setCommentaire(?string $commentaire): self {
    $this->commentaire = $commentaire;
    return $this;
}

public function setDateModification(string $dateModification): self {
    $this->dateModification = $dateModification;
    return $this;
}

    // Méthode de création depuis un tableau
    public static function fromArray(array $data): self {
        return new self(
            $data['id'],
            $data['demande_id'],
            $data['officier_etat_civil_id'],
            $data['officier_nom'] ?? '',
            $data['statut'],
            $data['commentaire'] ?? null,
            $data['date_modification']
        );
    }

    // Méthode pour obtenir le libellé du statut
    public function getStatutLibelle(): string {
        $statuts = [
            'en_attente' => 'En attente',
            'en_traitement' => 'En traitement',
            'pret' => 'Prêt',
            'recupere' => 'Récupéré',
            'annule' => 'Annulé'
        ];
        return $statuts[$this->statut] ?? $this->statut;
    }
}