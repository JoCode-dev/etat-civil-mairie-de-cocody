<?php

class PaiementModel {
    private ?int $id;
    private int $demandeId;
    private int $citoyenId;
    private float $montant;
    private string $methodePaiement;
    private string $reference;
    private string $statut;
    private string $dateTransaction;
    private ?string $dateMiseAJour;
    private ?array $donneesPaiement;

    public function __construct(
        ?int $id = null,
        int $demandeId = 0,
        int $citoyenId = 0,
        float $montant = 0.0,
        string $methodePaiement = 'autre',
        string $reference = '',
        string $statut = 'en_attente',
        string $dateTransaction = '',
        ?string $dateMiseAJour = null
    ) {
        $this->id = $id;
        $this->demandeId = $demandeId;
        $this->citoyenId = $citoyenId;
        $this->montant = $montant;
        $this->methodePaiement = $methodePaiement;
        $this->reference = $reference;
        $this->statut = $statut;
        $this->dateTransaction = $dateTransaction;
        $this->dateMiseAJour = $dateMiseAJour;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getDemandeId(): int { return $this->demandeId; }
    public function getCitoyenId(): int { return $this->citoyenId; }
    public function getMontant(): float { return $this->montant; }
    public function getMethodePaiement(): string { return $this->methodePaiement; }
    public function getReference(): string { return $this->reference; }
    public function getStatut(): string { return $this->statut; }
    public function getDateTransaction(): string { return $this->dateTransaction; }
    public function getDateMiseAJour(): ?string { return $this->dateMiseAJour; }

    // Setters avec fluid interface
    public function setId(?int $id): self { $this->id = $id; return $this; }
    public function setDemandeId(int $demandeId): self { $this->demandeId = $demandeId; return $this; }
    public function setCitoyenId(int $citoyenId): self { $this->citoyenId = $citoyenId; return $this; }
    public function setMontant(float $montant): self { $this->montant = $montant; return $this; }
    public function setMethodePaiement(string $methodePaiement): self { $this->methodePaiement = $methodePaiement; return $this; }
    public function setReference(string $reference): self { $this->reference = $reference; return $this; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }
    public function setDateTransaction(string $dateTransaction): self { $this->dateTransaction = $dateTransaction; return $this; }
    public function setDateMiseAJour(?string $dateMiseAJour): self { $this->dateMiseAJour = $dateMiseAJour; return $this; }

    // Méthodes utilitaires
    public function getMontantFormatte(): string {
        return number_format($this->montant, 2, ',', ' ') . ' FCFA';
    }

    public static function fromArray(array $data): self {
        return (new self())
            ->setId($data['id'] ?? null)
            ->setDemandeId($data['demande_id'] ?? 0)
            ->setCitoyenId($data['citoyen_id'] ?? 0)
            ->setMontant((float)($data['montant'] ?? 0.0))
            ->setMethodePaiement($data['methode_paiement'] ?? 'autre')
            ->setReference($data['reference'] ?? '')
            ->setStatut($data['statut'] ?? 'en_attente')
            ->setDateTransaction($data['date_transaction'] ?? '')
            ->setDateMiseAJour($data['date_mise_a_jour'] ?? null);
    }
}