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

$role = null;
$exceptionMessage = null;

// Récupération des détails du rôle
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    try {
        $role = $roleRepository->findById($id);
        if (!$role) {
            $exceptionMessage = "Le rôle avec l'ID $id n'existe pas.";
        }
    } catch (Exception $e) {
        $exceptionMessage = $e->getMessage();
    }
} else {
    $exceptionMessage = "Aucun ID de rôle fourni.";
}

?>

<?php include '../partials/header.php'; ?>

<main class="main-content">
    <div class="container mt-4">
        <?= ContentRendu::header(
            'Détails du rôle',
            [
                ActionHelper::bntIcon('Retour', 'bi bi-arrow-left', 'list.php', 'btn-secondary'),
                ActionHelper::bntIcon('Modifier', 'bi bi-pencil', isset($role) ? "edit.php?id=" . $role->getId() : '#', 'btn-warning'),
            ]
        ) ?>

        <div class="card">
            <div class="card-body">
                <?php if (!empty($exceptionMessage)): ?>
                    <?= AlertHelper::error($exceptionMessage); ?>
                <?php elseif ($role): ?>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>ID</th>
                                <td><?= htmlspecialchars($role->getId()); ?></td>
                            </tr>
                            <tr>
                                <th>Titre</th>
                                <td><?= htmlspecialchars($role->getTitre()); ?></td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td><?= htmlspecialchars($role->getDescription()); ?></td>
                            </tr>
                            <tr>
                                <th>Statut</th>
                                <td>
                                    <?= $role->getIsActive() ? '<span class="badge bg-success">Actif</span>' : '<span class="badge bg-danger">Inactif</span>'; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Date de création</th>
                                <td><?= htmlspecialchars($role->getCreatedAt()); ?></td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include '../partials/footer.php'; ?>