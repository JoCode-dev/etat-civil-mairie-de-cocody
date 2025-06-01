<?php

require_once __DIR__ . '/../../app/Config/Database.php';
require_once __DIR__ . '/../../app/Repositories/demande_repository.php';
require_once __DIR__ . '/../admin/table_helper.php';
require_once __DIR__ . '/../admin/content_render.php';
require_once __DIR__ . '/../../app/Helpers/pagination_helper.php';
require_once __DIR__ . '/../../app/Controllers/citoyen_controller.php';

CitoyenController::requirelogin();
$baseUrl = 'citoyen/demandes.php';
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
    $resultRequete = $repository->search($search, $currentPage, 20, $orderBy,'DESC',$_SESSION['citoyen_id']);
    $data = $resultRequete['data'];
} catch (Exception $e) {
    $exceptionMessage = $e->getMessage();
}

?>

<?php require_once 'includes/header.php'; ?>

<main class="main-content">
   <h5 class="m-4">Mes demandes</h5>

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
                ['Référence',  'Type d\'acte','Quantité', 'frais', 'Statut',  'Date',  'Actions'],
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
                        '<a href="demande?id=' . htmlspecialchars($demande->getId()) . '" class="btn btn-outline-info btn-sm"><i class="bi bi-eye"></i> Voir</a>'
                    ];
                }, $data)
            )
        ); ?>
    </div>
</main>

<?php require_once  'includes/footer.php'; ?>