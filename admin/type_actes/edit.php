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
$typeActeModel = new TypeActeModel();

$errors = [];
$success = false;
$exceptionMessage = null;

function validateFormData($data, &$errors) {
    if (empty($data['code'])) {
        $errors['code'] = "Le champ Code est obligatoire.";
    }
    if (empty($data['libelle'])) {
        $errors['libelle'] = "Le champ Libellé est obligatoire.";
    }
    if (!is_numeric($data['frais'])) {
        $errors['frais'] = "Le champ Frais doit être un nombre.";
    }
    return empty($errors);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
         $fichierPath = FileHelper::upload();
        $typeActeModel->setCode($_POST['code']);
        $typeActeModel->setLibelle($_POST['libelle']);
        $typeActeModel->setDescription($_POST['description']);
        $typeActeModel->setDelaiTraitement(intval($_POST['delai_traitement']));
        $typeActeModel->setFrais(floatval($_POST['frais']));
        $typeActeModel->setFichierPath($fichierPath);
        $typeActeModel->setStatut(isset($_POST['statut']) && $_POST['statut'] === '1');

         // Gestion du fichier uploadé
       

        if (validateFormData($_POST, $errors)) {
            if (isset($_POST['insert'])) {
                $success = $typeActeRepository->create($typeActeModel);
            } elseif (isset($_POST['update']) && isset($_GET['id'])) {
                $typeActeModel->setId(intval($_GET['id']));
                $success = $typeActeRepository->update($typeActeModel);
            }
        }
    } catch (Exception $e) {
        $exceptionMessage = $e->getMessage();
    }
} elseif (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $typeActe = $typeActeRepository->findById($id);
    $typeActeModel = $typeActe ? $typeActe : new TypeActeModel();
}

?>

<?php include '../partials/header.php'; ?>

<main class="main-content">
    <div class="container mt-4">
        <?= ContentRendu::header(
            isset($_GET['id']) ? "Modifier le type d'acte" : "Ajouter un type d'acte",
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
                            <?= isset($_GET['id']) ? "Le type d'acte a été mis à jour avec succès." : "Le type d'acte a été ajouté avec succès." ?>
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="edit.php" class="btn btn-primary">Nouveau</a>
                            <a href="list.php" class="btn btn-secondary">Retour</a>
                        </div>
                    </div>
                <?php else: ?>
                    <form action="" method="POST">
                        <?= FormItemHelper::input('code', 'Code', true, $typeActeModel->getCode() ?? '', 'text', $errors['code'] ?? '') ?>
                        <?= FormItemHelper::input('libelle', 'Libellé', true, $typeActeModel->getLibelle() ?? '', 'text', $errors['libelle'] ?? '') ?>
                        <?= FormItemHelper::textarea('description', 'Description', false, $typeActeModel->getDescription() ?? '', $errors['description'] ?? '') ?>
                        <?= FormItemHelper::input('delai_traitement', 'Délai de traitement (minutes)', true, $typeActeModel->getDelaiTraitement() ?? 3, 'number', $errors['delai_traitement'] ?? '') ?>
                        <?= FormItemHelper::input('frais', 'Frais (FCFA)', true, $typeActeModel->getFrais() ?? 0.0, 'number', $errors['frais'] ?? '') ?>
                        <?= FormItemHelper::select('statut', 'Statut', [
                            '1' => 'Actif',
                            '0' => 'Inactif'
                        ], true, $typeActeModel->isStatut() ? '1' : '0', $errors['statut'] ?? '') ?>
 <div class="mb-3">
                                    <label for="fichier" class="form-label fw-bold">Signature</label>
                                    <input type="file" class="form-control" id="fichier" name="fichier" accept=".pdf,.jpg,.jpeg,.png" require>
                                    <div class="form-text">Formats acceptés: PDF, JPG, PNG (max 2MB)</div>
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