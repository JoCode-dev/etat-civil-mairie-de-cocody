<?php

class ActeNaissanceModel {
    private ?int $id;
    private string $numeroRegistre;
    private int $anneeRegistre;
    private string $nom;
    private string $prenoms;
    private string $dateNaissanceLettre;
    private string $heureNaissanceLettre;
    private string $dateNaissance;
    private string $heureNaissance;
    private string $lieuNaissance;
    private string $nomPere;
    private ?string $professionPere;
    private string $nomMere;
    private ?string $professionMere;
    private ?string $mentionMariage;
    private ?string $mentionDivorce;
    private ?string $mentionDeces;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(
        ?int $id = null,
        string $numeroRegistre = '',
        int $anneeRegistre = 0,
        string $nom = '',
        string $prenoms = '',
        string $dateNaissanceLettre = '',
        string $heureNaissanceLettre = '',
        string $dateNaissance = '',
        string $heureNaissance = '',
        string $lieuNaissance = '',
        string $nomPere = '',
        ?string $professionPere = null,
        string $nomMere = '',
        ?string $professionMere = null,
        ?string $mentionMariage = null,
        ?string $mentionDivorce = null,
        ?string $mentionDeces = null,
        string $createdAt = '',
        string $updatedAt = ''
    ) {
        $this->id = $id;
        $this->numeroRegistre = $numeroRegistre;
        $this->anneeRegistre = $anneeRegistre;
        $this->nom = $nom;
        $this->prenoms = $prenoms;
        $this->dateNaissanceLettre = $dateNaissanceLettre;
        $this->heureNaissanceLettre = $heureNaissanceLettre;
        $this->dateNaissance = $dateNaissance;
        $this->heureNaissance = $heureNaissance;
        $this->lieuNaissance = $lieuNaissance;
        $this->nomPere = $nomPere;
        $this->professionPere = $professionPere;
        $this->nomMere = $nomMere;
        $this->professionMere = $professionMere;
        $this->mentionMariage = $mentionMariage;
        $this->mentionDivorce = $mentionDivorce;
        $this->mentionDeces = $mentionDeces;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNumeroRegistre(): string { return $this->numeroRegistre; }
    public function getAnneeRegistre(): int { return $this->anneeRegistre; }
    public function getNom(): string { return $this->nom; }
    public function getPrenoms(): string { return $this->prenoms; }
    public function getDateNaissanceLettre(): string { return $this->dateNaissanceLettre; }
    public function getHeureNaissanceLettre(): string { return $this->heureNaissanceLettre; }
    public function getDateNaissance(): string { return $this->dateNaissance; }
    public function getHeureNaissance(): string { return $this->heureNaissance; }
    public function getLieuNaissance(): string { return $this->lieuNaissance; }
    public function getNomPere(): string { return $this->nomPere; }
    public function getProfessionPere(): ?string { return $this->professionPere; }
    public function getNomMere(): string { return $this->nomMere; }
    public function getProfessionMere(): ?string { return $this->professionMere; }
    public function getMentionMariage(): ?string { return $this->mentionMariage; }
    public function getMentionDivorce(): ?string { return $this->mentionDivorce; }
    public function getMentionDeces(): ?string { return $this->mentionDeces; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getUpdatedAt(): string { return $this->updatedAt; }

    // Setters (with fluid interface)
    public function setId(?int $id): self { $this->id = $id; return $this; }
    public function setNumeroRegistre(string $numeroRegistre): self { $this->numeroRegistre = $numeroRegistre; return $this; }
    public function setAnneeRegistre(int $anneeRegistre): self { $this->anneeRegistre = $anneeRegistre; return $this; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function setPrenoms(string $prenoms): self { $this->prenoms = $prenoms; return $this; }
    public function setDateNaissanceLettre(string $dateNaissanceLettre): self { $this->dateNaissanceLettre = $dateNaissanceLettre; return $this; }
    public function setHeureNaissanceLettre(string $heureNaissanceLettre): self { $this->heureNaissanceLettre = $heureNaissanceLettre; return $this; }
    public function setDateNaissance(string $dateNaissance): self { $this->dateNaissance = $dateNaissance; return $this; }
    public function setHeureNaissance(string $heureNaissance): self { $this->heureNaissance = $heureNaissance; return $this; }
    public function setLieuNaissance(string $lieuNaissance): self { $this->lieuNaissance = $lieuNaissance; return $this; }
    public function setNomPere(string $nomPere): self { $this->nomPere = $nomPere; return $this; }
    public function setProfessionPere(?string $professionPere): self { $this->professionPere = $professionPere; return $this; }
    public function setNomMere(string $nomMere): self { $this->nomMere = $nomMere; return $this; }
    public function setProfessionMere(?string $professionMere): self { $this->professionMere = $professionMere; return $this; }
    public function setMentionMariage(?string $mentionMariage): self { $this->mentionMariage = $mentionMariage; return $this; }
    public function setMentionDivorce(?string $mentionDivorce): self { $this->mentionDivorce = $mentionDivorce; return $this; }
    public function setMentionDeces(?string $mentionDeces): self { $this->mentionDeces = $mentionDeces; return $this; }
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
            ->setDateNaissanceLettre($data['date_naissance_lettre'] ?? '')
            ->setHeureNaissanceLettre($data['heure_naissance_lettre'] ?? '')
            ->setDateNaissance($data['date_naissance'] ?? '')
            ->setHeureNaissance($data['heure_naissance'] ?? '')
            ->setLieuNaissance($data['lieu_naissance'] ?? '')
            ->setNomPere($data['nom_pere'] ?? '')
            ->setProfessionPere($data['profession_pere'] ?? null)
            ->setNomMere($data['nom_mere'] ?? '')
            ->setProfessionMere($data['profession_mere'] ?? null)
            ->setMentionMariage($data['mention_mariage'] ?? null)
            ->setMentionDivorce($data['mention_divorce'] ?? null)
            ->setMentionDeces($data['mention_deces'] ?? null)
            ->setCreatedAt($data['created_at'] ?? '')
            ->setUpdatedAt($data['updated_at'] ?? '');
    }
}