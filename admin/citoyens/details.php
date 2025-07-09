<?php
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/repositories/admin_repository.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';
require_once __DIR__ . '/../../app/repositories/citoyen_repository.php';

AdminController::requirelogin();
$activeMenu = "citoyens";
AdminController::checkAndRedirectPermission($activeMenu);
$citoyenRepository = new CitoyenRepository($db);

$id = $_GET['id'] ?? null;

if ($id === null) {
    die('ID du citoyen non fourni.');
}

try {
    $citoyen = $citoyenRepository->findById(intval($id));
    if ($citoyen === null) {
        die('citoyen non trouvé.');
    }

    // Exemple d'activités récentes (à remplacer par une requête réelle si nécessaire)
  
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}



?>

<?php include '../partials/header.php'; ?>

<main class="main-content">

     <?= ContentRendu::header(
        " Détails du citoyen",
        [
           ActionHelper::bntIcon('Retour', 'bi bi-arrow-left', 'list.php', 'btn-secondary'),
            ActionHelper::bntIcon('Modifier', 'bi bi-pencil-square', 'edit.php?id=' . htmlspecialchars($citoyen->getId()), 'btn-warning'),
        ]
    ) ?>

    
   <div class="row">
        <!-- Informations générales -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    
                    <h3><?= htmlspecialchars($citoyen->getPrenom() . ' ' . $citoyen->getNom()) ?></h3>
                  
                        
                    </span>
                    <p class="text-muted">Membre depuis <?= htmlspecialchars($citoyen->getCreatedAt()) ?></p>
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
                        <dd class="col-sm-9"><?= htmlspecialchars($citoyen->getId()) ?></dd>

                        <dt class="col-sm-3">Nom</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($citoyen->getNom()) ?></dd>

                        <dt class="col-sm-3">Prénom</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($citoyen->getPrenom()) ?></dd>

                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($citoyen->getEmail()) ?></dd>

                        

                        <dt class="col-sm-3">Statut</dt>
                        <dd class="col-sm-9">
                            <span class="badge bg-<?= $citoyen->getStatut() ? 'success' : 'secondary' ?>">
                                <?= $citoyen->getStatut() ? 'Actif' : 'Inactif' ?>
                            </span>
                        </dd>

                       
                        <dt class="col-sm-3">Date de création</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($citoyen->getCreatedAt()) ?></dd>

                        <dt class="col-sm-3">Dernière mise à jour</dt>
                        <dd class="col-sm-9"><?= $citoyen->getUpdatedAt() ? htmlspecialchars($citoyen->getUpdatedAt()) : 'Aucune' ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../partials/footer.php'; ?>
