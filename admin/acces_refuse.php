<?php include 'partials/header-login.php'; ?>

<div class="container mt-5">
    <div class="alert alert-danger">
        <h4><i class="fas fa-ban"></i> Accès refusé</h4>
        <p>Votre profil utilisateur ne permet pas d'accéder au système</p>
        <p>"Vous n'avez pas les droits nécessaires pour accéder à cette section" </p>
        <p>Ressource demandée : <strong><?= htmlspecialchars($_GET['ressource'] ?? 'N/A') ?></strong></p>
        
        <div class="mt-3">
            <a href="/admin/login" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Retour à la page de connection
            </a>
           
        </div>
    </div>
</div>

<?php include 'partials/footer-login.php'; ?>