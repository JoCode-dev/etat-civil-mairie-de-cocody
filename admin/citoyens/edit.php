<?php
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/helpers/admin_helper.php';
require_once __DIR__ . '/../../app/helpers/form_item_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/repositories/citoyen_repository.php';
require_once __DIR__ . '/../../app/models/citoyen_model.php';
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

AdminController::requirelogin();
$activeMenu = "citoyens";
AdminController::checkAndRedirectPermission($activeMenu);
$citoyenRepository = new CitoyenRepository($db);
$citoyenModel = new CitoyenModel();
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
        $citoyenModel->setNom($_POST['nom']);
        $citoyenModel->setPrenom($_POST['prenom']);
        $citoyenModel->setEmail($_POST['email']);
        $citoyenModel->setStatut($_POST['statut']);

        if (!empty($_POST['password']) && !isset($_GET['id'])) {
            $citoyenModel->setPasswordHash(password_hash($_POST['password'], PASSWORD_BCRYPT));
        }

        if (validateFormData($_POST, $errors)) {
            if (isset($_POST['insert'])) {
                $success = $citoyenRepository->create($citoyenModel);
            } elseif (isset($_POST['update']) && isset($_GET['id'])) {
                $citoyenModel->setId(intval($_GET['id']));
                $success = $citoyenRepository->update($citoyenModel);
            }
        }
    } catch (Exception $e) {
        $exceptionMessage = $e->getMessage();
    }
} elseif (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $admin = $citoyenRepository->findById($id);
    $citoyenModel = $admin ? $admin : new AdminModel();
}


?>

<?php include '../partials/header.php'; ?>


<main class="main-content">
    <div class="container mt-4">
        <?= ContentRendu::header(
            isset($_GET['id']) ? "Modifier un citoyen" : "Ajouter un citoyen",
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
                            <?= isset($_GET['id']) ? "Le citoyen a été mis à jour avec succès." : "Le citoyen a été ajouté avec succès." ?>
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="edit.php" class="btn btn-primary">Nouveau</a>
                            <a href="list.php" class="btn btn-secondary">Retour</a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Formulaire -->
                    <form action="" method="POST" enctype="multipart/form-data">
                        <?= FormItemHelper::input('nom', 'Nom', true, $citoyenModel->getNom() ?? '', 'text', $errors['nom'] ?? '') ?>
                        <?= FormItemHelper::input('prenom', 'Prénoms', true, $citoyenModel->getPrenom() ?? '', 'text', $errors['prenom'] ?? '') ?>
                        <?= FormItemHelper::input('email', 'Email', true, $citoyenModel->getEmail() ?? '', 'email', $errors['email'] ?? '') ?>
                        <?php if (!isset($_GET['id'])): ?>
                            <?= FormItemHelper::input('password', 'Mot de passe', true, '', 'password', $errors['password'] ?? '') ?>
                        <?php endif; ?>
                        <?= FormItemHelper::select('statut', 'Statut', [
                            '1' => 'Actif',
                            '0' => 'Inactif'
                        ], true, $citoyenModel->isActive() ? '1' : '0', $errors['statut'] ?? '') ?>
                       

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