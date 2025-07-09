<?php

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/repositories/demande_repository.php';
require_once __DIR__ . '/../admin/table_helper.php';
require_once __DIR__ . '/../app/helpers/action_helper.php';
require_once __DIR__ . '/../admin/content_render.php';
require_once __DIR__ . '/../app/helpers/pagination_helper.php';
require_once __DIR__ . '/../app/controllers/citoyen_controller.php';

CitoyenController::requirelogin();
$baseUrl = 'citoyen/demandes.php';
$repository = new DemandeRepository($db);

$currentPage = isset($_GET['page']) ? intval(htmlspecialchars($_GET['page'])) : 1;
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$orderBy = isset($_GET['orderBy']) ? htmlspecialchars($_GET['orderBy']) : 'date_demande';
$data = [];
$stat = [];
$resultRequete = [
    'data' => [],
    'total' => 0,
    'perPage' => 20,
    'totalPages' => 0
];

try {
    $stat = $repository->statistiques($_SESSION['citoyen_id']);
    $resultRequete = $repository->search($search, $currentPage, 20, $orderBy, 'DESC', $_SESSION['citoyen_id']);
    $data = $resultRequete['data'];
} catch (Exception $e) {
    $exceptionMessage = $e->getMessage();
}

?>

<?php require_once __DIR__ . '/includes/header.php'; ?>


<main class="main-content">
    <h5 class="m-4">Mes demandes</h5>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary stat-card h-100">
                <div class="card-body d-flex align-items-center py-2 px-3">
                    <div class="me-2">
                        <i class="fas fa-file-alt" style="font-size: 2.2rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1">Demandes Total</h6>

                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-text mb-1"><?php echo $stat['total'] ?></h3>
                                <small class="card-text text-white-50"><?php echo '+' . $stat['du_jour']['total'] ?>% aujourd'hui</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success stat-card h-100">
                <div class="card-body d-flex align-items-center py-2 px-3">
                    <div class="me-2">
                        <i class="fas fa-check-circle" style="font-size: 2.2rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1">Demandes Traitées</h6>

                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-text mb-1"><?php echo $stat['pret'] ?></h3>
                                <small class="card-text text-white-50"><?php echo '+' . $stat['du_jour']['pret'] ?>% aujourd'hui</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-warning stat-card h-100">
                <div class="card-body d-flex align-items-center py-2 px-3">
                    <div class="me-2">
                        <i class="fas fa-clock" style="font-size: 2.2rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1">Demandes en traitement</h6>

                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-text mb-1"><?php echo $stat['en_traitement'] ?></h3>
                                <small class="card-text text-white-50"><?php echo '+' . $stat['du_jour']['en_traitement'] ?>% aujourd'hui</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger stat-card h-100">
                <div class="card-body d-flex align-items-center py-2 px-3">
                    <div class="me-2">
                        <i class="fas fa-exclamation-triangle" style="font-size: 2.2rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1">Demandes Annulées</h6>

                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-text mb-1"><?php echo $stat['annule'] ?></h3>
                                <small class="card-text text-white-50"><?php echo '+' . $stat['du_jour']['annule'] ?>% aujourd'hui</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="m-4">
        <?php if (!empty($exceptionMessage)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($exceptionMessage) ?></div>
        <?php endif; ?>

        <?= TableHelper::table(
            TableHelper::actionsBar(
                $resultRequete['total'],
                count($data),
                PaginationHelper::render($currentPage, $resultRequete['totalPages'], $baseUrl)
            ),
            TableHelper::column(
                ['Référence',  'Type d\'acte', 'Quantité', 'frais', 'Statut',  'Date',  'Actions'],
                true,
                true
            ),
            TableHelper::body(
                array_map(function ($demande) {
                    return [
                        htmlspecialchars($demande->getReference()),
                        htmlspecialchars($demande->getActeLibelle()),
                        htmlspecialchars($demande->getNombreActes()),
                        htmlspecialchars($demande->getTotalFrais()),
                        TableHelper::renderStatut(
                            htmlspecialchars($demande->getStatut() == 'en_attente' ? 'Non payé' : $demande->getStatut()),
                            $demande->getStatut() == 'pret' ? 'green' : 'orange'
                        ),

                        htmlspecialchars($demande->getDateDemande()),
                        ActionHelper::generateActions([ // Actions
                            ActionHelper::bntIcon('bi bi-eye', "demande.php?id=" . $demande->getId(), 'btn-outline-info'),
                            ($demande->getStatut() == 'en_attente') ? ActionHelper::bntIconLabel('Payé', 'bi bi-money-bill', "/paiement.php?reference=" . $demande->getReference(), 'btn-outline-info') : ''

                        ]),
                    ];
                }, $data)
            )
        ); ?>
    </div>
</main>

<?php require_once  'includes/footer.php'; ?>