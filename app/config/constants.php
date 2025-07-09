<?php
namespace App\Config;
// Paramètres de l'application
define('APP_NAME', 'Portail État Civil - Mairie de Cocody');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'https://etat-civil.votreville.fr');
//define('APP_ENV', 'development'); // 'development' ou 'production'

// Paramètres de sécurité
define('SESSION_TIMEOUT', 3600); // 1 heure
define('CSRF_TOKEN_LIFETIME', 1800); // 30 minutes


// Chemins
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('SIGNATURE_DIR', __DIR__ . '/../signatures/');


//Constantes de la commune
define('COMMUNE', ' Cocody');
define('COMMUNE_NAME', ' Mairie de Cocody');
define('COMMUNE_POSTAL', '01 BP 001 Cocody');
define('COMMUNE_SLOGAN', 'Ville accueillante et dynamique');
define('COMMUNE_PHONE', '++225 23 45 67 89');
define('COMMUNE_EMAIL', 'contact@mairie-cocody.ci');

// Chemins des images
define('LOGO_PATH', 'images/logo.png');
define('HERO_IMAGE', 'images/hero-commune.jpg');
?>