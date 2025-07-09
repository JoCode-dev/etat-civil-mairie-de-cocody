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
$activeMenu = "admins";
AdminController::checkAndRedirectPermission($activeMenu);
$adminRepository = new AdminRepository($db);
$roleRepository = new RoleRepository($db);
$roles = $roleRepository->findAll();
$adminModel = new AdminModel();
$contenuRendu = new ContentRendu();

$errors = [];
$success = false;
$exceptionMessage = null;

function validateFormData($data, &$errors)
{
    if (empty($data['nom'])) {
        $errors['nom'] = "Le champ Nom est obligatoire.";
    }
    if (empty($data['prenom'])) {
        $errors['prenom'] = "Le champ Prénoms est obligatoire.";
    }
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Le champ Email est obligatoire et doit contenir une adresse email valide.";
    }

    if (isset($data['password']) && strlen($data['password']) < 8 && !isset($_GET['id'])) {
        $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères.";
    }


    return empty($errors);
}

// Traitement des actions (insert ou update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $adminModel->setNom($_POST['nom']);
        $adminModel->setPrenom($_POST['prenom']);
        $adminModel->setEmail($_POST['email']);
        $adminModel->setIsActive($_POST['statut']);
        $adminModel->setRoleId($_POST['roleid']);

        if (!empty($_POST['password']) && !isset($_GET['id'])) {
            $adminModel->setPasswordHash(password_hash($_POST['password'], PASSWORD_BCRYPT));
        }

        if (validateFormData($_POST, $errors)) {
            if (isset($_POST['insert'])) {
                $success = $adminRepository->create($adminModel);
            } elseif (isset($_POST['update']) && isset($_GET['id'])) {
                $adminModel->setId(intval($_GET['id']));
                $success = $adminRepository->update($adminModel);
            }
        }
    } catch (Exception $e) {
        $exceptionMessage = $e->getMessage();
    }
} elseif (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $admin = $adminRepository->findById($id);
    $adminModel = $admin ? $admin : new AdminModel();
}


?>

<?php include '../partials/header.php'; ?>


<main class="main-content">
    <div class="container mt-4">
        <?= ContentRendu::header(
            isset($_GET['id']) ? "Modifier l'administrateur" : "Ajouter un administrateur",
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
                            <?= isset($_GET['id']) ? "L'administrateur a été mis à jour avec succès." : "L'administrateur a été ajouté avec succès." ?>
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="edit.php" class="btn btn-primary">Nouveau</a>
                            <a href="list.php" class="btn btn-secondary">Retour</a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Formulaire -->
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4">
                                <?= FormItemHelper::input('nom', 'Nom', true, $adminModel->getNom() ?? '', 'text', $errors['nom'] ?? '') ?>
                            </div>
                            <div class="col-md-8">
                                <?= FormItemHelper::input('prenom', 'Prénoms', true, $adminModel->getPrenom() ?? '', 'text', $errors['prenom'] ?? '') ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <?= FormItemHelper::input('email', 'Email', true, $adminModel->getEmail() ?? '', 'email', $errors['email'] ?? '') ?>
                            </div>
                            <div class="col-md-8">
                                <?php if (!isset($_GET['id'])): ?>
                                    <?= FormItemHelper::input('password', 'Mot de passe', true, '', 'password', $errors['password'] ?? '') ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <?= FormItemHelper::select('statut', 'Statut', [
                                    '1' => 'Actif',
                                    '0' => 'Inactif'
                                ], true, $adminModel->isActive() ? '1' : '0', $errors['statut'] ?? '') ?>
                            </div>
                            <div class="col-md-8">
                                <?= FormItemHelper::select('roleid', 'Rôle', array_reduce($roles, function ($options, $role) {
                                    $options[$role->getId()] = $role->getTitre();
                                    return $options;
                                }, []), true, $adminModel->getRoleId() ?? '', $errors['roleid'] ?? '') ?>
                            </div>
                        </div>
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