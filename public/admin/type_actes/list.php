<?php
require_once __DIR__ . '/../../../app/Config/Database.php';
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../../app/Helpers/dropdown_helper.php';
require_once __DIR__ . '/../../../app/Helpers/pagination_helper.php';
require_once __DIR__ . '/../../../app/Helpers/alert_helper.php';
require_once __DIR__ . '/../../../app/Helpers/ActionHelper.php';
require_once __DIR__ . '/../../../app/Repositories/type_acte_repository.php';
require_once __DIR__ . '/../../../app/Controllers/AdminController.php';
 
AdminController::requirelogin();
$activeMenu = "type_actes";
$baseUrl = 'list.php';
AdminController::checkAndRedirectPermission($activeMenu);
$typeActeRepository = new TypeActeRepository($db);

$currentPage = isset($_GET['page']) ? intval(htmlspecialchars($_GET['page'])) : 1;
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$orderBy = isset($_GET['orderBy']) ? htmlspecialchars($_GET['orderBy']) : 'libelle';
$errorMessage = null;
$exceptionMessage = null;
$data = [];
$resultRequete = [
    'data' => [],
    'total' => 0,
    'perPage' => 20,
    'totalPages' => 0
];

try {
    $resultRequete = $typeActeRepository->search(
       $currentPage,
        $search,
        $orderBy
    );
    $data = $resultRequete['data'];
} catch (Exception $e) {
    $exceptionMessage = $e->getMessage();
}

?>

<?php include '../partials/header.php'; ?>
<main class="main-content">
    <?= ContentRendu::header(
        'Types d\'actes',
        [
            ActionHelper::bntIconLabel('Ajouter', 'bi bi-plus', "edit.php", 'btn-success'),
        ]
    ); ?>

    <div class="card mb-4">
        <div class="card-body">
            <?= ContentRendu::searchField() ?>
        </div>
    </div>

    <div class="container mt-4">
        <?php
        if (!empty($errorMessage)) {
            echo AlertHelper::error($errorMessage);
        }

        if (!empty($exceptionMessage)) {
            echo AlertHelper::exception($exceptionMessage);
        }

        echo TableHelper::table(
            TableHelper::actionsBar(
                $resultRequete['total'],
                count($data),
                DropdownHelper::render(
                    'Plus de filtres',
                    'bi bi-funnel',
                    [
                        ['label' => 'Libellé', 'url' => $baseUrl . '?orderBy=libelle'],
                        ['label' => 'Frais', 'url' => $baseUrl . '?orderBy=frais'],
                        ['label' => 'Date de création', 'url' => $baseUrl . '?orderBy=created_at'],
                    ],
                    $orderBy
                )
            ),
            TableHelper::column(
                ['ID', 'Code', 'Libellé', 'Frais', 'Statut', 'Actions'],
                true,
                true
            ),
            TableHelper::body(
                array_map(function ($typeActe) {
                    return [
                        htmlspecialchars($typeActe->getId()),
                        htmlspecialchars($typeActe->getCode()),
                        htmlspecialchars($typeActe->getLibelle()),
                        htmlspecialchars(number_format($typeActe->getFrais(), 2) . ' FCFA'),
                        TableHelper::renderStatut($typeActe->isStatut() ? 'Actif' : 'Inactif', $typeActe->isStatut() ? 'green' : 'red'),
                        ActionHelper::generateActions([
                            ActionHelper::bntIcon('bi bi-eye', "details.php?id=" . $typeActe->getId(), 'btn-outline-info'),
                            ActionHelper::bntIcon('bi bi-pencil', "edit.php?id=" . $typeActe->getId(), 'btn-outline-warning'),
                            ActionHelper::bntIcon('bi bi-trash', "delete.php?id=" . $typeActe->getId(), 'btn-outline-danger'),
                        ]),
                    ];
                }, $data)
            ),
            PaginationHelper::render($currentPage, $resultRequete['totalPages'], $baseUrl)
        );
        ?>
    </div>
</main>

<?php include '../partials/footer.php'; ?>