<?php
require_once __DIR__ . '/../../../app/Config/Database.php';
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../../app/Helpers/dropdown_helper.php';
require_once __DIR__ . '/../../../app/Helpers/pagination_helper.php';
require_once __DIR__ . '/../../../app/Helpers/alert_helper.php';
require_once __DIR__ . '/../../../app/Helpers/ActionHelper.php';
require_once __DIR__ . '/../../../app/Repositories/admin_repository.php';
require_once __DIR__ . '/../../../app/Controllers/AdminController.php';

AdminController::requirelogin();
$activeMenu = "admins"; 
$baseUrl = 'list.php'; 
AdminController::checkAndRedirectPermission($activeMenu);
$adminRepository = new AdminRepository($db);

$currentPage = isset($_GET['page']) ? intval(htmlspecialchars($_GET['page'])) : 1;
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$orderBy = isset($_GET['orderBy']) ? htmlspecialchars($_GET['orderBy']) : 'nom';
$errorMessage = null;
$exceptionMessagnnées = null;
$data = [];
$resultRequete = [
    'data' => [],
    'total' => 0,
    'perPage' => 20,
    'totalPages' => 0
];



try {
    $resultRequete = $adminRepository->search(
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
        'Administrateurs',
        [
    ActionHelper::bntIconLabel('Ajouter', 'bi bi-plus', "edit.php", 'btn-success'),
        ]
    ); ?>

     <!-- Card de recherche et filtres -->
        <div class="card mb-4">
            <div class="card-body">
               
        <?php echo ContentRendu::searchField()  ?>
            
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
            TableHelper::actionsBar($resultRequete['total'],count($data),
           DropdownHelper::render(
    'Plus de filtres',
    'bi bi-funnel',
    [
        ['label' => 'Nom Prénoms', 'url' => $baseUrl.'?orderBy=nom_prenoms'],
        ['label' => 'Date de création', 'url' => $baseUrl.'?orderBy=created_at'],
        ['label' => 'Role', 'url' => $baseUrl.'?orderBy=role_name'],
    ],
    $orderBy
),
        ),
            TableHelper::column(
                ['ID', 'Nom et prénoms',  'Role', 'Statut', 'Last Login',  'Actions'],
                true,true
            ),
            TableHelper::body( 
                array_map(function ($admin) {
                return [
                    htmlspecialchars($admin->getId()), // ID
                    htmlspecialchars($admin->getNomComplet()), // Nom et prénoms
                    htmlspecialchars($admin->getRoleName()), // Role
                    TableHelper::renderStatut($admin->isActive()?'actif':'inactif',$admin->isActive()?'green':'red'), // Statut
                    htmlspecialchars($admin->getLastLogin() ?? 'Jamais connecté'), // Last Login
                    ActionHelper::generateActions([ // Actions
                        ActionHelper::bntIcon( 'bi bi-eye', "details.php?id=" . $admin->getId(), 'btn-outline-info'),
                        ActionHelper::bntIcon( 'bi bi-pencil', "edit.php?id=" . $admin->getId(), 'btn-outline-warning'),
                        ActionHelper::bntIcon( 'bi bi-trash', "delete.php?id=" . $admin->getId(), 'btn-outline-danger'),
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