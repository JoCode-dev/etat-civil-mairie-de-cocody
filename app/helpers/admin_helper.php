<?php

class AdminHelper {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public static function validateAdminForm($data) {
        $errors = [];
        
        if (empty($data['nom'])) {
            $errors['nom'] = 'Le nom est obligatoire';
        }
        
        if (empty($data['prenom'])) {
            $errors['prenom'] = 'Le prénom est obligatoire';
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email invalide';
        }
        
        if (empty($data['role_id'])) {
            $errors['role_id'] = 'Le rôle est obligatoire';
        }
        
        return $errors;
    }

    public function generateTempPassword($length = 12) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $password;
    }

    public function getAvatarUrl($avatar) {
        return $avatar ? '/uploads/avatars/' . $avatar : '/assets/images/default-avatar.png';
    }

    // ... autres méthodes
}