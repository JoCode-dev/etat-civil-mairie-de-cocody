<?php
require_once __DIR__ . '/../../app/helpers/admin_helper.php';
require_once __DIR__ . '/../../app/config/constants.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

// Définir l'URL de base pour l'admin
$adminBaseUrl = '/etatcivil/';

AdminController::requireLogin();

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - <?php echo COMMUNE_NAME; ?></title>
    <meta content="" name="description">
    <link href="<?= $adminBaseUrl ?>assets/img/favicon.png" rel="icon">
    <!-- Bootstrap core CSS -->
    <link href="<?= $adminBaseUrl ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $adminBaseUrl ?>assets/css/admin.css" rel="stylesheet">
    <link href="<?= $adminBaseUrl ?>assets/css/bootstrap-icons.css" rel="stylesheet">

    <link href="<?= $adminBaseUrl ?>assets/css/fontawesome.all.css" rel="stylesheet">


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
                <img src="<?= $adminBaseUrl ?>assets/img/user.png" class="profile-img" alt="Profile">
                <span class="user-name me-2"></span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-right me-2"></i>Pofile</a></li>
                
                <li><a class="dropdown-item" href="/etatcivil/admin/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a></li>
            </ul>
        </div>
    </header>