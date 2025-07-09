<?php
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/repositories/demande_repository.php';
require_once __DIR__ . '/../../app/repositories/paiement_repository.php';
require_once __DIR__ . '/../../app/repositories/demande_statut_historique_repository.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';
require_once  'paiement_info_helper.php';
require_once  'statut_historique_helper.php';
require_once  'demande_helper.php';

AdminController::requirelogin();
$activeMenu = "demandes";

// Validation de l'ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die('ID de demande invalide');
}

// Initialisation des repositories
$demandeRepo = new DemandeRepository($db);

try {
    // Récupération des données
    $demande = $demandeRepo->findById($id, true);
    if (!$demande) {
        throw new Exception('Demande introuvable');
    }
    
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>

<?php include '../partials/header.php'; ?>

<main class="main-content">
    <div class="container-fluid px-4">
        <?= ContentRendu::header(
            "Détails de la demande #" . htmlspecialchars($demande->getReference()),
            [
                ActionHelper::bntIconLabel('Retour', 'bi-arrow-left', 'list.php', 'btn-outline-secondary'),
                ActionHelper::bntIconLabel('Traitement', 'bi-gear', 'traitement.php?search='.$demande->getReference(), 'btn-warning'),
            ]
        ); ?>

        <div class="row mt-4">
            <!-- Colonne Informations principales -->
            <div class="col-lg-6">
                 <?php echo DemandeHelper::showHtml($id) ?>

                <!-- Section Fichiers -->:
                <?php if ($demande->getFichierPath()): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-file-earmark me-2"></i>Documents associés</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="bi bi-filetype-pdf me-2"></i>
                                    Document principal
                                </span>
                                <a href="<?= htmlspecialchars($demande->getFichierPath()) ?>" 
                                   class="btn btn-sm btn-outline-primary"
                                   target="_blank">
                                    <i class="bi bi-download"></i> Télécharger
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Colonne Paiement et Historique -->
            <div class="col-lg-6">
                <!-- Section Paiement -->
                <?php echo PaiementInfoHelper::showHtml($id) ?>

                <!-- Section Historique des statuts -->
                <?php echo StatutHistoriqueHelper::showHtml($id) ?>
                
            </div>
        </div>
    </div>
</main>

     <link href="/etatcivil/assets/css/timeline.css" rel="stylesheet">

<?php include '../partials/footer.php'; ?>