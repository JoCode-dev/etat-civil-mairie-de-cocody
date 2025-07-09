<?php

class AdminModel {
    private ?int $id;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $passwordHash;
    private int $roleId;
    private bool $isActive;
    private ?string $lastLogin;
    private string $createdAt;
    private ?string $updatedAt;
    private ?string $roleName; // Utilisation de roleName au lieu d'un objet RoleModel

    public function __construct(
        ?int $id = null,
        string $nom = '',
        string $prenom = '',
        string $email = '',
        string $passwordHash = '',
        int $roleId = 0,
        bool $isActive = true,
        ?string $lastLogin = null,
        string $createdAt = '',
        ?string $updatedAt = null,
        ?string $roleName = null // Ajout de roleName dans le constructeur
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->roleId = $roleId;
        $this->isActive = $isActive;
        $this->lastLogin = $lastLogin;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->roleName = $roleName;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getEmail(): string { return $this->email; }
    public function getPasswordHash(): string { return $this->passwordHash; }
    public function getRoleId(): int { return $this->roleId; }
    public function isActive(): bool { return $this->isActive; }
    public function getLastLogin(): ?string { return $this->lastLogin; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function getRoleName(): ?string { return $this->roleName; } // Getter pour roleName

    // Setters avec fluid interface
    public function setId(?int $id): self { $this->id = $id; return $this; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function setPrenom(string $prenom): self { $this->prenom = $prenom; return $this; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }
    public function setPasswordHash(string $passwordHash): self { $this->passwordHash = $passwordHash; return $this; }
    public function setRoleId(int $roleId): self { $this->roleId = $roleId; return $this; }
    public function setIsActive(bool $isActive): self { $this->isActive = $isActive; return $this; }
    public function setLastLogin(?string $lastLogin): self { $this->lastLogin = $lastLogin; return $this; }
    public function setRoleName(?string $roleName): self { $this->roleName = $roleName; return $this; } // Setter pour roleName

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
            ->setPasswordHash($data['password_hash'] ?? '')
            ->setRoleId($data['role_id'] ?? 0)
            ->setIsActive((bool)($data['is_active'] ?? true))
            ->setLastLogin($data['last_login'] ?? null)
            ->setRoleName($data['role_name'] ?? null); 
    }
}