<?php

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/dropdown_helper.php';
require_once __DIR__ . '/../../app/helpers/pagination_helper.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/repositories/acte_mariage_repository.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

AdminController::requirelogin();
$activeMenu = "mariages";
$baseUrl = 'list.php';
AdminController::checkAndRedirectPermission($activeMenu);
$acteMariageRepository = new ActeMariageRepository($db);

$currentPage = isset($_GET['page']) ? intval(htmlspecialchars($_GET['page'])) : 1;
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$orderBy = isset($_GET['orderBy']) ? htmlspecialchars($_GET['orderBy']) : 'date_mariage';
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
    $resultRequete = $acteMariageRepository->search(
        $search,
        $currentPage,
        20,
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
        'Actes de Mariage',
        [
            ActionHelper::bntIconLabel('Ajouter', 'bi bi-plus', "edit.php", 'btn-success'),
        ]
    ); ?>

    <!-- Card de recherche et filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <?= ContentRendu::searchField(); ?>
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

        // Affichage de la table
        echo TableHelper::table(
            TableHelper::actionsBar(
                $resultRequete['total'],
                count($data),
                DropdownHelper::render(
                    'Plus de filtres',
                    'bi bi-funnel',
                    [
                        ['label' => 'Date de Mariage', 'url' => $baseUrl . '?orderBy=date_mariage'],
                        ['label' => 'Lieu de Mariage', 'url' => $baseUrl . '?orderBy=lieu_mariage'],
                        ['label' => 'Nom Conjoint Homme', 'url' => $baseUrl . '?orderBy=nom_conjoint_homme'],
                        ['label' => 'Nom Conjoint Femme', 'url' => $baseUrl . '?orderBy=nom_conjoint_femme'],
                    ],
                    $orderBy
                )
            ),
            TableHelper::column(
                ['ID', 'Numéro', 'Date de Mariage', 'Epoux', 'Epouse', 'Actions'],
                true,
                true
            ),
            TableHelper::body(
                array_map(function ($acte) {
                    return [
                        htmlspecialchars($acte->getId()), // ID
                        htmlspecialchars($acte->getNumeroRegistre()), // Numéro
                        htmlspecialchars($acte->getDateMariage()), // Date de Mariage
                        htmlspecialchars($acte->getNomPrenomsEpoux()), 
                        htmlspecialchars($acte->getNomPrenomsEpouse()), // Conjoints
                        ActionHelper::generateActions([ // Actions
                            ActionHelper::bntIcon('bi bi-file-pdf', "pdf.php?id=" . $acte->getId(), 'btn-outline-info'),
                            ActionHelper::bntIcon('bi bi-eye', "details.php?id=" . $acte->getId(), 'btn-outline-info'),
                            ActionHelper::bntIcon('bi bi-pencil', "edit.php?id=" . $acte->getId(), 'btn-outline-warning'),
                            ActionHelper::bntIcon('bi bi-trash', "delete.php?id=" . $acte->getId(), 'btn-outline-danger'),
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