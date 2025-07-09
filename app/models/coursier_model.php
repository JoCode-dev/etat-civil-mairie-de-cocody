<?php

class CoursierModel {
    private ?int $id;
    private string $nom;
    private string $prenom;
    private ?string $entreprise;
    private string $telephone;
    private ?string $email;
    private string $numeroIdentifiant;
    private string $moyenTransport;
    private string $statut;
    private string $passwordHash;
    private ?string $salt;
    private ?string $lastLogin;
    private ?string $resetToken;
    private ?string $resetTokenExpires;
    private ?string $zoneCouverture;
    private ?string $dateEmbauche;
    private ?string $notes;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(
        ?int $id = null,
        string $nom = '',
        string $prenom = '',
        ?string $entreprise = null,
        string $telephone = '',
        ?string $email = null,
        string $numeroIdentifiant = '',
        string $moyenTransport = 'moto',
        string $statut = 'actif',
        string $passwordHash = '',
        ?string $salt = null,
        ?string $lastLogin = null,
        ?string $resetToken = null,
        ?string $resetTokenExpires = null,
        ?string $zoneCouverture = null,
        ?string $dateEmbauche = null,
        ?string $notes = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->entreprise = $entreprise;
        $this->telephone = $telephone;
        $this->email = $email;
        $this->numeroIdentifiant = $numeroIdentifiant;
        $this->moyenTransport = $moyenTransport;
        $this->statut = $statut;
        $this->passwordHash = $passwordHash;
        $this->salt = $salt;
        $this->lastLogin = $lastLogin;
        $this->resetToken = $resetToken;
        $this->resetTokenExpires = $resetTokenExpires;
        $this->zoneCouverture = $zoneCouverture;
        $this->dateEmbauche = $dateEmbauche;
        $this->notes = $notes;
        $this->createdAt = $createdAt ?: date('Y-m-d H:i:s');
        $this->updatedAt = $updatedAt ?: date('Y-m-d H:i:s');
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getEntreprise(): ?string { return $this->entreprise; }
    public function getTelephone(): string { return $this->telephone; }
    public function getEmail(): ?string { return $this->email; }
    public function getNumeroIdentifiant(): string { return $this->numeroIdentifiant; }
    public function getMoyenTransport(): string { return $this->moyenTransport; }
    public function getStatut(): string { return $this->statut; }
    public function getPasswordHash(): string { return $this->passwordHash; }
    public function getSalt(): ?string { return $this->salt; }
    public function getLastLogin(): ?string { return $this->lastLogin; }
    public function getResetToken(): ?string { return $this->resetToken; }
    public function getResetTokenExpires(): ?string { return $this->resetTokenExpires; }
    public function getZoneCouverture(): ?string { return $this->zoneCouverture; }
    public function getDateEmbauche(): ?string { return $this->dateEmbauche; }
    public function getNotes(): ?string { return $this->notes; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getUpdatedAt(): string { return $this->updatedAt; }

    // Setters avec fluid interface
    public function setId(?int $id): self { $this->id = $id; return $this; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function setPrenom(string $prenom): self { $this->prenom = $prenom; return $this; }
    public function setEntreprise(?string $entreprise): self { $this->entreprise = $entreprise; return $this; }
    public function setTelephone(string $telephone): self { $this->telephone = $telephone; return $this; }
    public function setEmail(?string $email): self { $this->email = $email; return $this; }
    public function setNumeroIdentifiant(string $numeroIdentifiant): self { $this->numeroIdentifiant = $numeroIdentifiant; return $this; }
    public function setMoyenTransport(string $moyenTransport): self { $this->moyenTransport = $moyenTransport; return $this; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }
    public function setPasswordHash(string $passwordHash): self { $this->passwordHash = $passwordHash; return $this; }
    public function setSalt(?string $salt): self { $this->salt = $salt; return $this; }
    public function setLastLogin(?string $lastLogin): self { $this->lastLogin = $lastLogin; return $this; }
    public function setResetToken(?string $resetToken): self { $this->resetToken = $resetToken; return $this; }
    public function setResetTokenExpires(?string $resetTokenExpires): self { $this->resetTokenExpires = $resetTokenExpires; return $this; }
    public function setZoneCouverture(?string $zoneCouverture): self { $this->zoneCouverture = $zoneCouverture; return $this; }
    public function setDateEmbauche(?string $dateEmbauche): self { $this->dateEmbauche = $dateEmbauche; return $this; }
    public function setNotes(?string $notes): self { $this->notes = $notes; return $this; }

    // Méthodes utilitaires
    public function getNomComplet(): string {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getTransportLibelle(): string {
        $transports = [
            'moto' => 'Moto',
            'voiture' => 'Voiture',
            'velo' => 'Vélo',
            'camionnette' => 'Camionnette'
        ];
        return $transports[$this->moyenTransport] ?? $this->moyenTransport;
    }

    public function getStatutLibelle(): string {
        $statuts = [
            'actif' => 'Actif',
            'inactif' => 'Inactif',
            'en_congé' => 'En congé',
            'en_livraison' => 'En livraison'
        ];
        return $statuts[$this->statut] ?? $this->statut;
    }

    public function isActif(): bool {
        return $this->statut === 'actif';
    }

    public function isEnLivraison(): bool {
        return $this->statut === 'en_livraison';
    }

    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->passwordHash);
    }

    public function hashPassword(string $password): self {
        $this->passwordHash = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }

    public function generateResetToken(): self {
        $this->resetToken = bin2hex(random_bytes(32));
        $this->resetTokenExpires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        return $this;
    }

    public function clearResetToken(): self {
        $this->resetToken = null;
        $this->resetTokenExpires = null;
        return $this;
    }

    public function recordLogin(): self {
        $this->lastLogin = date('Y-m-d H:i:s');
        return $this;
    }

    public static function fromArray(array $data): self {
        return new self(
            $data['id'] ?? null,
            $data['nom'] ?? '',
            $data['prenom'] ?? '',
            $data['entreprise'] ?? null,
            $data['telephone'] ?? '',
            $data['email'] ?? null,
            $data['numero_identifiant'] ?? '',
            $data['moyen_transport'] ?? 'moto',
            $data['statut'] ?? 'actif',
            $data['password_hash'] ?? '',
            $data['salt'] ?? null,
            $data['last_login'] ?? null,
            $data['reset_token'] ?? null,
            $data['reset_token_expires'] ?? null,
            $data['zone_couverture'] ?? null,
            $data['date_embauche'] ?? null,
            $data['notes'] ?? null,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null
        );
    }

    public function toArray(bool $includeSensitive = false): array {
        $data = [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'entreprise' => $this->entreprise,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'numero_identifiant' => $this->numeroIdentifiant,
            'moyen_transport' => $this->moyenTransport,
            'statut' => $this->statut,
            'zone_couverture' => $this->zoneCouverture,
            'date_embauche' => $this->dateEmbauche,
            'notes' => $this->notes,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'nom_complet' => $this->getNomComplet(),
            'transport_libelle' => $this->getTransportLibelle(),
            'statut_libelle' => $this->getStatutLibelle()
        ];

        if ($includeSensitive) {
            $data['password_hash'] = $this->passwordHash;
            $data['salt'] = $this->salt;
            $data['last_login'] = $this->lastLogin;
            $data['reset_token'] = $this->resetToken;
            $data['reset_token_expires'] = $this->resetTokenExpires;
        }

        return $data;
    }

    public function generateIdentifiant(): self {
        $prefix = strtoupper(substr($this->prenom, 0, 1)) . strtoupper(substr($this->nom, 0, 1));
        $date = date('Ymd');
        $random = bin2hex(random_bytes(2));
        $this->numeroIdentifiant = "COUR-{$prefix}-{$date}-{$random}";
        return $this;
    }
}