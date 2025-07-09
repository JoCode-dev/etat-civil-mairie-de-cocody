INSERT INTO role (id, titre, description, is_active, created_at) VALUES
-- Niveau 1 : Administration locale
(1, 'Administrateur', 'Accès complet à toutes les fonctionnalités de la commune', true, NOW()),

-- Niveau 2 : Gestion des services municipaux
(2, 'Secrétaire Général', 'Supervision des services municipaux', true, NOW()),
(3, 'Responsable État Civil', 'Validation finale des actes et supervision des agents', true, NOW()),

-- Niveau 3 : Agents opérationnels
(4, 'Agent État Civil', 'Saisie et modification des actes', true, NOW()),
(5, 'Agent Archivage', 'Gestion physique et numérique des archives', true, NOW()),
(6, 'Agent Accueil', 'Enregistrement des demandes des citoyens', true, NOW()),

-- Niveau 4 : Partenaires externes
(7, 'Officier Judiciaire', 'Consultation des actes pour enquêtes', true, NOW()),
(8, 'Partenaire Funéraire', 'Accès limité aux actes de décès', true, NOW()),
(9, 'Statisticien', 'Accès en lecture seule pour rapports', true, NOW()),
(10, 'Auditeur', 'Accès temporaire pour contrôles', false, NOW());


-- Mot de passe : "admin123" (hashé en bcrypt)
INSERT INTO administrateurs (nom, prenom, email, mobile, password_hash, role_id, statut, created_at) VALUES
-- Admin (Accès complet à toutes les fonctionnalités)
('Kouadio', 'Jean', 'admin@commune.ci', '0700000001', '$2y$10$elbV6WSAflrfFVgQ1QU4JOV4af0tALOtidDEhElV1yawNR8B5wuA.', 1, true, NOW()),

-- Secrétaire Général (Supervision des services municipaux)
('Traoré', 'Awa', 'sg@commune.ci', '0700000002', '$2y$10$elbV6WSAflrfFVgQ1QU4JOV4af0tALOtidDEhElV1yawNR8B5wuA.', 2, true, NOW()),

-- Responsable État Civil (Validation des actes et supervision des agents)
('Koné', 'Mamadou', 'resp-ec@commune.ci', '0700000003', '$2y$10$elbV6WSAflrfFVgQ1QU4JOV4af0tALOtidDEhElV1yawNR8B5wuA.', 3, true, NOW()),

-- Agent État Civil (Saisie et modification des actes)
('Yao', 'Akissi', 'agent-ec@commune.ci', '0700000004', '$2y$10$elbV6WSAflrfFVgQ1QU4JOV4af0tALOtidDEhElV1yawNR8B5wuA.', 4, true, NOW()),

-- Agent Archivage (Gestion des archives)
('Diomandé', 'Ibrahim', 'archiviste@commune.ci', '0700000005', '$2y$10$elbV6WSAflrfFVgQ1QU4JOV4af0tALOtidDEhElV1yawNR8B5wuA.', 5, true, NOW()),

-- Agent Accueil (Enregistrement des demandes des citoyens)
('Bamba', 'Fatou', 'accueil@commune.ci', '0700000006', '$2y$10$elbV6WSAflrfFVgQ1QU4JOV4af0tALOtidDEhElV1yawNR8B5wuA.', 6, true, NOW()),

-- Officier Judiciaire (Consultation des actes pour enquêtes)
('Ouattara', 'Moussa', 'officier-judiciaire@justice.ci', '0700000007', '$2y$10$elbV6WSAflrfFVgQ1QU4JOV4af0tALOtidDEhElV1yawNR8B5wuA.', 7, true, NOW()),

-- Partenaire Funéraire (Accès limité aux actes de décès)
('Koffi', 'Serge', 'funeraire@commune.ci', '0700000008', '$2y$10$elbV6WSAflrfFVgQ1QU4JOV4af0tALOtidDEhElV1yawNR8B5wuA.', 8, true, NOW()),

-- Statisticien (Accès en lecture seule pour rapports)
('NGuessan', 'Marie', 'statisticien@commune.ci', '0700000009', '$2y$10$elbV6WSAflrfFVgQ1QU4JOV4af0tALOtidDEhElV1yawNR8B5wuA.', 9, true, NOW()),

