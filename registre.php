<?php

require_once 'app/config/constants.php';
require_once 'app/config/database.php';
require_once 'app/helpers/alert_helper.php';
require_once 'app/helpers/form_item_helper.php';
require_once 'app/repositories/citoyen_repository.php';
require_once 'app/models/citoyen_model.php';
require_once 'app/Controllers/citoyen_controller.php';

// Définir l'URL de base
$baseUrl = '/etatcivil/';

require_once __DIR__ . '/citoyen/includes/header.php';

 CitoyenController::redirectIfAuth();
$citoyenRepository = new CitoyenRepository($db);
$citoyenModel = new CitoyenModel();
$errors = [];
$success = false;
$exceptionMessage = null;



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //  try {
    if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['password'])) {

        $citoyenModel->setNom($_POST['nom']);
        $citoyenModel->setPrenom($_POST['prenom']);
        $citoyenModel->setEmail($_POST['email']);
        $citoyenModel->setAdresse($_POST['adresse']);
        CitoyenController::register($db, $citoyenModel, $_POST['password']);
        $success = true;
        $successMessage = "Inscription réussie. Vous pouvez vous connecter.";
    } else {
        $errorMessage = "Veuillez corriger les erreurs dans le formulaire.";
    }
    /* } catch (Exception $e) {
        $exceptionMessage = $e->getMessage();
    } */
}

?>

<div class="container">
    <div class="login-container mt-4 d-flex flex-column flex-lg-row align-items-center align-items-lg-start">
        <!-- Left Section: Title and Logo -->
        <div class="login-header text-center me-lg-5">
            <h1 class="login-title">S'enregistrer</h1>
            <img src="<?= $baseUrl ?>assets/img/favicon.png" alt="Logo" class="img-fluid mt-3" style="max-width: 200px; display: block; margin: 0 auto;">

            <p class="login-subtitle">Veuillez entrer vos identifiants pour effectuer une demande d'actes.</p>

        </div>

        <!-- Right Section: Login Form -->
        <div class="login-form w-100 mt-4 mt-lg-0">
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="row">
                    <div class="col-md-4">
                        <?= FormItemHelper::input('nom', 'Nom', true, $citoyenModel->getNom() ?? '', 'text', $errors['nom'] ?? '') ?>
                    </div>
                    <div class="col-md-8">
                        <?= FormItemHelper::input('prenom', 'Prénom', true, $citoyenModel->getPrenom() ?? '', 'text', $errors['prenom'] ?? '') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?= FormItemHelper::input('adresse', 'Adresse', true, $citoyenModel->getAdresse() ?? '', 'text', $errors['adresse'] ?? '') ?>
                    </div>
                </div>
                <?= FormItemHelper::input('email', 'Email', true, $citoyenModel->getEmail() ?? '', 'email', $errors['email'] ?? '') ?>
                <?= FormItemHelper::input('password', 'Mot de passe', true, '', 'password', $errors['password'] ?? '') ?>

                <div class="divider"></div>

                <button type="submit" class="btn btn-primary login-btn w-100">S'enregistrer</button>


                <div class="d-flex justify-content-center mt-5">
                    <span>Vous avez un compte? </span>
                    <a href="/etatcivil/login.php" class="ms-2">Connectez-vous</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @media (max-width: 991.98px) {
        .login-container {
            flex-direction: column;
        }

        .login-header {
            margin-bottom: 20px;
        }
    }
</style>




<?php include 'citoyen/includes/footer.php'; ?>