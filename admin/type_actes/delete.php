<?php
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/helpers/admin_helper.php';
require_once __DIR__ . '/../../app/helpers/form_item_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/repositories/type_acte_repository.php';
require_once __DIR__ . '/../../app/models/type_acte_model.php';
require_once __DIR__ . '/../../app/config/database.php';
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
    if (isset($_POST['del'])) {
        $typeActeRepository->delete(intval($id));
        $successMessage = "Le type d'acte a été supprimé avec succès.";
    } else {
        $typeActe = $typeActeRepository->findById(intval($id));
        if ($typeActe === null) {
            die('Type d\'acte non trouvé.');
        }
    }
} catch (PDOException $e) {
    $exceptionMessage = "Erreur lors de la suppression du type d'acte : " . $e->getMessage();
}

?>

<?php include '../partials/header.php'; ?>

<main class="main-content">
    <?= ContentRendu::header(
        "Suppression d'un type d'acte",
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
                <p>Êtes-vous sûr de vouloir supprimer le type d'acte suivant ?</p>
                <ul>
                    <li><strong>Code :</strong> <?= htmlspecialchars($typeActe->getCode()) ?></li>
                    <li><strong>Libellé :</strong> <?= htmlspecialchars($typeActe->getLibelle()) ?></li>
                    <li><strong>Frais :</strong> <?= htmlspecialchars(number_format($typeActe->getFrais(), 2)) ?> FCFA</li>
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