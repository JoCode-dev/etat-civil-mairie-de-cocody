<?php


require_once __DIR__ . '/../../../app/Config/Database.php';
require_once __DIR__ . '/../../../app/Repositories/demande_repository.php';
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../../app/Helpers/pagination_helper.php';
require_once __DIR__ . '/../../../app/Helpers/ActionHelper.php';
require_once __DIR__ . '/../../../app/Controllers/AdminController.php';

AdminController::requirelogin();
$activeMenu = "demandes";
$baseUrl = 'list.php';
AdminController::checkAndRedirectPermission($activeMenu);
$repository = new DemandeRepository($db);

$currentPage = isset($_GET['page']) ? intval(htmlspecialchars($_GET['page'])) : 1;
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$orderBy = isset($_GET['orderBy']) ? htmlspecialchars($_GET['orderBy']) : 'date_demande';
$data = [];
$resultRequete = [
    'data' => [],
    'total' => 0,
    'perPage' => 20,
    'totalPages' => 0
];

try {
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
                        ActionHelper::bntIcon( 'bi bi-gear', "traitement?id=" . $demande->getId(), 'btn-outline-info'),
                        ActionHelper::bntIcon( 'bi bi-pencil', "edit.php?id=" . $demande->getId(), 'btn-outline-warning'),
                        ActionHelper::bntIcon( 'bi bi-trash', "delete.php?id=" . $demande->getId(), 'btn-outline-danger'),
                    ]),
                    ];
                }, $data)
            )
        ); ?>
    </div>
</main>

<?php include '../partials/footer.php'; ?>