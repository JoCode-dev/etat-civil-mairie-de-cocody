<?php
require_once __DIR__ . '/../../app/config/constants.php';
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/repositories/acte_deces_repository.php';
require_once __DIR__ . '/../../app/models/acte_deces_model.php';
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

AdminController::requirelogin();
$activeMenu = "deces";
AdminController::checkAndRedirectPermission($activeMenu);
$acteDecesRepository = new ActeDecesRepository($db);
$id = $_GET['id'] ?? null;

if ($id === null) {
    die('ID de l\'acte de décès non fourni.');
}

try {
    if (isset($_POST['del'])) {
        $acteDecesRepository->delete(intval($id));
        $successMessage = "L'acte de décès a été supprimé avec succès.";
    } else {
        $acteDecesModel = $acteDecesRepository->findById(intval($id));
        if ($acteDecesModel === null) {
            die('Acte de décès non trouvé.');
        }
    }
} catch (PDOException $e) {
    $exceptionMessage = "Erreur lors de la suppression de l'acte de décès : " . $e->getMessage();
}

?>

<?php include '../partials/header.php'; ?>

<main class="main-content">
    <?= ContentRendu::header(
        "Suppression d'un acte de décès",
        [
            ActionHelper::bntIcon('Retour', 'bi bi-arrow-left', 'list.php', 'btn-secondary'),
        ]
    ) ?>

    <section class="container-sm" style="max-width: 700px;">
        <?php if (isset($successMessage)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($successMessage) ?>
            </div>
            <div class="text-center mt-4">
                <a href="list.php" class="btn btn-primary">Retour à la liste</a>
            </div>
        <?php elseif (isset($exceptionMessage)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($exceptionMessage) ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <p>Êtes-vous sûr de vouloir supprimer l'acte de décès suivant ?</p>
                <ul>
                    <li><strong>Numéro :</strong> <?= htmlspecialchars($acteDecesModel->getNumeroRegistre()) ?></li>
                    <li><strong>Nom :</strong> <?= htmlspecialchars($acteDecesModel->getNom()) ?></li>
                    <li><strong>Prénoms :</strong> <?= htmlspecialchars($acteDecesModel->getPrenoms()) ?></li>
                    <li><strong>Date de décès :</strong> <?= htmlspecialchars($acteDecesModel->getDateDeces()) ?></li>
                    <li><strong>Lieu de décès :</strong> <?= htmlspecialchars($acteDecesModel->getLieuDeces()) ?></li>
                </ul>
            </div>
            <form action="" method="POST">
                <input type="hidden" name="del" value="1">
                <div class="d-flex justify-content-between">
                    <a href="list.php" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                </div>
            </form>
        <?php endif; ?>
    </section>
</main>

<?php include '../partials/footer.php'; ?>