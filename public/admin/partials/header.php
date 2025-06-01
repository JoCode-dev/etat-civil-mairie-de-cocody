<?php
require_once __DIR__ . '/../../../app/Config/Constants.php';


// Vérification de l'authentification (à adapter selon votre système)
/* if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
} */
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo COMMUNE_NAME; ?> </title>
    <meta content="" name="description">
    <link href="../../assets/img/favicon.png" rel="icon">
    <!-- Bootstrap CSS -->
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/admin.css" rel="stylesheet">
     <link href="../../assets/css/bootstrap-icons.css" rel="stylesheet">    
   
    <link href="../../assets/css/fontawesome.all.css" rel="stylesheet">


</head>

<body>

    <!-- Sidebar -->
    <?php require_once __DIR__ . '/sidebar.php'; ?>


    <header class="header">
        <div>
           
               <i id="sidebarToggle" class="bi bi-list action-iconWhite" ></i>
           
            <span class="mb-0 title"><?php echo COMMUNE_NAME; ?></span>
        </div>
        <div class="user-profile dropdown">
            <div data-bs-toggle="dropdown" aria-expanded="false">
                <img src="../../assets/img/user.png" class="profile-img" alt="Profile">
                <span class="user-name me-2">John Doe</span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profil</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Paramètres</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="/admin/logout"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a></li>
            </ul>
        </div>
    </header>