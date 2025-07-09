<?php
require_once 'app/config/constants.php';
require_once 'app/config/database.php';
require_once 'app/helpers/alert_helper.php';
require_once 'app/helpers/form_item_helper.php';
require_once 'app/repositories/citoyen_repository.php';
require_once 'app/models/citoyen_model.php';
// require_once 'app/Controllers/citoyen_controller.php';
include "citoyen/includes/header.php";

// Définir l'URL de base
$baseUrl = '/etatcivil/';

CitoyenController::redirectIfAuth();
$errorMessage = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "L'adresse email est invalide.";
        } else if (empty($password)) {
            $errorMessage = "Le mot de passe ne peut pas être vide.";
        } else {
            $email = htmlspecialchars($email);
            $password = htmlspecialchars($password);
        }

        $result =  CitoyenController::login($db,$email,$password);
         $errorMessage = $result;

    } catch (Exception $e) {
        $errorMessage = "Une erreur est survenue : " . $e->getMessage();
    }
}

?>



<div class="container">
   
    <div class="login-container mt-4 d-flex flex-column flex-lg-row align-items-center align-items-lg-start">
        <!-- Left Section: Title and Logo -->
        <div class="login-header text-center me-lg-5">
             <h1 class="login-title">Connexion</h1>
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
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="admin@example.com" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Mot de Passe</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••••" required>
                </div>

                <div class="divider"></div>

                <button type="submit" class="btn btn-primary login-btn w-100">Se Connecter</button>

                <div class="forgot-password text-center mt-3">
                    <a href="#">Mot de passe oublié ?</a>
                </div>
                <div class="d-flex justify-content-center mt-5">
                    <span>Vous n'avez pas de compte? </span>
                    <a href="/etatcivil/registre.php" class="ms-2">Enregistrez-vous</a>
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