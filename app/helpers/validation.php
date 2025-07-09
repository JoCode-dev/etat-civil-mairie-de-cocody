<?php
/**
 * Valide une adresse email
 * 
 * @param string $email Email à valider
 * @return bool True si l'email est valide
 */
function validerEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valide un numéro de téléphone français
 * 
 * @param string $telephone Numéro à valider
 * @return bool True si le numéro est valide
 */
function validerTelephone($telephone) {
    // Supprime tous les caractères non numériques
    $cleaned = preg_replace('/[^0-9]/', '', $telephone);
    
    // Vérifie les formats français courants
    return preg_match('/^(0|\\+33|0033)[1-9][0-9]{8}$/', $cleaned);
}

/**
 * Valide une date au format français (jj/mm/aaaa)
 * 
 * @param string $date Date à valider
 * @return bool True si la date est valide
 */
function validerDateFrancaise($date) {
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $matches)) {
        return checkdate($matches[2], $matches[1], $matches[3]);
    }
    return false;
}

/**
 * Valide une date de naissance (doit être dans le passé)
 * 
 * @param string $date Date à valider
 * @return bool True si la date est valide
 */
function validerDateNaissance($date) {
    if (!validerDateFrancaise($date)) {
        return false;
    }
    
    list($jour, $mois, $annee) = explode('/', $date);
    $dateNaissance = new DateTime("$annee-$mois-$jour");
    $aujourdhui = new DateTime();
    
    return $dateNaissance < $aujourdhui;
}

/**
 * Valide une date d'événement pour un acte
 * 
 * @param string $date Date à valider
 * @param string $type Type d'acte (naissance, mariage, deces)
 * @return bool True si la date est valide pour ce type d'acte
 */
function validerDateEvenement($date, $type) {
    if (!validerDateFrancaise($date)) {
        return false;
    }
    
    list($jour, $mois, $annee) = explode('/', $date);
    $dateEvenement = new DateTime("$annee-$mois-$jour");
    $aujourdhui = new DateTime();
    
    switch ($type) {
        case 'naissance':
            // Une naissance doit être dans le passé
            return $dateEvenement < $aujourdhui;
            
        case 'mariage':
            // Un mariage doit être dans le passé
            return $dateEvenement < $aujourdhui;
            
        case 'deces':
            // Un décès doit être dans le passé
            return $dateEvenement < $aujourdhui;
            
        default:
            return false;
    }
}

/**
 * Valide un nom ou prénom
 * 
 * @param string $nom Nom à valider
 * @return bool True si le nom est valide
 */
function validerNom($nom) {
    return preg_match('/^[\p{L}\-\'\s]{2,100}$/u', $nom);
}

/**
 * Valide un mot de passe
 * 
 * @param string $password Mot de passe à valider
 * @return array Tableau d'erreurs (vide si valide)
 */
function validerMotDePasse($password) {
    $erreurs = [];
    
    if (strlen($password) < 8) {
        $erreurs[] = "Le mot de passe doit contenir au moins 8 caractères";
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $erreurs[] = "Le mot de passe doit contenir au moins une majuscule";
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $erreurs[] = "Le mot de passe doit contenir au moins une minuscule";
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $erreurs[] = "Le mot de passe doit contenir au moins un chiffre";
    }
    
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $erreurs[] = "Le mot de passe doit contenir au moins un caractère spécial";
    }
    
    return $erreurs;
}

/**
 * Valide les données d'une demande d'acte
 * 
 * @param array $data Données du formulaire
 * @return array Tableau d'erreurs (vide si valide)
 */
function validerDemandeActe($data) {
    $erreurs = [];
    
    // Validation du type d'acte
    if (empty($data['type_acte']) || !in_array($data['type_acte'], ['naissance', 'mariage', 'deces'])) {
        $erreurs['type_acte'] = "Veuillez sélectionner un type d'acte valide";
    }
    
    // Validation de la date d'événement
    if (empty($data['date_evenement'])) {
        $erreurs['date_evenement'] = "Veuillez entrer une date";
    } elseif (!validerDateEvenement($data['date_evenement'], $data['type_acte'])) {
        $erreurs['date_evenement'] = "Veuillez entrer une date valide pour ce type d'acte";
    }
    
    // Validation du lieu d'événement
    if (empty($data['lieu_evenement'])) {
        $erreurs['lieu_evenement'] = "Veuillez entrer un lieu";
    } elseif (strlen($data['lieu_evenement']) > 255) {
        $erreurs['lieu_evenement'] = "Le lieu ne peut pas dépasser 255 caractères";
    }
    
    // Validation spécifique pour les actes de mariage
    if ($data['type_acte'] === 'mariage') {
        if (empty($data['nom_conjoint'])) {
            $erreurs['nom_conjoint'] = "Veuillez entrer le nom du conjoint";
        } elseif (!validerNom($data['nom_conjoint'])) {
            $erreurs['nom_conjoint'] = "Veuillez entrer un nom valide";
        }
        
        if (empty($data['prenom_conjoint'])) {
            $erreurs['prenom_conjoint'] = "Veuillez entrer le prénom du conjoint";
        } elseif (!validerNom($data['prenom_conjoint'])) {
            $erreurs['prenom_conjoint'] = "Veuillez entrer un prénom valide";
        }
    }
    
    return $erreurs;
}

/**
 * Nettoie une entrée utilisateur
 * 
 * @param string $input Entrée à nettoyer
 * @return string Entrée nettoyée
 */
function nettoyerInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * Valide et nettoie un tableau de données
 * 
 * @param array $data Données à valider et nettoyer
 * @return array Données nettoyées
 */
function validerEtNettoyer($data) {
    $nettoye = [];
    
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $nettoye[$key] = validerEtNettoyer($value);
        } else {
            $nettoye[$key] = nettoyerInput($value);
        }
    }
    
    return $nettoye;
}