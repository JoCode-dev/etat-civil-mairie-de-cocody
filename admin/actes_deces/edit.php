<?php
require_once __DIR__ . '/../../app/config/constants.php';
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/helpers/form_item_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/repositories/acte_deces_repository.php';
require_once __DIR__ . '/../../app/models/acte_deces_model.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

AdminController::requirelogin();
$activeMenu = "deces";
AdminController::checkAndRedirectPermission($activeMenu);
$acteDecesRepository = new ActeDecesRepository($db);
$acteDecesModel = new ActeDecesModel();

$errors = [];
$success = false;
$exceptionMessage = null;

function validateFormData($data, &$errors)
{
    if (empty($data['numero_registre'])) {
        $errors['numero_registre'] = "Le champ Numéro de registre est obligatoire.";
    }
    if (empty($data['nom'])) {
        $errors['nom'] = "Le champ Nom est obligatoire.";
    }
    if (empty($data['prenoms'])) {
        $errors['prenoms'] = "Le champ Prénoms est obligatoire.";
    }
    if (empty($data['date_deces'])) {
        $errors['date_deces'] = "Le champ Date de décès est obligatoire.";
    }
    if (empty($data['date_deces_lettre'])) {
        $errors['date_deces_lettre'] = "Le champ Date de décès (en lettres) est obligatoire.";
    }
    if (empty($data['lieu_deces'])) {
        $errors['lieu_deces'] = "Le champ Lieu de décès est obligatoire.";
    }
    return empty($errors);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $acteDecesModel->setNumeroRegistre($_POST['numero_registre']);
        $acteDecesModel->setAnneeRegistre(intval($_POST['annee_registre']));
        $acteDecesModel->setNom($_POST['nom']);
        $acteDecesModel->setPrenoms($_POST['prenoms']);
        $acteDecesModel->setDateDecesLettre($_POST['date_deces_lettre']);
        $acteDecesModel->setDateDeces($_POST['date_deces']);
        $acteDecesModel->setLieuDeces($_POST['lieu_deces']);

        if (validateFormData($_POST, $errors)) {
            if (isset($_POST['insert'])) {
                $success = $acteDecesRepository->create($acteDecesModel);
            } elseif (isset($_POST['update']) && isset($_GET['id'])) {
                $acteDecesModel->setId(intval($_GET['id']));
                $success = $acteDecesRepository->update($acteDecesModel);
            }
        }
    } catch (Exception $e) {
        $exceptionMessage = $e->getMessage();
    }
} elseif (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $acte = $acteDecesRepository->findById($id);
    $acteDecesModel = $acte ? $acte : new ActeDecesModel();
}

?>

<?php include '../partials/header.php'; ?>

<main class="main-content">
    <div class="container mt-4">
        <?= ContentRendu::header(
            isset($_GET['id']) ? "Modifier l'acte de décès" : "Ajouter un acte de décès",
            [
                ActionHelper::bntIcon('Retour', 'bi bi-arrow-left', 'list.php', 'btn-secondary'),
            ]
        ) ?>

        <div class="card">
            <div class="card-body">
                <?php
                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        echo AlertHelper::error($error);
                    }
                }

                if (!empty($exceptionMessage)) {
                    echo AlertHelper::exception($exceptionMessage);
                }

                if ($success): ?>
                    <div class="text-center">
                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                        <p class="mt-3">
                            <?= isset($_GET['id']) ? "L'acte de décès a été mis à jour avec succès." : "L'acte de décès a été ajouté avec succès." ?>
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="edit.php" class="btn btn-primary">Nouveau</a>
                            <a href="list.php" class="btn btn-secondary">Retour</a>
                        </div>
                    </div>
                <?php else: ?>
                    <form action="" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <?= FormItemHelper::input('numero_registre', 'Numéro de registre', true, $acteDecesModel->getNumeroRegistre() ?? '', 'text', $errors['numero_registre'] ?? '') ?>
                            </div>
                            <div class="col-md-6">
                                <?= FormItemHelper::input('annee_registre', 'Année de registre', true, $acteDecesModel->getAnneeRegistre() ?? '', 'number', $errors['annee_registre'] ?? '') ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <?= FormItemHelper::input('nom', 'Nom', true, $acteDecesModel->getNom() ?? '', 'text', $errors['nom'] ?? '') ?>
                            </div>
                            <div class="col-md-6">
                                <?= FormItemHelper::input('prenoms', 'Prénoms', true, $acteDecesModel->getPrenoms() ?? '', 'text', $errors['prenoms'] ?? '') ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <?= FormItemHelper::input('date_deces_lettre', 'Date de décès (en lettres)', true, $acteDecesModel->getDateDecesLettre() ?? '', 'text', $errors['date_deces_lettre'] ?? '') ?>
                            </div>
                            <div class="col-md-6">
                                <?= FormItemHelper::input('date_deces', 'Date de décès', true, $acteDecesModel->getDateDeces() ?? '', 'date', $errors['date_deces'] ?? '') ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <?= FormItemHelper::input('lieu_deces', 'Lieu de décès', true, $acteDecesModel->getLieuDeces() ?? '', 'text', $errors['lieu_deces'] ?? '') ?>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
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