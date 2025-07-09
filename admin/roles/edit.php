<?php
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/helpers/form_item_helper.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/repositories/role_repository.php';
require_once __DIR__ . '/../../app/models/role_model.php';
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

AdminController::requirelogin();
$activeMenu = "roles";
AdminController::checkAndRedirectPermission($activeMenu);
$roleRepository = new RoleRepository($db);
$roleModel = new RoleModel();
$contenuRendu = new ContentRendu();

$errors = [];
$success = false;
$exceptionMessage = null;

function validateFormData($data, &$errors)
{
    if (empty($data['titre']) || strlen($data['titre']) < 3) {
        $errors['titre'] = "Le champ Titre est obligatoire et doit contenir au moins 3 caractères.";
    }
    if (empty($data['description'])) {
        $errors['description'] = "Le champ Description est obligatoire.";
    }
    return empty($errors);
}

// Traitement des actions (insert ou update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $roleModel->setTitre($_POST['titre']);
        $roleModel->setDescription($_POST['description']);
        $roleModel->setIsActive($_POST['statut']);

        if (validateFormData($_POST, $errors)) {
            if (isset($_POST['insert'])) {
                $success = $roleRepository->create($roleModel);
            } elseif (isset($_POST['update']) && isset($_GET['id'])) {
                $roleModel->setId(intval($_GET['id']));
                $success = $roleRepository->update($roleModel);
            }
        }
    } catch (Exception $e) {
        $exceptionMessage = $e->getMessage();
    }
} elseif (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $role = $roleRepository->findById($id);
    $roleModel = $role ? $role : new RoleModel();
}

?>

<?php include '../partials/header.php'; ?>

<main class="main-content">
    <div class="container mt-4">
        <?= ContentRendu::header(
            isset($_GET['id']) ? "Modifier le rôle" : "Ajouter un rôle",
            [
                ActionHelper::bntIcon('Retour', 'bi bi-arrow-left', 'list.php', 'btn-secondary'),
            ]
        ) ?>

        <div class="card">
            <div class="card-body">
                <?php
                // Affichage des alertes
                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        echo AlertHelper::error($error);
                    }
                }

                if (!empty($exceptionMessage)) {
                    echo AlertHelper::exception($exceptionMessage);
                }

                // Si succès, afficher le message de confirmation
                if ($success): ?>
                    <div class="text-center">
                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                        <p class="mt-3">
                            <?= isset($_GET['id']) ? "Le rôle a été mis à jour avec succès." : "Le rôle a été ajouté avec succès." ?>
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="edit.php" class="btn btn-primary">Nouveau</a>
                            <a href="list.php" class="btn btn-secondary">Retour</a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Formulaire -->
                    <form action="" method="POST" enctype="multipart/form-data">
                        <?= FormItemHelper::input('titre', 'Titre', true, $roleModel->getTitre() ?? '', 'text', $errors['titre'] ?? '') ?>
                        <?= FormItemHelper::textarea('description', 'Description', true, $roleModel->getDescription() ?? '', $errors['description'] ?? '') ?>
                        <?= FormItemHelper::select('statut', 'Statut', [
                            '1' => 'Actif',
                            '0' => 'Inactif'
                        ], true, $roleModel->getIsActive() ? '1' : '0', $errors['statut'] ?? '') ?>

                        <div class="d-flex justify-content-between">
                            <button type="submit" name="<?= isset($_GET['id']) ? 'update' : 'insert' ?>" class="btn btn-success">
                                <?= isset($_GET['id']) ? "Mettre à jour" : "Ajouter" ?>
                            </button>
                            <a href="list.php" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include '../partials/footer.php'; ?>