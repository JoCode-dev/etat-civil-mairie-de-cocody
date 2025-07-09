<?php
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/helpers/admin_helper.php';
require_once __DIR__ . '/../../app/helpers/form_item_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/repositories/admin_repository.php';
require_once __DIR__ . '/../../app/repositories/role_repository.php';
require_once __DIR__ . '/../../app/models/admin_model.php';
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

AdminController::requirelogin();
$activeMenu = "citoyens";
AdminController::checkAndRedirectPermission($activeMenu);
$citoyenRepository = new CitoyenRepository($db);
$id = $_GET['id'] ?? null;

if ($id === null) {
    die('ID du citoyen non fourni.');
}

try {
    if (isset($_POST['del'])) {
        $citoyenRepository->delete(intval($id));
        $successMessage = "citoyen a été supprimé avec succès.";
    } else {
        $adminModel = $citoyenRepository->findById(intval($id));
        if ($adminModel === null) {
            die('citoyen non trouvé.');
        }
    }
} catch (PDOException $e) {
    $exceptionMessage = "Erreur lors de la suppression du citoyen : " . $e->getMessage();
}



?>

<?php include '../partials/header.php'; ?>

<main class="main-content">
    <?= ContentRendu::header(
        "Suppression d'un citoyen",
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
                <p>Êtes-vous sûr de vouloir supprimer l'citoyen suivant ?</p>
                <ul>
                    <li><strong>Nom :</strong> <?= htmlspecialchars($adminModel->getNomComplet()) ?></li>
                    <li><strong>Email :</strong> <?= htmlspecialchars($adminModel->getEmail()) ?></li>
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