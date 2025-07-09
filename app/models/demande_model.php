<?php

class DemandeModel {
    private ?int $id;
    private string $reference;
    private int $citoyenId;
    private string $acteLibelle;
    private string $citoyenNom;
    private string $numeroActes;
    private int $typeActesId;
    private string $dateDemande;
    private string $statut;
    private ?string $fichierPath;
    private float $fraisUnitaire;
    private float $fraisLivraison;
    private float $totalFrais;
    private int $nombreActes;
    private ?string $adresseLivraison;
    private ?string $dateLivraisonPrevue;
    private ?string $dateLivraisonEffectuee;
    private ?int $coursierId;
    private ?string $numeroSuivi;
    private string $methodeLivraison;

    public function __construct(
        ?int $id = null,
        string $reference = '',
        int $citoyenId = 0,
        string $acteLibelle = '',
        string $citoyenNom = '',
        string $numeroActes = '',
        int $typeActesId = 0,
        string $dateDemande = '',
        string $statut = 'en_attente',
        ?string $fichierPath = null,
        float $fraisUnitaire = 0.0,
        float $fraisLivraison = 0.0,
        float $totalFrais = 0.0,
        int $nombreActes = 1,
        ?string $adresseLivraison = null,
        ?string $dateLivraisonPrevue = null,
        ?string $dateLivraisonEffectuee = null,
        ?int $coursierId = null,
        ?string $numeroSuivi = null,
        string $methodeLivraison = 'retrait_guichet'
    ) {
        $this->id = $id;
        $this->reference = $reference;
        $this->citoyenId = $citoyenId;
        $this->acteLibelle = $acteLibelle;
        $this->citoyenNom = $citoyenNom;
        $this->numeroActes = $numeroActes;
        $this->typeActesId = $typeActesId;
        $this->dateDemande = $dateDemande ?: date('Y-m-d H:i:s');
        $this->statut = $statut;
        $this->fichierPath = $fichierPath;
        $this->fraisLivraison = $fraisLivraison;
        $this->fraisUnitaire = $fraisUnitaire;
        $this->totalFrais = $totalFrais;
        $this->nombreActes = $nombreActes;
        $this->adresseLivraison = $adresseLivraison;
        $this->dateLivraisonPrevue = $dateLivraisonPrevue;
        $this->dateLivraisonEffectuee = $dateLivraisonEffectuee;
        $this->coursierId = $coursierId;
        $this->numeroSuivi = $numeroSuivi;
        $this->methodeLivraison = $methodeLivraison;
        
        $this->calculateTotalFrais();
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getReference(): string { return $this->reference; }
    public function getCitoyenId(): int { return $this->citoyenId; }
    public function getActeLibelle(): string { return $this->acteLibelle; }
    public function getCitoyenNom(): string { return $this->citoyenNom; }
    public function getNumeroActes(): string { return $this->numeroActes; }
    public function getTypeActesId(): int { return $this->typeActesId; }
    public function getDateDemande(): string { return $this->dateDemande; }
    public function getStatut(): string { return $this->statut; }
    public function getFichierPath(): ?string { return $this->fichierPath; }
    public function getFraisLivraison(): float { return $this->fraisLivraison; }
    public function getFraisUnitaire(): float { return $this->fraisUnitaire; }
    public function getTotalFrais(): float { return $this->totalFrais; }
    public function getNombreActes(): int { return $this->nombreActes; }
    public function getAdresseLivraison(): ?string { return $this->adresseLivraison; }
    public function getDateLivraisonPrevue(): ?string { return $this->dateLivraisonPrevue; }
    public function getDateLivraisonEffectuee(): ?string { return $this->dateLivraisonEffectuee; }
    public function getCoursierId(): ?int { return $this->coursierId; }
    public function getNumeroSuivi(): ?string { return $this->numeroSuivi; }
    public function getMethodeLivraison(): string { return $this->methodeLivraison; }

    // Setters avec fluid interface
    public function setId(?int $id): self { $this->id = $id; return $this; }
    public function setReference(string $reference): self { $this->reference = $reference; return $this; }
    public function setCitoyenId(int $citoyenId): self { $this->citoyenId = $citoyenId; return $this; }
    public function setActeLibelle(string $acteLibelle): self { $this->acteLibelle = $acteLibelle; return $this; }
    public function setCitoyenNom(string $citoyenNom): self { $this->citoyenNom = $citoyenNom; return $this; }
    public function setNumeroActes(string $numeroActes): self { $this->numeroActes = $numeroActes; return $this; }
    public function setTypeActesId(int $typeActesId): self { $this->typeActesId = $typeActesId; return $this; }
    public function setDateDemande(string $dateDemande): self { $this->dateDemande = $dateDemande; return $this; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }
    public function setFichierPath(?string $fichierPath): self { $this->fichierPath = $fichierPath; return $this; }
    public function setFraisLivraison(float $fraisLivraison): self { 
        $this->fraisLivraison = $fraisLivraison;
        $this->calculateTotalFrais();
        return $this; 
    }
    public function setFraisUnitaire(float $fraisUnitaire): self { 
        $this->fraisUnitaire = $fraisUnitaire;
        $this->calculateTotalFrais();
        return $this; 
    }
    public function setNombreActes(int $nombreActes): self { 
        $this->nombreActes = $nombreActes;
        $this->calculateTotalFrais();
        return $this; 
    }
    public function setAdresseLivraison(?string $adresseLivraison): self { 
        $this->adresseLivraison = $adresseLivraison;
        return $this; 
    }
    public function setDateLivraisonPrevue(?string $dateLivraisonPrevue): self { 
        $this->dateLivraisonPrevue = $dateLivraisonPrevue;
        return $this; 
    }
    public function setDateLivraisonEffectuee(?string $dateLivraisonEffectuee): self { 
        $this->dateLivraisonEffectuee = $dateLivraisonEffectuee;
        return $this; 
    }
    public function setCoursierId(?int $coursierId): self { 
        $this->coursierId = $coursierId;
        return $this; 
    }
    public function setNumeroSuivi(?string $numeroSuivi): self { 
        $this->numeroSuivi = $numeroSuivi;
        return $this; 
    }
    public function setMethodeLivraison(string $methodeLivraison): self { 
        $this->methodeLivraison = $methodeLivraison;
        return $this; 
    }

    // Méthodes utilitaires
    public function getStatutLibelle(): string {
        $statuts = [
            'en_attente' => 'En attente',
            'en_traitement' => 'En traitement',
            'pret' => 'Prêt',
            'en_livraison' => 'En livraison',
            'livre' => 'Livré',
            'recupere' => 'Récupéré',
            'annule' => 'Annulé'
        ];
        return $statuts[$this->statut] ?? $this->statut;
    }

    public function isAnnule(): bool {
        return $this->statut === 'annule';
    }

    public function isPret(): bool {
        return $this->statut === 'pret';
    }

    public function isEnAttente(): bool {
        return $this->statut === 'en_attente';
    }

    public function isEnLivraison(): bool {
        return $this->statut === 'en_livraison';
    }

    public function isLivre(): bool {
        return $this->statut === 'livre';
    }

    private function calculateTotalFrais(): void {
        $this->totalFrais = ($this->fraisUnitaire * $this->nombreActes) + $this->fraisLivraison;
    }

    public static function fromArray(array $data): self {
        return new self(
            $data['id'] ?? null,
            $data['reference'] ?? '',
            $data['citoyen_id'] ?? 0,
            $data['acte_libelle'] ?? '',
            $data['citoyen_nom'] ?? '',
            $data['numero_actes'] ?? '',
            $data['type_actes_id'] ?? 0,
            $data['date_demande'] ?? '',
            $data['statut'] ?? 'en_attente',
            $data['fichier_path'] ?? null,
            (float)($data['frais_unitaire'] ?? 0.0),
            (float)($data['frais_livraison'] ?? 0.0),
            (float)($data['total_frais'] ?? 0.0),
            (int)($data['nombreActes'] ?? 1),
            $data['adresse_livraison'] ?? null,
            $data['date_livraison_prevue'] ?? null,
            $data['date_livraison_effectue'] ?? null,
            $data['coursier_id'] ?? null,
            $data['numero_suivi'] ?? null,
            $data['methode_livraison'] ?? 'retrait_guichet'
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'citoyen_id' => $this->citoyenId,
            'acte_libelle' => $this->acteLibelle,
            'citoyen_nom' => $this->citoyenNom,
            'numero_actes' => $this->numeroActes,
            'type_actes_id' => $this->typeActesId,
            'date_demande' => $this->dateDemande,
            'statut' => $this->statut,
            'fichier_path' => $this->fichierPath,
            'frais_unitaire' => $this->fraisUnitaire,
            'frais_livraison' => $this->fraisLivraison,
            'total_frais' => $this->totalFrais,
            'nombreActes' => $this->nombreActes,
            'adresse_livraison' => $this->adresseLivraison,
            'date_livraison_prevue' => $this->dateLivraisonPrevue,
            'date_livraison_effectue' => $this->dateLivraisonEffectuee,
            'coursier_id' => $this->coursierId,
            'numero_suivi' => $this->numeroSuivi,
            'methode_livraison' => $this->methodeLivraison
        ];
    }

    public function generateReference(): self {
        $prefix = strtoupper(substr($this->acteLibelle, 0, 3));
        $date = date('Ymd');
        $random = bin2hex(random_bytes(2));
        $this->reference = "DEM-{$prefix}-{$date}-{$random}";
        return $this;
    }

    public function preparerPourLivraison(int $coursierId, string $dateLivraisonPrevue): self {
        $this->statut = 'en_livraison';
        $this->coursierId = $coursierId;
        $this->dateLivraisonPrevue = $dateLivraisonPrevue;
        $this->numeroSuivi = 'LIV-' . strtoupper(bin2hex(random_bytes(4)));
        return $this;
    }

    public function confirmerLivraison(): self {
        $this->statut = 'livre';
        $this->dateLivraisonEffectuee = date('Y-m-d H:i:s');
        return $this;
    }
}