-- Auditeur (Accès temporaire pour contrôles)
('Ehouman', 'Jacques', 'auditeur@commune.ci', '0700000010', '$2y$10$elbV6WSAflrfFVgQ1QU4JOV4af0tALOtidDEhElV1yawNR8B5wuA.', 10, false, NOW());

-- Mot de passe : "admin123" (hashé en bcrypt)
INSERT INTO citoyens (nom, prenom, email, mobile, adresse, password_hash, statut, created_at) VALUES
('Kouadio', 'Jean', 'jean.kouadio@example.ci', '0701010101', '10 Rue des Palmiers, Cocody, Abidjan', '$2y$10$elbV6WSAflrfFVgQ1QU4JOV4af0tALOtidDEhElV1yawNR8B5wuA.', true, NOW()),
('Traoré', 'Awa', 'awa.traore@example.ci', '0702020202', '25 Boulevard de la Paix, Bouaké', '$2y$10$elbV6WSAflrfFVgQ1QU4JOV4af0tALOtidDEhElV1yawNR8B5wuA.', true, NOW()),
('Koné', 'Mamadou', 'mamadou.kone@example.ci', '0703030303', '5 Rue des Orchidées, Yamoussoukro', '$2y$10$elbV6WSAflrfFVgQ1QU4JOV4af0tALOtidDEhElV1yawNR8B5wuA.', true, NOW()),
('Yao', 'Akissi', 'akissi.yao@example.ci', '0704040404', '15 Allée des Cocotiers, San Pedro', '$2y$10$elbV6WSAflrfFVgQ1QU4JOV4af0tALOtidDEhElV1yawNR8B5wuA.', true, NOW());

INSERT INTO type_actes (code, libelle, description, delai_traitement, frais, statut, created_at) VALUES
('NAISS', 'Acte de naissance', 'Enregistrement des naissances survenues dans la commune', 3, 2000, true, NOW()),
('MARI', 'Acte de mariage', 'Enregistrement des mariages célébrés dans la commune', 5, 5000, true, NOW()),
('DECES', 'Acte de décès', 'Enregistrement des décès survenus dans la commune', 1, 1000, true, NOW()),
('RECON', 'Reconnaissance', 'Reconnaissance d\'enfant naturel', 2, 3000, true, NOW()),
('JUG', 'Jugement déclaratif', 'Jugement supplétif en cas de perte ou d\'absence d\'acte', 10, 10000, true, NOW()),
('LIVFAM', 'Livret de famille', 'Création ou mise à jour du livret de famille', 2, 4000, true, NOW()),
('COPIE', 'Copie intégrale', 'Délivrance de copie intégrale d\'acte', 1, 500, true, NOW()),
('EXTRAIT', 'Extrait d\'acte', 'Délivrance d\'extrait avec filiation', 1, 500, true, NOW()),
('CERTIF', 'Certificat de non-inscription', 'Attestation de non-enregistrement d\'un acte', 1, 1000, true, NOW()),
('LEGAL', 'Légalisation de signature', 'Certification de signature sur documents', 1, 2000, true, NOW());


INSERT INTO actes_naissance (numero_registre, annee_registre, nom, prenoms, date_naissance_lettre, heure_naissance_lettre, date_naissance, heure_naissance, lieu_naissance, nom_pere, profession_pere, nom_mere, profession_mere) VALUES
('COC-2023-001', 2023, 'KOUADIO', 'Amani Christian', 'Le vingt-cinq décembre deux mille vingt-deux', 'dix heures trente minutes', '2022-12-25', '10:30:00', 'Clinique Internationale de Cocody, Abidjan', 'KOUADIO Jean', 'Ingénieur informaticien', 'TRAORE Aminata', 'Enseignante'),

('COC-2023-002', 2023, 'KONAN', 'Marie Chantal', 'Le premier janvier deux mille vingt-trois', 'huit heures quinze minutes', '2023-01-01', '08:15:00', 'CHU de Cocody, Abidjan', 'KONAN Paul', 'Médecin', 'DIABY Fatou', 'Commerçante'),

('COC-2023-003', 2023, 'YEO', 'Mohamed Lamine', 'Le quinze mars deux mille vingt-trois', 'quatorze heures', '2023-03-15', '14:00:00', 'Polyclinique des Deux-Plateaux, Cocody', 'YEO Moussa', 'Entrepreneur', 'CISSE Aïssata', 'Fonctionnaire'),

