<?php
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/repositories/demande_repository.php';
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/pagination_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

AdminController::requirelogin();
$activeMenu = "demandes";
$baseUrl = 'list.php';
AdminController::checkAndRedirectPermission($activeMenu);
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
        $stat = $repository->statistiques();
    $resultRequete = $repository->search($search, $currentPage, 20, $orderBy);
    $data = $resultRequete['data'];
} catch (Exception $e) {
    $exceptionMessage = $e->getMessage();
}

?>

<?php include '../partials/header.php'; ?>
<main class="main-content">
    <?= ContentRendu::header(
        'Liste des demandes',
        [
           
        ]
    ); ?>

    <div class="row mb-4 g-3">
        <?php 
        // Configuration des cartes stats
        $statsCards = [
            [
                'title' => 'Demandes Total',
                'value' => $stat['total'] ?? 0,
                'daily' => $stat['du_jour']['total'] ?? 0,
                'icon' => 'fas fa-file-alt',
                'bg' => 'primary'
            ],
            [
                'title' => 'Demandes Traitées',
                'value' => $stat['pret'] ?? 0,
                'daily' => $stat['du_jour']['pret'] ?? 0,
                'icon' => 'fas fa-check-circle',
                'bg' => 'success'
            ],
            [
                'title' => 'En traitement',
                'value' => $stat['en_traitement'] ?? 0,
                'daily' => $stat['du_jour']['en_traitement'] ?? 0,
                'icon' => 'fas fa-clock',
                'bg' => 'warning'
            ],
            [
                'title' => 'Demandes Annulées',
                'value' => $stat['annule'] ?? 0,
                'daily' => $stat['du_jour']['annule'] ?? 0,
                'icon' => 'fas fa-exclamation-triangle',
                'bg' => 'danger'
            ]
        ];
        
        foreach ($statsCards as $card): ?>
        <div class="col-md-3">
            <div class="card text-white bg-<?= $card['bg'] ?> stat-card h-100">
                <div class="card-body d-flex align-items-center py-2 px-3">
                    <div class="me-2">
                        <i class="<?= $card['icon'] ?>" style="font-size: 2.2rem;" aria-hidden="true"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1"><?= $card['title'] ?></h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-text mb-1"><?= $card['value'] ?></h5>
                            <small class="card-text text-white-50">
                                <?= $card['daily'] ? '+' . $card['daily'] . '% aujourd\'hui' : 'N/A' ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="container mt-4">
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
                ['Référence',  'Type d\'acte','Quantité', 'frais', 'Statut',  'Date', 'Actions'],
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
                        TableHelper::renderStatut(htmlspecialchars($demande->getStatut()),$demande->getStatut()=='pret'?'green':'orange'),
                        htmlspecialchars($demande->getDateDemande()),
                        ActionHelper::generateActions([ // Actions
                        ActionHelper::bntIcon( 'bi bi-eye', "details.php?id=" . $demande->getId(), 'btn-outline-info'),
                        ActionHelper::bntIcon( 'bi bi-gear', "traitement.php?id=" . $demande->getId(), 'btn-outline-info'),
                       
                        ActionHelper::bntIcon( 'bi bi-trash', "delete.php?id=" . $demande->getId(), 'btn-outline-danger'),
                    ]),
                    ];
                }, $data)
            )
        ); ?>
    </div>
</main>

<?php include '../partials/footer.php'; ?>