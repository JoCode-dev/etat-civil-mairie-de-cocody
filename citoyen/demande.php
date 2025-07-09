<?php
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/repositories/demande_repository.php';
require_once __DIR__ . '/../admin/table_helper.php';
require_once __DIR__ . '/../admin/content_render.php';
require_once __DIR__ . '/../app/helpers/pagination_helper.php';
require_once __DIR__ . '/../app/controllers/citoyen_controller.php';

CitoyenController::requirelogin();


$id = isset($_GET['id']) ? intval(htmlspecialchars($_GET['id'])) : null;

if (!$id) {
    die('ID de la demande non fourni.');
}

$repository = new DemandeRepository($db);

try {
    $demande = $repository->findById($id);
    if (!$demande) {
        die('Demande introuvable.');
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

?>

<?php require_once 'includes/header.php'; ?>

<main class="main-content">
    <h5 class="card-title m-4">Details demandes</h5>

    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Informations générales</h5>
                <dl class="row">
                    <dt class="col-sm-3">Référence</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($demande->getReference()) ?></dd>

                    <dt class="col-sm-3">Citoyen</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($demande->getCitoyenNom()) // Remplacez par le nom complet si disponible ?></dd>

                   <!--  <dt class="col-sm-3">Type d'acte</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($demande->getActeLibelle()) ?></dd> -->

                    <dt class="col-sm-3">Date de demande</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($demande->getDateDemande()) ?></dd>

                    <dt class="col-sm-3">Statut</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($demande->getStatut()) ?></dd>

                    <dt class="col-sm-3">Frais</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars(number_format($demande->getFraisUnitaire(), 2)) ?> FCFA</dd>

                    <dt class="col-sm-3">Date demande</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($demande->getDateDemande()) ?></dd>

                   
                </dl>

                <?php if ($demande->getFichierPath()): ?>
                    <h5 class="card-title mt-4">Fichier associé</h5>
                    <p><a href="<?= htmlspecialchars($demande->getFichierPath()) ?>" target="_blank">Télécharger le fichier</a></p>
                <?php endif; ?>

               
            </div>
        </div>
    </div>
</main>

<?php require_once  'includes/footer.php'; ?>