('COC-2023-004', 2023, 'BAMBA', 'Louise Carine', 'Le trente avril deux mille vingt-trois', 'sept heures quarante-cinq minutes', '2023-04-30', '07:45:00', 'Clinique Saint Jean, Cocody', 'BAMBA Pierre', 'Architecte', 'KOUAME Affoué', 'Infirmière'),

('COC-2023-005', 2023, 'DIALLO', 'Ismaël', 'Le douze juillet deux mille vingt-trois', 'dix-neuf heures vingt minutes', '2023-07-12', '19:20:00', 'Hôpital Général de Cocody, Abidjan', 'DIALLO Ibrahim', 'Journaliste', 'SANGARE Mariam', 'Avocate'),

('COC-2023-006', 2023, 'GBAGBO', 'Arnaud Rodrigue', 'Le cinq septembre deux mille vingt-trois', 'midi', '2023-09-05', '12:00:00', 'Clinique de la Riviera, Cocody', 'GBAGBO Georges', 'Ingénieur agronome', 'KONE Salimata', 'Banquière'),

('COC-2023-007', 2023, 'TOURE', 'Aïcha Fatim', 'Le vingt octobre deux mille vingt-trois', 'vingt-deux heures dix minutes', '2023-10-20', '22:10:00', 'Centre Médical Dominique Ouattara, Cocody', 'TOURE Mamadou', 'Professeur', 'CAMARA Djeneba', 'Sage-femme'),

('COC-2023-008', 2023, 'KOUAME', 'Joël Pacôme', 'Le trois novembre deux mille vingt-trois', 'quatre heures du matin', '2023-11-03', '04:00:00', 'Clinique Les Harmonies, Cocody', 'KOUAME Ernest', 'Policier', 'AKA Béatrice', 'Esthéticienne'),

('COC-2023-009', 2023, 'DOSSO', 'Nadia Vanessa', 'Le vingt-cinq décembre deux mille vingt-trois', 'seize heures cinquante minutes', '2023-12-25', '16:50:00', 'Polyclinique Internationale Sainte Anne-Marie, Cocody', 'DOSSO Karim', 'Pilote', 'KOUASSI Nadège', 'Designer'),

('COC-2023-010', 2023, 'ADOU', 'Franck Olivier', 'Le trente et un décembre deux mille vingt-trois', 'vingt-trois heures trente minutes', '2023-12-31', '23:30:00', 'Clinique Biaka Boda, Cocody', 'ADOU Marcel', 'Chauffeur', 'KOUAKOU Yvette', 'Coiffeuse');


INSERT INTO actes_mariage (
    numero_registre, annee_registre, date_mariage_lettre, date_mariage,
    lieu_mariage, nom_prenoms_epoux, date_naissance_epoux, profession_epoux,
    nom_pere_epoux, nom_mere_epoux, nom_prenoms_epouse, date_naissance_epouse,
    profession_epouse, nom_pere_epouse, nom_mere_epouse, temoin_homme, temoin_femme,
    mention_divorce, create_by
) VALUES
-- Acte 1
('COC-2023-001', 2023, 'le quinze janvier deux mille vingt-trois', '2023-01-15 10:00:00',
'Mairie de Cocody', 'KOUADIO Jean-Paul', '1990-05-12 00:00:00', 'Ingénieur informatique',
'KOUADIO Bernard', 'KOUADIO née TRAORE Awa', 'TRAORE Aminata', '1992-08-20 00:00:00',
'Infirmière', 'TRAORE Moussa', 'TRAORE née DIALLO Fatou', 'KONAN Yao', 'KOUAME Affoué',
NULL, 'admin1'),

-- Acte 2
('COC-2023-002', 2023, 'le vingt février deux mille vingt-trois', '2023-02-20 11:30:00',
'Mairie de Cocody', 'YAPI Serge', '1988-11-03 00:00:00', 'Comptable',
'YAPI Marcel', 'YAPI née KOUAME Akissi', 'DIABY Mariam', '1990-07-15 00:00:00',
'Enseignante', 'DIABY Ibrahim', 'DIABY née CISSE Aminata', 'BAMBA Karim', 'KONE Salimata',
NULL, 'admin1'),

