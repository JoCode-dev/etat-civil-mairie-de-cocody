<?php
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/repositories/type_acte_repository.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

AdminController::requirelogin();
$activeMenu = "type_actes";
AdminController::checkAndRedirectPermission($activeMenu);
$typeActeRepository = new TypeActeRepository($db);

$id = $_GET['id'] ?? null;

if ($id === null) {
    die('ID du type d\'acte non fourni.');
}

try {
    $typeActe = $typeActeRepository->findById(intval($id));
    if ($typeActe === null) {
        die('Type d\'acte non trouvé.');
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

?>

<?php include '../partials/header.php'; ?>

<main class="main-content">
    <?= ContentRendu::header(
        "Détails du type d'acte",
        [
            ActionHelper::bntIcon('Retour', 'bi bi-arrow-left', 'list.php', 'btn-secondary'),
            ActionHelper::bntIcon('Modifier', 'bi bi-pencil-square', 'edit.php?id=' . htmlspecialchars($typeActe->getId()), 'btn-warning'),
        ]
    ) ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <h3><?= htmlspecialchars($typeActe->getLibelle()) ?></h3>
                    <p class="text-muted">Code : <?= htmlspecialchars($typeActe->getCode()) ?></p>
                    <p class="text-muted">Créé le <?= htmlspecialchars($typeActe->getCreatedAt()) ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">Informations complètes</h4>
                    
                    <dl class="row">
                        <dt class="col-sm-3">ID</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($typeActe->getId()) ?></dd>

                        <dt class="col-sm-3">Code</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($typeActe->getCode()) ?></dd>

                        <dt class="col-sm-3">Libellé</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($typeActe->getLibelle()) ?></dd>

                        <dt class="col-sm-3">Description</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($typeActe->getDescription() ?? 'Aucune') ?></dd>

                        <dt class="col-sm-3">Délai de traitement</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($typeActe->getDelaiTraitement()) ?> jours</dd>

                        <dt class="col-sm-3">Frais</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars(number_format($typeActe->getFrais(), 2)) ?> FCFA</dd>

                        <dt class="col-sm-3">Statut</dt>
                        <dd class="col-sm-9">
                            <span class="badge bg-<?= $typeActe->isStatut() ? 'success' : 'secondary' ?>">
                                <?= $typeActe->isStatut() ? 'Actif' : 'Inactif' ?>
                            </span>
                        </dd>

                        <dt class="col-sm-3">Date de création</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($typeActe->getCreatedAt()) ?></dd>

                        <dt class="col-sm-3">Dernière mise à jour</dt>
                        <dd class="col-sm-9"><?= $typeActe->getUpdatedAt() ? htmlspecialchars($typeActe->getUpdatedAt()) : 'Aucune' ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../partials/footer.php'; ?>