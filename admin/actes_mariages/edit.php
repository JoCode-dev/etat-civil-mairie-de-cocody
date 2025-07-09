<?php
require_once __DIR__ . '/../../app/config/constants.php';
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/helpers/form_item_helper.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/repositories/acte_mariage_repository.php';
require_once __DIR__ . '/../../app/models/acte_mariage_model.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

AdminController::requirelogin();
$activeMenu = "mariages";
AdminController::checkAndRedirectPermission($activeMenu);
$acteMariageRepository = new ActeMariageRepository($db);
$acteMariageModel = new ActeMariageModel();

$errors = [];
$success = false;
$exceptionMessage = null;

function validateFormData($data, &$errors) {
    if (empty($data['numero_registre'])) {
        $errors['numero_registre'] = "Le champ Numéro de registre est obligatoire.";
    }
    if (empty($data['annee_registre'])) {
        $errors['annee_registre'] = "Le champ Année du registre est obligatoire.";
    }
    if (empty($data['date_mariage_lettre'])) {
        $errors['date_mariage_lettre'] = "Le champ Date de mariage en lettres est obligatoire.";
    }
    if (empty($data['date_mariage'])) {
        $errors['date_mariage'] = "Le champ Date de mariage est obligatoire.";
    }
    if (empty($data['lieu_mariage'])) {
        $errors['lieu_mariage'] = "Le champ Lieu de mariage est obligatoire.";
    }
    if (empty($data['nom_prenoms_epoux'])) {
        $errors['nom_prenoms_epoux'] = "Le champ Nom et prénoms de l'époux est obligatoire.";
    }
    if (empty($data['nom_prenoms_epouse'])) {
        $errors['nom_prenoms_epouse'] = "Le champ Nom et prénoms de l'épouse est obligatoire.";
    }
    if (empty($data['date_naissance_epoux'])) {
        $errors['date_naissance_epoux'] = "Le champ Date de naissance de l'époux est obligatoire.";
    }
    if (empty($data['date_naissance_epouse'])) {
        $errors['date_naissance_epouse'] = "Le champ Date de naissance de l'épouse est obligatoire.";
    }
    if (empty($data['nom_pere_epoux'])) {
        $errors['nom_pere_epoux'] = "Le champ Nom du père de l'époux est obligatoire.";
    }
    if (empty($data['nom_mere_epoux'])) {
        $errors['nom_mere_epoux'] = "Le champ Nom de la mère de l'époux est obligatoire.";
    }
    if (empty($data['nom_pere_epouse'])) {
        $errors['nom_pere_epouse'] = "Le champ Nom du père de l'épouse est obligatoire.";
    }
    if (empty($data['nom_mere_epouse'])) {
        $errors['nom_mere_epouse'] = "Le champ Nom de la mère de l'épouse est obligatoire.";
    }
    if (empty($data['temoin_homme'])) {
        $errors['temoin_homme'] = "Le champ Témoin homme est obligatoire.";
    }
    if (empty($data['temoin_femme'])) {
        $errors['temoin_femme'] = "Le champ Témoin femme est obligatoire.";
    }
    return empty($errors);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $acteMariageModel->setNumeroRegistre($_POST['numero_registre']);
        $acteMariageModel->setAnneeRegistre(intval($_POST['annee_registre']));
        $acteMariageModel->setDateMariageLettre($_POST['date_mariage_lettre']);
        $acteMariageModel->setDateMariage($_POST['date_mariage']);
        $acteMariageModel->setLieuMariage($_POST['lieu_mariage']);
        $acteMariageModel->setNomPrenomsEpoux($_POST['nom_prenoms_epoux']);
        $acteMariageModel->setNomPrenomsEpouse($_POST['nom_prenoms_epouse']);
        $acteMariageModel->setProfessionEpoux($_POST['profession_epoux'] ?? null);
        $acteMariageModel->setProfessionEpouse($_POST['profession_epouse'] ?? null);
        $acteMariageModel->setDateNaissanceEpoux($_POST['date_naissance_epoux']);
        $acteMariageModel->setDateNaissanceEpouse($_POST['date_naissance_epouse']);
        $acteMariageModel->setNomPereEpoux($_POST['nom_pere_epoux']);
        $acteMariageModel->setNomMereEpoux($_POST['nom_mere_epoux']);
        $acteMariageModel->setNomPereEpouse($_POST['nom_pere_epouse']);
        $acteMariageModel->setNomMereEpouse($_POST['nom_mere_epouse']);
        $acteMariageModel->setTemoinHomme($_POST['temoin_homme']);
        $acteMariageModel->setTemoinFemme($_POST['temoin_femme']);
        $acteMariageModel->setMentionDivorce($_POST['mention_divorce'] ?? null);
        $acteMariageModel->setCreateBy($_SESSION['user_id'] ?? 1);

        if (validateFormData($_POST, $errors)) {
            if (isset($_POST['insert'])) {
                $success = $acteMariageRepository->create($acteMariageModel);
            } elseif (isset($_POST['update']) && isset($_GET['id'])) {
                $acteMariageModel->setId(intval($_GET['id']));
                $success = $acteMariageRepository->update($acteMariageModel);
            }
        }
    } catch (Exception $e) {
        $exceptionMessage = $e->getMessage();
    }
} elseif (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $acte = $acteMariageRepository->findById($id);
    $acteMariageModel = $acte ? $acte : new ActeMariageModel();
}

