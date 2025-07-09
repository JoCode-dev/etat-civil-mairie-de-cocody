<?php
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/repositories/admin_repository.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

AdminController::requirelogin();
$activeMenu = "admins";
AdminController::checkAndRedirectPermission($activeMenu);
$adminRepository = new AdminRepository($db);

$id = $_GET['id'] ?? null;

if ($id === null) {
    die('ID de l\'administrateur non fourni.');
}

try {
    $admin = $adminRepository->findById(intval($id));
    if ($admin === null) {
        die('Administrateur non trouvé.');
    }

    // Exemple d'activités récentes (à remplacer par une requête réelle si nécessaire)
  
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}



?>

<?php include '../partials/header.php'; ?>

<main class="main-content">

     <?= ContentRendu::header(
        " Détails de l'administrateur",
        [
           ActionHelper::bntIcon('Retour', 'bi bi-arrow-left', 'list.php', 'btn-secondary'),
            ActionHelper::bntIcon('Modifier', 'bi bi-pencil-square', 'edit.php?id=' . htmlspecialchars($admin->getId()), 'btn-warning'),
        ]
    ) ?>

    
   <div class="row">
        <!-- Informations générales -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    
                    <h3><?= htmlspecialchars($admin->getPrenom() . ' ' . $admin->getNom()) ?></h3>
                  
                        <?= htmlspecialchars($admin->getRoleName()) ?>
                    </span>
                    <p class="text-muted">Membre depuis <?= htmlspecialchars($admin->getCreatedAt()) ?></p>
                </div>
            </div>
        </div>

        <!-- Détails complets -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">Informations complètes</h4>
                    
                    <dl class="row">
                        <dt class="col-sm-3">ID</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($admin->getId()) ?></dd>

                        <dt class="col-sm-3">Nom</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($admin->getNom()) ?></dd>

                        <dt class="col-sm-3">Prénom</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($admin->getPrenom()) ?></dd>

                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($admin->getEmail()) ?></dd>

                        <dt class="col-sm-3">Rôle</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($admin->getRoleName()) ?></dd>

                        <dt class="col-sm-3">Statut</dt>
                        <dd class="col-sm-9">
                            <span class="badge bg-<?= $admin->isActive() ? 'success' : 'secondary' ?>">
                                <?= $admin->isActive() ? 'Actif' : 'Inactif' ?>
                            </span>
                        </dd>

                        <dt class="col-sm-3">Dernière connexion</dt>
                        <dd class="col-sm-9"><?= $admin->getLastLogin() ? htmlspecialchars($admin->getLastLogin()) : 'Jamais' ?></dd>

                        <dt class="col-sm-3">Date de création</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($admin->getCreatedAt()) ?></dd>

                        <dt class="col-sm-3">Dernière mise à jour</dt>
                        <dd class="col-sm-9"><?= $admin->getUpdatedAt() ? htmlspecialchars($admin->getUpdatedAt()) : 'Aucune' ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../partials/footer.php'; ?>
