<?php
require_once __DIR__ . '/../../../app/Controllers/citoyen_controller.php';


$url =  $_SERVER['PHP_SELF'];
function navItem(string $title, $urlpage): string
{
    global  $url;
    $classe = "";
    if (str_contains($url, $urlpage)) {
        $classe .= ' active';
    }
    return <<<HTML
        <li><a href="{$urlpage}" class="{$classe}">{$title}</a></li>
  HTML;
}




?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maire de Cocody</title>
    <meta content="" name="description">
    <link href="/assets/img/favicon.png" rel="icon">
    <!-- Bootstrap core CSS -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/bootstrap-icons.css" rel="stylesheet">

    <link href="/assets/css/fontawesome.all.css" rel="stylesheet">

    <!--  <link href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet"> -->

    <link rel="stylesheet" href="/assets/css/citoyen.css">

</head>

<body>
    <header>
        <div class="logo-commune">
            <img src="/assets/img/favicon.png" alt="Logo">
            <div class="titre-commune">
                <h1>Maire de Cocody</h1>
                <h2>Portail État Civil</h2>
            </div>
        </div>
        <div class="header-right">
            <?= CitoyenController::authHtml() ?>
        </div>
    </header>

    <nav>
        <ul>
            <?= navItem('Accueil', "/index"); ?>
            <?= navItem('Démande', "/acte_demande") ?>
            <?= CitoyenController::isAuth() ? navItem('Mes démandes', "/citoyen/demandes") : '' ?>
            <?= navItem('Contacts', "/contacts"); ?>
            <?= navItem('A propos', "/apropos"); ?>

        </ul>
    </nav>