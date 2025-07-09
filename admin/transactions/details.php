<?php
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/repositories/transaction_repository.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

AdminController::requirelogin();
$activeMenu = "transactions";
AdminController::checkAndRedirectPermission($activeMenu);
$transactionRepository = new PaiementRepository($db);

$id = $_GET['id'] ?? null;

if ($id === null) {
    die('ID de la transaction non fourni.');
}

try {
    $transaction = $transactionRepository->findById(intval($id));
    if ($transaction === null) {
        die('Transaction non trouvée.');
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

?>

<?php include '../partials/header.php'; ?>

<main class="main-content">
    <?= ContentRendu::header(
        "Détails de la transaction",
        [
            ActionHelper::bntIcon('Retour', 'bi bi-arrow-left', 'list.php', 'btn-secondary'),
        ]
    ) ?>

    <div class="row">
        <!-- Informations générales -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <h3>Transaction #<?= htmlspecialchars($transaction->getId()) ?></h3>
                    <p class="text-muted">Référence : <?= htmlspecialchars($transaction->getReference()) ?></p>
                    <p class="text-muted">Date : <?= htmlspecialchars($transaction->getDateTransaction()) ?></p>
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
                        <dd class="col-sm-9"><?= htmlspecialchars($transaction->getId()) ?></dd>

                        <dt class="col-sm-3">Demande ID</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($transaction->getDemandeId()) ?></dd>

                        <dt class="col-sm-3">Citoyen ID</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($transaction->getCitoyenId()) ?></dd>

                        <dt class="col-sm-3">Montant</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($transaction->getMontantFormatte()) ?></dd>

                        <dt class="col-sm-3">Méthode de paiement</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($transaction->getMethodePaiement()) ?></dd>

                        <dt class="col-sm-3">Statut</dt>
                        <dd class="col-sm-9">
                            <span class="badge bg-<?= $transaction->getStatut() === 'paye' ? 'success' : 'secondary' ?>">
                                <?= htmlspecialchars(ucfirst($transaction->getStatut())) ?>
                            </span>
                        </dd>

                        <dt class="col-sm-3">Date de transaction</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($transaction->getDateTransaction()) ?></dd>

                        <dt class="col-sm-3">Dernière mise à jour</dt>
                        <dd class="col-sm-9"><?= $transaction->getDateMiseAJour() ? htmlspecialchars($transaction->getDateMiseAJour()) : 'Aucune' ?></dd>
</dl>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../partials/footer.php'; ?>