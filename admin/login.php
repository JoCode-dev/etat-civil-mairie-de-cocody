<?php

require_once __DIR__ . '/../app/config/constants.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/alert_helper.php';
require_once __DIR__ . '/../app/controllers/admin_controller.php';
require_once __DIR__ . '/../app/repositories/admin_repository.php';
require_once __DIR__ . '/../app/models/admin_model.php';


AdminController::redirectIfAuth();
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
            
            // Tentative de connexion
            $loginResult = AdminController::login($db, $email, $password);
            if ($loginResult !== "") {
                $errorMessage = $loginResult;
            }
        }

    } catch (Exception $e) {
        $errorMessage = "Une erreur est survenue : " . $e->getMessage();
    }
}

?>

<?php include 'partials/header-login.php'; ?>

<div class="container">
    <div class="login-container mt-4">
        <div class="login-header">
            <h1 class="login-title">Connexion Administrateur</h1>
            <p class="login-subtitle">Veuillez entrer vos identifiants pour accéder au tableau de bord.</p>
        </div>

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

            <button type="submit" class="btn btn-primary login-btn">Se Connecter</button>

            <div class="forgot-password">
                <a href="#">Mot de passe oublié ?</a>
            </div>
        </form>
    </div>
</div>

<?php include 'partials/footer-login.php'; ?>