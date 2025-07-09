<?php
// filepath: c:\wamp64\www\etatcivil\public\admin\actes_naissance\edit.php
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/helpers/form_item_helper.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/repositories/acte_naissance_repository.php';
require_once __DIR__ . '/../../app/models/acte_naissance_model.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

AdminController::requirelogin();
$activeMenu = "naissances";
AdminController::checkAndRedirectPermission($activeMenu);
$acteNaissanceRepository = new ActeNaissanceRepository($db);
$acteNaissanceModel = new ActeNaissanceModel();

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
    if (empty($data['date_naissance'])) {
        $errors['date_naissance'] = "Le champ Date de naissance est obligatoire.";
    }
    if (empty($data['lieu_naissance'])) {
        $errors['lieu_naissance'] = "Le champ Lieu de naissance est obligatoire.";
    }
    return empty($errors);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $acteNaissanceModel->setNumeroRegistre($_POST['numero_registre']);
        $acteNaissanceModel->setAnneeRegistre($_POST['annee_registre'] ?? date('Y'));
        $acteNaissanceModel->setNom($_POST['nom']);
        $acteNaissanceModel->setPrenoms($_POST['prenoms']);
        $acteNaissanceModel->setDateNaissanceLettre($_POST['date_naissance_lettre'] ?? '');
        $acteNaissanceModel->setHeureNaissanceLettre($_POST['heure_naissance_lettre'] ?? '');
        $acteNaissanceModel->setDateNaissance($_POST['date_naissance']);
        $acteNaissanceModel->setHeureNaissance($_POST['heure_naissance'] ?? '00:00:00');
        $acteNaissanceModel->setLieuNaissance($_POST['lieu_naissance']);
        $acteNaissanceModel->setNomPere($_POST['nom_pere'] ?? '');
        $acteNaissanceModel->setProfessionPere($_POST['profession_pere'] ?? null);
        $acteNaissanceModel->setNomMere($_POST['nom_mere'] ?? '');
        $acteNaissanceModel->setProfessionMere($_POST['profession_mere'] ?? null);

        if (validateFormData($_POST, $errors)) {
            if (isset($_POST['insert'])) {
                $success = $acteNaissanceRepository->create($acteNaissanceModel);
            } elseif (isset($_POST['update']) && isset($_GET['id'])) {
                $acteNaissanceModel->setId(intval($_GET['id']));
                $success = $acteNaissanceRepository->update($acteNaissanceModel);
            }
        }
    } catch (Exception $e) {
        $exceptionMessage = $e->getMessage();
    }
} else if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $acte = $acteNaissanceRepository->findById($id);
    $acteNaissanceModel = $acte ? $acte : new ActeNaissanceModel();
}

?>

<?php include '../partials/header.php'; ?>

<main class="main-content">
    <div class="container mt-4">
        <?= ContentRendu::header(
            isset($_GET['id']) ? "Modifier l'acte de naissance" : "Ajouter un acte de naissance",
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
                            <?= isset($_GET['id']) ? "L'acte de naissance a été mis à jour avec succès." : "L'acte de naissance a été ajouté avec succès." ?>
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
                                <?= FormItemHelper::input('numero_registre', 'Numéro de registre', true, $acteNaissanceModel->getNumeroRegistre() ?? '', 'text', $errors['numero_registre'] ?? '') ?>
                            </div>
                            <div class="col-md-6">
                                <?= FormItemHelper::input('annee_registre', 'Année du registre', false, $acteNaissanceModel->getAnneeRegistre() ?? date('Y'), 'number', $errors['annee_registre'] ?? '') ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <?= FormItemHelper::input('nom', 'Nom', true, $acteNaissanceModel->getNom() ?? '', 'text', $errors['nom'] ?? '') ?>
                            </div>
                            <div class="col-md-6">
                                <?= FormItemHelper::input('prenoms', 'Prénoms', true, $acteNaissanceModel->getPrenoms() ?? '', 'text', $errors['prenoms'] ?? '') ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <?= FormItemHelper::input('date_naissance_lettre', 'Date de naissance (en lettres)', false, $acteNaissanceModel->getDateNaissanceLettre() ?? '', 'text', $errors['date_naissance_lettre'] ?? '') ?>
                            </div>
                            <div class="col-md-6">
                                <?= FormItemHelper::input('heure_naissance_lettre', 'Heure de naissance (en lettres)', false, $acteNaissanceModel->getHeureNaissanceLettre() ?? '', 'text', $errors['heure_naissance_lettre'] ?? '') ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <?= FormItemHelper::input('date_naissance', 'Date de naissance', true, $acteNaissanceModel->getDateNaissance() ?? '', 'date', $errors['date_naissance'] ?? '') ?>
                            </div>
                            <div class="col-md-6">
                                <?= FormItemHelper::input('heure_naissance', 'Heure de naissance', false, $acteNaissanceModel->getHeureNaissance() ?? '00:00', 'time', $errors['heure_naissance'] ?? '') ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <?= FormItemHelper::input('lieu_naissance', 'Lieu de naissance', true, $acteNaissanceModel->getLieuNaissance() ?? '', 'text', $errors['lieu_naissance'] ?? '') ?>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <?= FormItemHelper::input('nom_pere', 'Nom du père', false, $acteNaissanceModel->getNomPere() ?? '', 'text', $errors['nom_pere'] ?? '') ?>
                            </div>
                            <div class="col-md-6">
                                <?= FormItemHelper::input('profession_pere', 'Profession du père', false, $acteNaissanceModel->getProfessionPere() ?? '', 'text', $errors['profession_pere'] ?? '') ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <?= FormItemHelper::input('nom_mere', 'Nom de la mère', false, $acteNaissanceModel->getNomMere() ?? '', 'text', $errors['nom_mere'] ?? '') ?>
                            </div>
                            <div class="col-md-6">
                                <?= FormItemHelper::input('profession_mere', 'Profession de la mère', false, $acteNaissanceModel->getProfessionMere() ?? '', 'text', $errors['profession_mere'] ?? '') ?>
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