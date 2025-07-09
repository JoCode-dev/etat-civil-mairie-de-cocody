<?php
require_once __DIR__ . '/../../app/config/constants.php';
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/repositories/acte_mariage_repository.php';
require_once __DIR__ . '/../../app/models/acte_mariage_model.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

AdminController::requirelogin();
$activeMenu = "mariages";
AdminController::checkAndRedirectPermission($activeMenu);
$acteMariageRepository = new ActeMariageRepository($db);
$id = $_GET['id'] ?? null;

if ($id === null) {
    die('ID de l\'acte de mariage non fourni.');
}

try {
    if (isset($_POST['del'])) {
        $acteMariageRepository->delete(intval($id));
        $successMessage = "L'acte de mariage a été supprimé avec succès.";
    } else {
        $acteMariageModel = $acteMariageRepository->findById(intval($id));
        if ($acteMariageModel === null) {
            die('Acte de mariage non trouvé.');
        }
    }
} catch (PDOException $e) {
    $exceptionMessage = "Erreur lors de la suppression de l'acte de mariage : " . $e->getMessage();
}

?>

<?php include '../partials/header.php'; ?>

<main class="main-content">
    <?= ContentRendu::header(
        "Suppression d'un acte de mariage",
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
                <p>Êtes-vous sûr de vouloir supprimer l'acte de mariage suivant ?</p>
                <ul>
                    <li><strong>Numéro :</strong> <?= htmlspecialchars($acteMariageModel->getNumeroRegistre()) ?></li>
                    <li><strong>Date de mariage :</strong> <?= htmlspecialchars($acteMariageModel->getDateMariage()) ?></li>
                  
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