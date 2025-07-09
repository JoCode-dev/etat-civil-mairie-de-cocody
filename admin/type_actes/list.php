<?php
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/dropdown_helper.php';
require_once __DIR__ . '/../../app/helpers/pagination_helper.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/repositories/type_acte_repository.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';
 
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
$stat = [];
$resultRequete = [
    'data' => [],
    'total' => 0,
    'perPage' => 20,
    'totalPages' => 0
];

try {
    $stat = $typeActeRepository->statistiques();
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

    <div class="row mb-4 g-3">
        <?php
        // Configuration des cartes stats
        $statsCards = [
             [
                'title' => 'Total',
                'value' => $stat['total_types'] ?? 0,
                'daily' => null,
                'icon' => 'bi bi-file-earmark-text',
                'bg' => 'primary'
            ],
            [
                'title' => 'Actifs',
                'value' => $stat['active_types'] ?? 0,
                 'daily' => null,
                'icon' => 'bi bi-check-circle',
                'bg' => 'success'
            ],
            [
                'title' => 'Inactifs',
                'value' => $stat['inactive_types'] ?? 0,
                 'daily' => null,
                'icon' => 'bi bi-x-circle',
                'bg' => 'warning'
            ],
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