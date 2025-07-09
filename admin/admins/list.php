<?php
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../app/helpers/dropdown_helper.php';
require_once __DIR__ . '/../../app/helpers/pagination_helper.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/repositories/admin_repository.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

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
$stat = [];
$resultRequete = [
    'data' => [],
    'total' => 0,
    'perPage' => 20,
    'totalPages' => 0
];



try {
    $stat = $adminRepository->statistiques();
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

 <div class="row mb-4 g-3">
     <?php 
        // Configuration des cartes stats
        $statsCards = [
            [
                'title' => 'Administrateurs Total',
                'value' => $stat['total'] ?? 0,
                'daily' => $stat['du_jour']['total'] ?? 0,
                'icon' => 'fas fa-users',
                'bg' => 'primary'
            ],
            [
                'title' => 'Administrateurs Actifs',
                'value' => $stat['actif'] ?? 0,
                'daily' => $stat['du_jour']['actif'] ?? 0,
                'icon' => 'fas fa-user-check',
                'bg' => 'success'
            ],
            [
                'title' => 'Administrateurs Inactifs',
                'value' => $stat['inactif'] ?? 0,
                'daily' => $stat['du_jour']['inactif'] ?? 0,
                'icon' => 'fas fa-user-times',
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
</div>
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