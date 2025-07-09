<?php

class ActeMariageModel {
    private ?int $id;
    private string $numeroRegistre;
    private int $anneeRegistre;
    private string $dateMariageLettre;
    private string $dateMariage;
    private string $lieuMariage;
    private string $nomPrenomsEpoux;
    private string $dateNaissanceEpoux;
    private ?string $professionEpoux;
    private string $nomPereEpoux;
    private string $nomMereEpoux;
    private string $nomPrenomsEpouse;
    private ?string $professionEpouse;
    private string $dateNaissanceEpouse;
    private string $nomPereEpouse;
    private string $nomMereEpouse;
    private string $temoinHomme;
    private string $temoinFemme;
    private ?string $mentionDivorce;
    private string $createBy;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(
        ?int $id = null,
        string $numeroRegistre = '',
        int $anneeRegistre = 0,
        string $dateMariageLettre = '',
        string $dateMariage = '',
        string $lieuMariage = '',
        string $nomPrenomsEpoux = '',
        string $dateNaissanceEpoux = '',
        ?string $professionEpoux = null,
        string $nomPereEpoux = '',
        string $nomMereEpoux = '',
        string $nomPrenomsEpouse = '',
        ?string $professionEpouse = null,
        string $dateNaissanceEpouse = '',
        string $nomPereEpouse = '',
        string $nomMereEpouse = '',
        string $temoinHomme = '',
        string $temoinFemme = '',
        ?string $mentionDivorce = null,
        string $createBy = '',
        string $createdAt = '',
        string $updatedAt = ''
    ) {
        $this->id = $id;
        $this->numeroRegistre = $numeroRegistre;
        $this->anneeRegistre = $anneeRegistre;
        $this->dateMariageLettre = $dateMariageLettre;
        $this->dateMariage = $dateMariage;
        $this->lieuMariage = $lieuMariage;
        $this->nomPrenomsEpoux = $nomPrenomsEpoux;
        $this->dateNaissanceEpoux = $dateNaissanceEpoux;
        $this->professionEpoux = $professionEpoux;
        $this->nomPereEpoux = $nomPereEpoux;
        $this->nomMereEpoux = $nomMereEpoux;
        $this->nomPrenomsEpouse = $nomPrenomsEpouse;
        $this->professionEpouse = $professionEpouse;
        $this->dateNaissanceEpouse = $dateNaissanceEpouse;
        $this->nomPereEpouse = $nomPereEpouse;
        $this->nomMereEpouse = $nomMereEpouse;
        $this->temoinHomme = $temoinHomme;
        $this->temoinFemme = $temoinFemme;
        $this->mentionDivorce = $mentionDivorce;
        $this->createBy = $createBy;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNumeroRegistre(): string { return $this->numeroRegistre; }
    public function getAnneeRegistre(): int { return $this->anneeRegistre; }
    public function getDateMariageLettre(): string { return $this->dateMariageLettre; }
    public function getDateMariage(): string { return $this->dateMariage; }
    public function getLieuMariage(): string { return $this->lieuMariage; }
    public function getNomPrenomsEpoux(): string { return $this->nomPrenomsEpoux; }
    public function getDateNaissanceEpoux(): string { return $this->dateNaissanceEpoux; }
    public function getProfessionEpoux(): ?string { return $this->professionEpoux; }
    public function getNomPereEpoux(): string { return $this->nomPereEpoux; }
    public function getNomMereEpoux(): string { return $this->nomMereEpoux; }
    public function getNomPrenomsEpouse(): string { return $this->nomPrenomsEpouse; }
    public function getProfessionEpouse(): ?string { return $this->professionEpouse; }
    public function getDateNaissanceEpouse(): string { return $this->dateNaissanceEpouse; }
    public function getNomPereEpouse(): string { return $this->nomPereEpouse; }
    public function getNomMereEpouse(): string { return $this->nomMereEpouse; }
    public function getTemoinHomme(): string { return $this->temoinHomme; }
    public function getTemoinFemme(): string { return $this->temoinFemme; }
    public function getMentionDivorce(): ?string { return $this->mentionDivorce; }
    public function getCreateBy(): string { return $this->createBy; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getUpdatedAt(): string { return $this->updatedAt; }

    // Setters (fluid interface)
    public function setId(?int $id): self { $this->id = $id; return $this; }
    public function setNumeroRegistre(string $numeroRegistre): self { $this->numeroRegistre = $numeroRegistre; return $this; }
    public function setAnneeRegistre(int $anneeRegistre): self { $this->anneeRegistre = $anneeRegistre; return $this; }
    public function setDateMariageLettre(string $dateMariageLettre): self { $this->dateMariageLettre = $dateMariageLettre; return $this; }
    public function setDateMariage(string $dateMariage): self { $this->dateMariage = $dateMariage; return $this; }
    public function setLieuMariage(string $lieuMariage): self { $this->lieuMariage = $lieuMariage; return $this; }
    public function setNomPrenomsEpoux(string $nomPrenomsEpoux): self { $this->nomPrenomsEpoux = $nomPrenomsEpoux; return $this; }
    public function setDateNaissanceEpoux(string $dateNaissanceEpoux): self { $this->dateNaissanceEpoux = $dateNaissanceEpoux; return $this; }
    public function setProfessionEpoux(?string $professionEpoux): self { $this->professionEpoux = $professionEpoux; return $this; }
    public function setNomPereEpoux(string $nomPereEpoux): self { $this->nomPereEpoux = $nomPereEpoux; return $this; }
    public function setNomMereEpoux(string $nomMereEpoux): self { $this->nomMereEpoux = $nomMereEpoux; return $this; }
    public function setNomPrenomsEpouse(string $nomPrenomsEpouse): self { $this->nomPrenomsEpouse = $nomPrenomsEpouse; return $this; }
    public function setProfessionEpouse(?string $professionEpouse): self { $this->professionEpouse = $professionEpouse; return $this; }
    public function setDateNaissanceEpouse(string $dateNaissanceEpouse): self { $this->dateNaissanceEpouse = $dateNaissanceEpouse; return $this; }
    public function setNomPereEpouse(string $nomPereEpouse): self { $this->nomPereEpouse = $nomPereEpouse; return $this; }
    public function setNomMereEpouse(string $nomMereEpouse): self { $this->nomMereEpouse = $nomMereEpouse; return $this; }
    public function setTemoinHomme(string $temoinHomme): self { $this->temoinHomme = $temoinHomme; return $this; }
    public function setTemoinFemme(string $temoinFemme): self { $this->temoinFemme = $temoinFemme; return $this; }
    public function setMentionDivorce(?string $mentionDivorce): self { $this->mentionDivorce = $mentionDivorce; return $this; }
    public function setCreateBy(string $createBy): self { $this->createBy = $createBy; return $this; }
    public function setCreatedAt(string $createdAt): self { $this->createdAt = $createdAt; return $this; }
    public function setUpdatedAt(string $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }

    public static function fromArray(array $data): self {
        return (new self())
            ->setId($data['id'] ?? null)
            ->setNumeroRegistre($data['numero_registre'] ?? '')
            ->setAnneeRegistre($data['annee_registre'] ?? date('Y'))
            ->setDateMariageLettre($data['date_mariage_lettre'] ?? '')
            ->setDateMariage($data['date_mariage'] ?? '')
            ->setLieuMariage($data['lieu_mariage'] ?? '')
            ->setNomPrenomsEpoux($data['nom_prenoms_epoux'] ?? '')
            ->setDateNaissanceEpoux($data['date_naissance_epoux'] ?? '')
            ->setProfessionEpoux($data['profession_epoux'] ?? null)
            ->setNomPereEpoux($data['nom_pere_epoux'] ?? '')
            ->setNomMereEpoux($data['nom_mere_epoux'] ?? '')
            ->setNomPrenomsEpouse($data['nom_prenoms_epouse'] ?? '')
            ->setProfessionEpouse($data['profession_epouse'] ?? null)
            ->setDateNaissanceEpouse($data['date_naissance_epouse'] ?? '')
            ->setNomPereEpouse($data['nom_pere_epouse'] ?? '')
            ->setNomMereEpouse($data['nom_mere_epouse'] ?? '')
            ->setTemoinHomme($data['temoin_homme'] ?? '')
            ->setTemoinFemme($data['temoin_femme'] ?? '')
            ->setMentionDivorce($data['mention_divorce'] ?? null)
            ->setCreateBy($data['create_by'] ?? '')
            ->setCreatedAt($data['created_at'] ?? '')
            ->setUpdatedAt($data['updated_at'] ?? '');
    }
}