-- Acte 3
('COC-2023-003', 2023, 'le dix mars deux mille vingt-trois', '2023-03-10 09:00:00',
'Mairie de Cocody', 'KONE Boubacar', '1985-09-25 00:00:00', 'Commerçant',
'KONE Daouda', 'KONE née SORO Mariam', 'CAMARA Aïcha', '1987-12-30 00:00:00',
'Gestionnaire', 'CAMARA Sékou', 'CAMARA née DIARRA Bintou', 'SANGARE Moussa', 'TOURE Hadja',
NULL, 'admin2'),

-- Acte 4
('COC-2023-004', 2023, 'le cinq avril deux mille vingt-trois', '2023-04-05 15:00:00',
'Mairie de Cocody', 'DIALLO Mamadou', '1992-04-18 00:00:00', 'Médecin',
'DIALLO Ousmane', 'DIALLO née SOW Ramata', 'SISSOKO Fatoumata', '1993-06-22 00:00:00',
'Avocate', 'SISSOKO Modibo', 'SISSOKO née KEITA Kadiatou', 'BAH Amadou', 'DIARRA Mariam',
NULL, 'admin2'),


('COC-2023-005', 2023, 'le vingt-cinq mai deux mille vingt-trois', '2023-05-25 14:00:00',
'Mairie de Cocody', 'KOUAME Yao', '1989-07-08 00:00:00', 'Architecte',
'KOUAME Koffi', 'KOUAME née ADJOBI Marie', 'AKA Jessica', '1991-03-14 00:00:00',
'Journaliste', 'AKA Pierre', 'AKA née DUBOIS Claire', 'GBAGBO Jacques', 'NDRI Yvette',
NULL, 'admin3'),

-- Acte 6
('COC-2023-006', 2023, 'le douze juin deux mille vingt-trois', '2023-06-12 10:30:00',
'Mairie de Cocody', 'BAMBA Ali', '1991-10-05 00:00:00', 'Ingénieur civil',
'BAMBA Mohamed', 'BAMBA née CISSE Aïssatou', 'FOFANA Kadidja', '1993-01-28 00:00:00',
'Designer', 'FOFANA Moussa', 'FOFANA née SANGARE Amina', 'TRAORE Lassina', 'DIABY Mariam',
NULL, 'admin3'),

-- Acte 7
('COC-2023-007', 2023, 'le trente juillet deux mille vingt-trois', '2023-07-30 16:00:00',
'Mairie de Cocody', 'KOUASSI Eric', '1987-12-15 00:00:00', 'Banquier',
'KOUASSI Martin', 'KOUASSI née ADOU Henriette', 'KONAN Nadège', '1989-09-10 00:00:00',
'Pharmacienne', 'KONAN Paul', 'KONAN née KOUADIO Véronique', 'YEO Solange', 'AKA Béatrice',
NULL, 'admin1'),

-- Acte 8
('COC-2023-008', 2023, 'le huit août deux mille vingt-trois', '2023-08-08 11:00:00',
'Mairie de Cocody', 'TRAORE Ibrahim', '1986-02-20 00:00:00', 'Entrepreneur',
'TRAORE Amadou', 'TRAORE née DIARRA Fatou', 'DIALLO Aïssatou', '1988-11-12 00:00:00',
'Docteure', 'DIALLO Boubacar', 'DIALLO née SIDIBE Mariam', 'COULIBALY Drissa', 'SANGARE Oumou',
NULL, 'admin2'),

-- Acte 9
('COC-2023-009', 2023, 'le dix-sept septembre deux mille vingt-trois', '2023-09-17 09:30:00',
'Mairie de Cocody', 'N GUESSAN Marc', '1990-06-25 00:00:00', 'Juriste',
'N GUESSAN Jean', 'N GUESSAN née KOUADIO Marguerite', 'KOUADIO Rachel', '1992-04-03 00:00:00',
'Architecte d intérieur', 'KOUADIO Simon', 'KOUADIO née YAO Esther', 'KOUAME Lucien', 'KONE Affoué',
NULL, 'admin3'),

-- Acte 10
('COC-2023-010', 2023, 'le cinq décembre deux mille vingt-trois', '2023-12-05 14:30:00',
'Mairie de Cocody', 'SORO Karim', '1988-08-30 00:00:00', 'Pilote',
'SORO Moussa', 'SORO née KONE Aïcha', 'YEO Fatou', '1990-07-18 00:00:00',
'Chirurgienne', 'YEO Issa', 'YEO née TRAORE Mariam', 'KONE Mamadou', 'DIABY Aminata',
NULL, 'admin1');
