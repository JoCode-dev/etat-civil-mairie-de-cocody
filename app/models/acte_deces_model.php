<?php

class ActeDecesModel {
    private ?int $id;
    private string $numeroRegistre;
    private int $anneeRegistre;
    private string $nom;
    private string $prenoms;
    private string $dateDecesLettre;
    private string $dateDeces;
    private string $lieuDeces;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(
        ?int $id = null,
        string $numeroRegistre = '',
        int $anneeRegistre = 0,
        string $nom = '',
        string $prenoms = '',
        string $dateDecesLettre = '',
        string $dateDeces = '',
        string $lieuDeces = '',
        string $createdAt = '',
        string $updatedAt = ''
    ) {
        $this->id = $id;
        $this->numeroRegistre = $numeroRegistre;
        $this->anneeRegistre = $anneeRegistre;
        $this->nom = $nom;
        $this->prenoms = $prenoms;
        $this->dateDecesLettre = $dateDecesLettre;
        $this->dateDeces = $dateDeces;
        $this->lieuDeces = $lieuDeces;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNumeroRegistre(): string { return $this->numeroRegistre; }
    public function getAnneeRegistre(): int { return $this->anneeRegistre; }
    public function getNom(): string { return $this->nom; }
    public function getPrenoms(): string { return $this->prenoms; }
    public function getDateDecesLettre(): string { return $this->dateDecesLettre; }
    public function getDateDeces(): string { return $this->dateDeces; }
    public function getLieuDeces(): string { return $this->lieuDeces; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getUpdatedAt(): string { return $this->updatedAt; }

    // Setters (with fluid interface)
    public function setId(?int $id): self { $this->id = $id; return $this; }
    public function setNumeroRegistre(string $numeroRegistre): self { $this->numeroRegistre = $numeroRegistre; return $this; }
    public function setAnneeRegistre(int $anneeRegistre): self { $this->anneeRegistre = $anneeRegistre; return $this; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function setPrenoms(string $prenoms): self { $this->prenoms = $prenoms; return $this; }
    public function setDateDecesLettre(string $dateDecesLettre): self { $this->dateDecesLettre = $dateDecesLettre; return $this; }
    public function setDateDeces(string $dateDeces): self { $this->dateDeces = $dateDeces; return $this; }
    public function setLieuDeces(string $lieuDeces): self { $this->lieuDeces = $lieuDeces; return $this; }
    public function setCreatedAt(string $createdAt): self { $this->createdAt = $createdAt; return $this; }
    public function setUpdatedAt(string $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }

    // Méthodes utilitaires
    public function getNomComplet(): string {
        return $this->prenoms . ' ' . $this->nom;
    }

    public static function fromArray(array $data): self {
        return (new self())
            ->setId($data['id'] ?? null)
            ->setNumeroRegistre($data['numero_registre'] ?? '')
            ->setAnneeRegistre($data['annee_registre'] ?? 0)
            ->setNom($data['nom'] ?? '')
            ->setPrenoms($data['prenoms'] ?? '')
            ->setDateDecesLettre($data['date_deces_lettre'] ?? '')
            ->setDateDeces($data['date_deces'] ?? '')
            ->setLieuDeces($data['lieu_deces'] ?? '')
            ->setCreatedAt($data['created_at'] ?? '')
            ->setUpdatedAt($data['updated_at'] ?? '');
    }
}