?>

<?php include '../partials/header.php'; ?>

<main class="main-content">
    <div class="container mt-4">
        <?= ContentRendu::header(
            isset($_GET['id']) ? "Modifier l'acte de mariage" : "Ajouter un acte de mariage",
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
                            <?= isset($_GET['id']) ? "L'acte de mariage a été mis à jour avec succès." : "L'acte de mariage a été ajouté avec succès." ?>
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="edit.php" class="btn btn-primary">Nouveau</a>
                            <a href="list.php" class="btn btn-secondary">Retour</a>
                        </div>
                    </div>
                <?php else: ?>
                    <form action="" method="POST">  
                        <div class="row">
                            <div class="col-md-4">
                                <?= FormItemHelper::input('numero_registre', 'Numéro de registre', true, $acteMariageModel->getNumeroRegistre() ?? '', 'text', $errors['numero_registre'] ?? '') ?>
                            </div>
                            <div class="col-md-4">
                                <?= FormItemHelper::input('annee_registre', 'Année du registre', true, $acteMariageModel->getAnneeRegistre() ?? date('Y'), 'number', $errors['annee_registre'] ?? '') ?>
                            </div>
                            <div class="col-md-4">
                                <?= FormItemHelper::input('date_mariage_lettre', 'Date en lettres', true, $acteMariageModel->getDateMariageLettre() ?? '', 'text', $errors['date_mariage_lettre'] ?? '') ?>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <?= FormItemHelper::input('date_mariage', 'Date de mariage', true, $acteMariageModel->getDateMariage() ?? '', 'datetime-local', $errors['date_mariage'] ?? '') ?>
                            </div>
                            <div class="col-md-6">
                                <?= FormItemHelper::input('lieu_mariage', 'Lieu de mariage', true, $acteMariageModel->getLieuMariage() ?? '', 'text', $errors['lieu_mariage'] ?? '') ?>
                            </div>
                        </div>

                        <h5 class="mt-4">Informations sur l'époux</h5>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <?= FormItemHelper::input('nom_prenoms_epoux', 'Nom et prénoms', true, $acteMariageModel->getNomPrenomsEpoux() ?? '', 'text', $errors['nom_prenoms_epoux'] ?? '') ?>
                            </div>
                            <div class="col-md-6">
                                <?= FormItemHelper::input('profession_epoux', 'Profession', false, $acteMariageModel->getProfessionEpoux() ?? '', 'text', $errors['profession_epoux'] ?? '') ?>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <?= FormItemHelper::input('date_naissance_epoux', 'Date de naissance', true, $acteMariageModel->getDateNaissanceEpoux() ?? '', 'date', $errors['date_naissance_epoux'] ?? '') ?>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <?= FormItemHelper::input('nom_pere_epoux', 'Nom du père', true, $acteMariageModel->getNomPereEpoux() ?? '', 'text', $errors['nom_pere_epoux'] ?? '') ?>
                            </div>
                            <div class="col-md-6">
                                <?= FormItemHelper::input('nom_mere_epoux', 'Nom de la mère', true, $acteMariageModel->getNomMereEpoux() ?? '', 'text', $errors['nom_mere_epoux'] ?? '') ?>
                            </div>
                        </div>

                        <h5 class="mt-4">Informations sur l'épouse</h5>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <?= FormItemHelper::input('nom_prenoms_epouse', 'Nom et prénoms', true, $acteMariageModel->getNomPrenomsEpouse() ?? '', 'text', $errors['nom_prenoms_epouse'] ?? '') ?>
                            </div>
                            <div class="col-md-6">
                                <?= FormItemHelper::input('profession_epouse', 'Profession', false, $acteMariageModel->getProfessionEpouse() ?? '', 'text', $errors['profession_epouse'] ?? '') ?>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <?= FormItemHelper::input('date_naissance_epouse', 'Date de naissance', true, $acteMariageModel->getDateNaissanceEpouse() ?? '', 'date', $errors['date_naissance_epouse'] ?? '') ?>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <?= FormItemHelper::input('nom_pere_epouse', 'Nom du père', true, $acteMariageModel->getNomPereEpouse() ?? '', 'text', $errors['nom_pere_epouse'] ?? '') ?>
                            </div>
                            <div class="col-md-6">
                                <?= FormItemHelper::input('nom_mere_epouse', 'Nom de la mère', true, $acteMariageModel->getNomMereEpouse() ?? '', 'text', $errors['nom_mere_epouse'] ?? '') ?>
                            </div>
                        </div>

                        <h5 class="mt-4">Témoins</h5>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <?= FormItemHelper::input('temoin_homme', 'Témoin homme', true, $acteMariageModel->getTemoinHomme() ?? '', 'text', $errors['temoin_homme'] ?? '') ?>
                            </div>
                            <div class="col-md-6">
                                <?= FormItemHelper::input('temoin_femme', 'Témoin femme', true, $acteMariageModel->getTemoinFemme() ?? '', 'text', $errors['temoin_femme'] ?? '') ?>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <?= FormItemHelper::textarea('mention_divorce', 'Mention de divorce', false, $acteMariageModel->getMentionDivorce() ?? '', $errors['mention_divorce'] ?? '') ?>
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