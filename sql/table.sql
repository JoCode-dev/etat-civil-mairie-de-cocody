


CREATE TABLE role (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Identifiant unique du rôle',
    titre VARCHAR(225) NOT NULL COMMENT 'Nom unique du rôle (ex: Admin, Agent)',
    description TEXT NOT NULL COMMENT 'Description ou responsabilités associées au rôle',
    is_active BOOLEAN NOT NULL DEFAULT true COMMENT 'Statut d activation du rôle (true = actif)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date et heure de création du rôle',
    PRIMARY KEY (id),
    UNIQUE KEY (titre)
) ENGINE=InnoDB COMMENT='Table des rôles d utilisateurs';


CREATE TABLE type_actes (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identifiant unique du type d acte',
    code VARCHAR(20) NOT NULL UNIQUE COMMENT 'Code unique du type d acte (ex: ACTE_NAIS)',
    libelle VARCHAR(100) NOT NULL COMMENT 'Nom ou libellé du type d acte',
    description TEXT COMMENT 'Description détaillée du type d acte',
    delai_traitement INT NOT NULL DEFAULT 3 COMMENT 'Durée prévue en jours pour le traitement',
    frais REAL NOT NULL DEFAULT 0 COMMENT 'Coût associé à l acte (en FCFA)',
    statut BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'Actif ou non (true = actif)',
    fichier_path VARCHAR(255) COMMENT 'Chemin du modèle de fichier associé (PDF, etc.)',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création',
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date de dernière mise à jour'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table des types d actes gérés par l état civil';



CREATE TABLE citoyens (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identifiant unique du citoyen',
    nom VARCHAR(100) NOT NULL COMMENT 'Nom de famille du citoyen',
    prenom VARCHAR(100) NOT NULL COMMENT 'Prénom du citoyen',
    email VARCHAR(255) NOT NULL UNIQUE COMMENT 'Adresse e-mail unique du citoyen',
    mobile VARCHAR(20) COMMENT 'Numéro de téléphone mobile',
    adresse TEXT COMMENT 'Adresse physique complète du citoyen',
    password_hash VARCHAR(255) NOT NULL COMMENT 'Mot de passe crypté (hash)',
    verification_token VARCHAR(64) COMMENT 'Jeton de vérification e-mail',
    statut BOOLEAN DEFAULT TRUE COMMENT 'Statut du compte (actif/inactif)',
    email_verified BOOLEAN DEFAULT FALSE COMMENT 'Email vérifié (true/false)',
    remember_token VARCHAR(255) COMMENT 'Jeton pour la reconnexion automatique',
    remember_token_expires DATETIME COMMENT 'Date d expiration du remember_token',
    last_login DATETIME COMMENT 'Dernière connexion du citoyen',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Date d inscription',
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Dernière mise à jour des données'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table des citoyens inscrits au service';




-- Table des administrateurs (version corrigée et optimisée)
CREATE TABLE administrateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mobile VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    verification_token VARCHAR(64),
    statut BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    remember_token VARCHAR(255),
    remember_token_expires DATETIME,
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (role_id) REFERENCES role(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    
    -- Index
    INDEX idx_email (email),
    INDEX idx_role (role_id),
    INDEX idx_statut (statut),
    INDEX idx_nom_prenom (nom, prenom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE coursiers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    entreprise VARCHAR(100),
    telephone VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) UNIQUE,
    numero_identifiant VARCHAR(50) NOT NULL UNIQUE,
    moyen_transport ENUM('moto', 'voiture', 'velo', 'camionnette') NOT NULL,
    statut ENUM('actif', 'inactif', 'en_congé', 'en_livraison') DEFAULT 'actif',
    password_hash VARCHAR(255) NOT NULL,
    salt VARCHAR(32),
    last_login DATETIME,
    reset_token VARCHAR(100),
    reset_token_expires DATETIME,
    zone_couverture TEXT,
    date_embauche DATE,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE actes_naissance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero_registre VARCHAR(50) NOT NULL,
    annee_registre INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenoms VARCHAR(100) NOT NULL,
    date_naissance_lettre VARCHAR(225)  NOT NULL,
    heure_naissance_lettre VARCHAR(100)  NOT NULL,
    date_naissance DATE  NOT NULL,
    heure_naissance TIME  NOT NULL,
    lieu_naissance VARCHAR(255) NOT NULL,
    
    nom_pere VARCHAR(100) NOT NULL,
    profession_pere VARCHAR(100),
    
    nom_mere VARCHAR(100)  NOT NULL,
    profession_mere VARCHAR(100) ,
    
    mention_mariage TEXT,
    mention_divorce TEXT,
    mention_deces TEXT,

    created_at DATETIME NOT NULL DEFAULT now(),
    updated_at DATETIME NOT NULL DEFAULT now()
);

CREATE TABLE actes_mariage (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero_registre VARCHAR(50) NOT NULL,
    annee_registre INT NOT NULL,
    date_mariage_lettre VARCHAR(225)  NOT NULL,
    date_mariage DATETIME NOT NULL,
    lieu_mariage VARCHAR(100) NOT NULL,
    nom_prenoms_epoux VARCHAR(100) NOT NULL,
    date_naissance_epoux DATETIME NOT NULL,
    profession_epoux VARCHAR(100) ,
    nom_pere_epoux VARCHAR(100) NOT NULL,
    nom_mere_epoux VARCHAR(100) NOT NULL,
    nom_prenoms_epouse VARCHAR(100) NOT NULL,
    profession_epouse VARCHAR(100) ,
    date_naissance_epouse DATETIME NOT NULL,
    nom_pere_epouse VARCHAR(100) NOT NULL,
    nom_mere_epouse VARCHAR(100) NOT NULL,
    temoin_homme VARCHAR(100) NOT NULL,
    temoin_femme VARCHAR(100) NOT NULL,
    mention_divorce TEXT,
    create_by VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT now(),
    updated_at DATETIME NOT NULL DEFAULT now()
);

CREATE TABLE actes_deces (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero_registre VARCHAR(50) NOT NULL,
    annee_registre INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenoms VARCHAR(100) NOT NULL,
    date_deces_lettre VARCHAR(225)  NOT NULL,
    date_deces DATE  NOT NULL,
    lieu_deces VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT now(),
    updated_at DATETIME NOT NULL DEFAULT now()
);



CREATE TABLE demandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(50) NOT NULL,
    citoyen_id INT NOT NULL,
    acte_libelle VARCHAR(100) NOT NULL,
    citoyen_nom VARCHAR(100) NOT NULL,
    numero_actes VARCHAR(100) NOT NULL,
    type_actes_id INT NOT NULL,
    date_demande DATETIME NOT NULL DEFAULT now(),
    statut ENUM('en_attente', 'en_traitement', 'pret', 'recupere', 'annule', 'en_livraison', 'livre') DEFAULT 'en_attente',
    fichier_path VARCHAR(255),
    adresse_livraison TEXT,
    frais_livraison DECIMAL(10,2) DEFAULT 0,
    frais_unitaire DECIMAL(10,2) DEFAULT 0,
    total_frais DECIMAL(10,2) DEFAULT 0,
    nombreActes INT DEFAULT 1,
    date_livraison_prevue DATE,
    date_livraison_effectue DATETIME,
    coursier_id INT,
    numero_suivi VARCHAR(100),
    methode_livraison ENUM('retrait_guichet', 'livraison_domicile', 'livraison_point_relais') DEFAULT 'retrait_guichet',
    FOREIGN KEY (citoyen_id) REFERENCES citoyens(id) ON DELETE CASCADE,
    FOREIGN KEY (type_actes_id) REFERENCES type_actes(id) ON DELETE CASCADE,
    FOREIGN KEY (coursier_id) REFERENCES coursiers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- Table des notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    citoyen_id INT NOT NULL,
    demande_id INT,
    message TEXT NOT NULL,
    lu BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (citoyen_id) REFERENCES citoyens(id) ON DELETE CASCADE,
    FOREIGN KEY (demande_id) REFERENCES demandes(id) ON DELETE SET NULL
);




CREATE TABLE paiements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(50) NOT NULL,
    demande_id INT NOT NULL,
    citoyen_id INT NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    methode_paiement ENUM('mobile_money', 'carte_credit', 'especes', 'autre') NOT NULL,
    statut ENUM('en_attente', 'complete', 'annule', 'echec') DEFAULT 'en_attente',
    date_transaction DATETIME DEFAULT CURRENT_TIMESTAMP,
    details TEXT,
    FOREIGN KEY (demande_id) REFERENCES demandes(id) ON DELETE CASCADE,
    FOREIGN KEY (citoyen_id) REFERENCES citoyens(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE reclamations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(20) NOT NULL,
    citoyen_id INT NOT NULL,
    demande_id INT,
    sujet VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    statut ENUM('nouveau', 'en_cours', 'resolu', 'rejete') DEFAULT 'nouveau',
    agent_id INT,
    admin_id INT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME,
    FOREIGN KEY (citoyen_id) REFERENCES citoyens(id),
    FOREIGN KEY (demande_id) REFERENCES demandes(id),
    FOREIGN KEY (agent_id) REFERENCES agents(id),
    FOREIGN KEY (admin_id) REFERENCES admins(id)
);

CREATE TABLE reclamation_historique (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reclamation_id INT NOT NULL,
    statut VARCHAR(20) NOT NULL,
    admin_id INT NOT NULL,
    commentaire TEXT,
    date_action DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reclamation_id) REFERENCES reclamations(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admins(id)
);

CREATE TABLE demande_statut_historique (
    id INT AUTO_INCREMENT PRIMARY KEY,
    demande_id INT NOT NULL,
    officier_etat_civil_id INT NOT NULL,
    statut VARCHAR(20) NOT NULL,
    commentaire TEXT,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (demande_id) REFERENCES demandes(id) ON DELETE CASCADE
);