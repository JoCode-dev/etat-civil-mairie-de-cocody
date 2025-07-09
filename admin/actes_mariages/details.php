<?php
require_once __DIR__ . '/../../app/config/constants.php';
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/repositories/acte_mariage_repository.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

AdminController::requirelogin();
$activeMenu = "mariages";
AdminController::checkAndRedirectPermission($activeMenu);
$acteMariageRepository = new ActeMariageRepository($db);
$id = $_GET['id'] ?? null;

if ($id === null) {
    die("ID de l'acte de mariage non fourni.");
}

try {
    $acte = $acteMariageRepository->findById((int)$id);
    if (!$acte) {
        die("Acte de mariage non trouvé.");
    }
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<?php include '../partials/header.php'; ?>

<main class="main-content">
    <?= ContentRendu::header(
        "Détails de l'acte de mariage",
        [
            ActionHelper::bntIcon('Retour', 'bi bi-arrow-left', 'list.php', 'btn-secondary'),
            ActionHelper::bntIcon('Modifier', 'bi bi-pencil-square', 'edit.php?id=' . htmlspecialchars($acte->getId()), 'btn-warning'),
        ]
    ) ?>
 
 <div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            Informations de l'acte de mariage
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-4">Numéro acte</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($acte->getNumeroRegistre()) ?></dd>

                <dt class="col-sm-4">Date mariage</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($acte->getDateMariage()) ?></dd>

                <dt class="col-sm-4">Lieu mariage</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($acte->getLieuMariage()) ?></dd>

                <dt class="col-sm-4">Nom et Prénoms époux</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($acte->getNomPrenomsEpoux()) ?></dd>

                <dt class="col-sm-4">Date naissance époux</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($acte->getDateNaissanceEpoux()) ?></dd>

    
                <dt class="col-sm-4">Profession époux</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($acte->getProfessionEpoux()) ?></dd>

                <dt class="col-sm-4">Nom père époux</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($acte->getNomPereEpoux()) ?></dd>

                <dt class="col-sm-4">Nom mère époux</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($acte->getNomMereEpoux()) ?></dd>

                <dt class="col-sm-4">Nom Prénoms épouse</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($acte->getNomPrenomsEpoux()) ?></dd>

                <dt class="col-sm-4">Date naissance épouse</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($acte->getDateNaissanceEpouse()) ?></dd>

                

                <dt class="col-sm-4">Profession épouse</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($acte->getProfessionEpouse()) ?></dd>

                <dt class="col-sm-4">Nom père épouse</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($acte->getNomPereEpouse()) ?></dd>

                <dt class="col-sm-4">Nom mère épouse</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($acte->getNomMereEpouse()) ?></dd>

                <dt class="col-sm-4">Témoins 1</dt>
                <dd class="col-sm-8"><?= nl2br(htmlspecialchars($acte->getTemoinHomme())) ?></dd>

                <dt class="col-sm-4">Témoins 2</dt>
                <dd class="col-sm-8"><?= nl2br(htmlspecialchars($acte->getTemoinFemme())) ?></dd>

               
            </dl>
        </div>
    </div>
</div>

   
</main>

<?php include '../partials/footer.php'; ?>
