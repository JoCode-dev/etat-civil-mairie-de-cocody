<?php
require_once __DIR__ . '/../../../app/Config/Database.php';
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../../app/Helpers/dropdown_helper.php';
require_once __DIR__ . '/../../../app/Helpers/pagination_helper.php';
require_once __DIR__ . '/../../../app/Helpers/alert_helper.php';
require_once __DIR__ . '/../../../app/Helpers/ActionHelper.php';
require_once __DIR__ . '/../../../app/Repositories/role_repository.php';
require_once __DIR__ . '/../../../app/Controllers/AdminController.php';

AdminController::requirelogin();
$activeMenu = "roles";
AdminController::checkAndRedirectPermission($activeMenu);
$roleRepository = new RoleRepository($db);
$currentPage = isset($_GET['page']) ? intval(htmlspecialchars($_GET['page'])) : 1;
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$orderBy = isset($_GET['orderBy']) ? htmlspecialchars($_GET['orderBy']) : 'titre';
$isActive = isset($_GET['isActive']) ? ($_GET['isActive'] === '1' ? true : ($_GET['isActive'] === '0' ? false : null)) : null;
$errorMessage = null;
$exceptionMessage = null;
$data = [];
$resultRequete = [
    'data' => [],
    'total' => 0,
    'perPage' => 20,
    'totalPages' => 0
];

$baseUrl = 'list.php';



try {
    $resultRequete = $roleRepository->search(
        $search, 
        $currentPage,
        $orderBy,
        $isActive,
        'ASC',
        20,
    );
    $data = $resultRequete['data'];
 } catch (Exception $e) {
    $exceptionMessage = $e->getMessage();
} 

?>

<?php include '../partials/header.php'; ?>
<main class="main-content">
    <?= ContentRendu::header(
        'Rôles',
        [
            ActionHelper::bntIconLabel('Ajouter', 'bi bi-plus', "edit.php", 'btn-success'),
        ]
    ); ?>

    <!-- Card de recherche et filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <?php echo ContentRendu::searchField(); ?>
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
                        ['label' => 'Titre', 'url' => $baseUrl . '?orderBy=titre'],
                        ['label' => 'Date de création', 'url' => $baseUrl . '?orderBy=created_at'],
                        ['label' => 'Actifs', 'url' => $baseUrl . '?isActive=1'],
                        ['label' => 'Inactifs', 'url' => $baseUrl . '?isActive=0'],
                    ],
                    $orderBy
                ),
            ),
            TableHelper::column(
                ['ID', 'Titre', 'Description', 'Statut', 'Actions'],
                true,
                true
            ),
            TableHelper::body(
                array_map(function ($role) {
                    return [
                        htmlspecialchars($role->getId()), // ID
                        htmlspecialchars($role->getTitre()), // Titre
                        htmlspecialchars($role->getDescription()), // Description
                        TableHelper::renderStatut($role->getIsActive() ? 'actif' : 'inactif', $role->getIsActive() ? 'green' : 'red'), // Statut
                        ActionHelper::generateActions([ // Actions
                            ActionHelper::bntIcon('bi bi-eye', "details.php?id=" . $role->getId(), 'btn-outline-info'),
                            ActionHelper::bntIcon('bi bi-pencil', "edit.php?id=" . $role->getId(), 'btn-outline-warning'),
                            ActionHelper::bntIcon('bi bi-trash', "delete.php?id=" . $role->getId(), 'btn-outline-danger'),
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