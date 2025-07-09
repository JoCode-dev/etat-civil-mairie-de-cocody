<?php

class CitoyenModel {
    private ?int $id;
    private string $nom;
    private string $prenom;
    private string $email;
    private ?string $mobile;
    private ?string $adresse;
    private string $passwordHash;
    private ?string $verificationToken;
    private bool $statut;
    private bool $emailVerified;
    private ?string $rememberToken;
    private ?string $rememberTokenExpires;
    private ?string $lastLogin;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(
        ?int $id = null,
        string $nom = '',
        string $prenom = '',
        string $email = '',
        ?string $mobile = null,
        ?string $adresse = null,
        string $passwordHash = '',
        ?string $verificationToken = null,
        bool $statut = true,
        bool $emailVerified = false,
        ?string $rememberToken = null,
        ?string $rememberTokenExpires = null,
        ?string $lastLogin = null,
        string $createdAt = '',
        string $updatedAt = ''
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->mobile = $mobile;
        $this->adresse = $adresse;
        $this->passwordHash = $passwordHash;
        $this->verificationToken = $verificationToken;
        $this->statut = $statut;
        $this->emailVerified = $emailVerified;
        $this->rememberToken = $rememberToken;
        $this->rememberTokenExpires = $rememberTokenExpires;
        $this->lastLogin = $lastLogin;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getEmail(): string { return $this->email; }
    public function getMobile(): ?string { return $this->mobile; }
    public function getAdresse(): ?string { return $this->adresse; }
    public function getPasswordHash(): string { return $this->passwordHash; }
    public function getVerificationToken(): ?string { return $this->verificationToken; }
    public function getStatut(): bool { return $this->statut; }
    public function isEmailVerified(): bool { return $this->emailVerified; }
    public function getRememberToken(): ?string { return $this->rememberToken; }
    public function getRememberTokenExpires(): ?string { return $this->rememberTokenExpires; }
    public function getLastLogin(): ?string { return $this->lastLogin; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getUpdatedAt(): string { return $this->updatedAt; }

    // Setters (with fluid interface)
    public function setId(?int $id): self { $this->id = $id; return $this; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function setPrenom(string $prenom): self { $this->prenom = $prenom; return $this; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }
    public function setMobile(?string $mobile): self { $this->mobile = $mobile; return $this; }
    public function setAdresse(?string $adresse): self { $this->adresse = $adresse; return $this; }
    public function setPasswordHash(string $passwordHash): self { $this->passwordHash = $passwordHash; return $this; }
    public function setVerificationToken(?string $verificationToken): self { $this->verificationToken = $verificationToken; return $this; }
    public function setStatut(bool $statut): self { $this->statut = $statut; return $this; }
    public function setEmailVerified(bool $emailVerified): self { $this->emailVerified = $emailVerified; return $this; }
    public function setRememberToken(?string $rememberToken): self { $this->rememberToken = $rememberToken; return $this; }
    public function setRememberTokenExpires(?string $rememberTokenExpires): self { $this->rememberTokenExpires = $rememberTokenExpires; return $this; }
    public function setLastLogin(?string $lastLogin): self { $this->lastLogin = $lastLogin; return $this; }
    public function setCreatedAt(string $createdAt): self { $this->createdAt = $createdAt; return $this; }
    public function setUpdatedAt(string $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }

    // Méthodes utilitaires
    public function getNomComplet(): string {
        return $this->prenom . ' ' . $this->nom;
    }

    public static function fromArray(array $data): self {
        return (new self())
            ->setId($data['id'] ?? null)
            ->setNom($data['nom'] ?? '')
            ->setPrenom($data['prenom'] ?? '')
            ->setEmail($data['email'] ?? '')
            ->setMobile($data['mobile'] ?? null)
            ->setAdresse($data['adresse'] ?? null)
            ->setPasswordHash($data['password_hash'] ?? '')
            ->setVerificationToken($data['verification_token'] ?? null)
            ->setStatut($data['statut'] ?? true)
            ->setEmailVerified($data['email_verified'] ?? false)
            ->setRememberToken($data['remember_token'] ?? null)
            ->setRememberTokenExpires($data['remember_token_expires'] ?? null)
            ->setLastLogin($data['last_login'] ?? null)
            ->setCreatedAt($data['created_at'] ?? '')
            ->setUpdatedAt($data['updated_at'] ?? '');
    }
}