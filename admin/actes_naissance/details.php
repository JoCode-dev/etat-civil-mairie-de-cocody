<?php
require_once __DIR__ . '/../../app/config/constants.php';
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/repositories/acte_naissance_repository.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

AdminController::requirelogin();
$activeMenu = "naissances";
AdminController::checkAndRedirectPermission($activeMenu);
$acteNaissanceRepository = new ActeNaissanceRepository($db);

$id = $_GET['id'] ?? null;

if ($id === null) {
    die('ID de l\'acte de naissance non fourni.');
}

try {
    $acte = $acteNaissanceRepository->findById(intval($id));
    if ($acte === null) {
        die('Acte de naissance non trouvé.');
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

?>

<?php include '../partials/header.php'; ?>

<main class="main-content">
    <?= ContentRendu::header(
        "Détails de l'acte de naissance",
        [
            ActionHelper::bntIcon('Retour', 'bi bi-arrow-left', 'list.php', 'btn-secondary'),
            ActionHelper::bntIcon('Modifier', 'bi bi-pencil-square', 'edit.php?id=' . htmlspecialchars($acte->getId()), 'btn-warning'),
        ]
    ) ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <h3><?= htmlspecialchars($acte->getNomComplet()) ?></h3>
                    <p class="text-muted">Numéro de registre : <?= htmlspecialchars($acte->getNumeroRegistre()) ?></p>
                    <p class="text-muted">Date de naissance : <?= htmlspecialchars($acte->getDateNaissance()) ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">Informations complètes</h4>
                    
                    <dl class="row">
                        <dt class="col-sm-3">ID</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($acte->getId()) ?></dd>

                        <dt class="col-sm-3">Numéro de registre</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($acte->getNumeroRegistre()) ?></dd>

                        <dt class="col-sm-3">Nom</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($acte->getNom()) ?></dd>

                        <dt class="col-sm-3">Prénoms</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($acte->getPrenoms()) ?></dd>

                        <dt class="col-sm-3">Date de naissance</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($acte->getDateNaissance()) ?></dd>

                        <dt class="col-sm-3">Heure de naissance</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($acte->getHeureNaissance()) ?></dd>

                        <dt class="col-sm-3">Lieu de naissance</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($acte->getLieuNaissance()) ?></dd>

                        <dt class="col-sm-3">Nom du père</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($acte->getNomPere()) ?></dd>

                        <dt class="col-sm-3">Profession du père</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($acte->getProfessionPere() ?? 'Non renseignée') ?></dd>

                        <dt class="col-sm-3">Nom de la mère</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($acte->getNomMere()) ?></dd>

                        <dt class="col-sm-3">Profession de la mère</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($acte->getProfessionMere() ?? 'Non renseignée') ?></dd>

                        <dt class="col-sm-3">Date de création</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($acte->getCreatedAt()) ?></dd>

                        <dt class="col-sm-3">Dernière mise à jour</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($acte->getUpdatedAt()) ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../partials/footer.php'; ?>