<?php

class TypeActeModel {
    private ?int $id;
    private string $code;
    private string $libelle;
    private ?string $description;
    private int $delaiTraitement;
    private ?string $fichierPath;
    private float $frais;
    private bool $statut;
    private string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id = null,
        string $code = '', 
        string $libelle = '',
        ?string $description = null,
        int $delaiTraitement = 3,
        float $frais = 0.0,
        bool $statut = true,
        string $createdAt = '',
        ?string $updatedAt = null,
        ?string $fichierPath = null,
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->libelle = $libelle;
        $this->description = $description;
        $this->delaiTraitement = $delaiTraitement;
        $this->frais = $frais;
        $this->statut = $statut;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->fichierPath = $fichierPath;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getCode(): string { return $this->code; }
    public function getLibelle(): string { return $this->libelle; }
    public function getDescription(): ?string { return $this->description; }
    public function getDelaiTraitement(): int { return $this->delaiTraitement; }
    public function getFrais(): float { return $this->frais; }
    public function isStatut(): bool { return $this->statut; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function getFichierPath(): ?string { return $this->fichierPath; }


    // Setters avec fluid interface
    public function setId(?int $id): self { $this->id = $id; return $this; }
    public function setCode(string $code): self { $this->code = $code; return $this; }
    public function setLibelle(string $libelle): self { $this->libelle = $libelle; return $this; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }
    public function setDelaiTraitement(int $delaiTraitement): self { $this->delaiTraitement = $delaiTraitement; return $this; }
    public function setFrais(float $frais): self { $this->frais = $frais; return $this; }
    public function setStatut(bool $statut): self { $this->statut = $statut; return $this; }
    public function setCreatedAt(string $createdAt): self { $this->createdAt = $createdAt; return $this; }
    public function setUpdatedAt(?string $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }
    public function setFichierPath(?string $fichierPath): self { $this->fichierPath = $fichierPath; return $this; }


    // Méthodes utilitaires
    public function isActif(): string {
        return $this->statut ? 'Actif' : 'Inactif';
    }

    public static function fromArray(array $data): self {
        return (new self())
            ->setId($data['id'] ?? null)
            ->setCode($data['code'] ?? '')
            ->setLibelle($data['libelle'] ?? '')
            ->setDescription($data['description'] ?? null)
            ->setFichierPath($data['fichier_path'] ?? null)
            ->setDelaiTraitement($data['delai_traitement'] ?? 3)
            ->setFrais((float)($data['frais'] ?? 0.0))
            ->setStatut((bool)($data['statut'] ?? true))
            ->setCreatedAt($data['created_at'] ?? '')
            ->setUpdatedAt($data['updated_at'] ?? null);
    }
}