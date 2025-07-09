<?php
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/repositories/role_repository.php';
require_once __DIR__ . '/../../app/models/role_model.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

AdminController::requirelogin();
$activeMenu = "roles";
AdminController::checkAndRedirectPermission($activeMenu);
$roleRepository = new RoleRepository($db);
$id = $_GET['id'] ?? null;

if ($id === null) {
    die('ID du rôle non fourni.');
}

try {
    if (isset($_POST['del'])) {
        $roleRepository->delete(intval($id));
        $successMessage = "Le rôle a été supprimé avec succès.";
    } else {
        $roleModel = $roleRepository->findById(intval($id));
        if ($roleModel === null) {
            die('Rôle non trouvé.');
        }
    }
} catch (PDOException $e) {
    $exceptionMessage = "Erreur lors de la suppression du rôle : " . $e->getMessage();
}

?>

<?php include '../partials/header.php'; ?>

<main class="main-content">
    <?= ContentRendu::header(
        "Suppression d'un rôle",
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
                <p>Êtes-vous sûr de vouloir supprimer le rôle suivant ?</p>
                <ul>
                    <li><strong>Titre :</strong> <?= htmlspecialchars($roleModel->getTitre()) ?></li>
                    <li><strong>Description :</strong> <?= htmlspecialchars($roleModel->getDescription()) ?></li>
                    <li><strong>Statut :</strong> <?= $roleModel->getIsActive() ? 'Actif' : 'Inactif' ?></li>
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