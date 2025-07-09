<?php
require_once __DIR__ . '/../../app/config/constants.php';
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/repositories/acte_naissance_repository.php';
require_once __DIR__ . '/../../app/models/acte_naissance_model.php';
require_once __DIR__ . '/../../app/config/database.php'; 
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
    if (isset($_POST['del'])) {
        $acteNaissanceRepository->delete(intval($id));
        $successMessage = "L'acte de naissance a été supprimé avec succès.";
    } else {
        $acteNaissanceModel = $acteNaissanceRepository->findById(intval($id));
        if ($acteNaissanceModel === null) {
            die('Acte de naissance non trouvé.');
        }
    }
} catch (PDOException $e) {
    $exceptionMessage = "Erreur lors de la suppression de l'acte de naissance : " . $e->getMessage();
}

?>

<?php include '../partials/header.php'; ?>

<main class="main-content">
    <?= ContentRendu::header(
        "Suppression d'un acte de naissance",
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
                <p>Êtes-vous sûr de vouloir supprimer l'acte de naissance suivant ?</p>
                <ul>
                    <li><strong>Numéro :</strong> <?= htmlspecialchars($acteNaissanceModel->getNumeroRegistre()) ?></li>
                    <li><strong>Nom :</strong> <?= htmlspecialchars($acteNaissanceModel->getNom()) ?></li>
                    <li><strong>Prénoms :</strong> <?= htmlspecialchars($acteNaissanceModel->getPrenoms()) ?></li>
                    <li><strong>Date de naissance :</strong> <?= htmlspecialchars($acteNaissanceModel->getDateNaissance()) ?></li>
                    <li><strong>Lieu de naissance :</strong> <?= htmlspecialchars($acteNaissanceModel->getLieuNaissance()) ?></li>
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