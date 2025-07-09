<?php
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/dropdown_helper.php';
require_once __DIR__ . '/../../app/helpers/pagination_helper.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/repositories/transaction_repository.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

AdminController::requirelogin();
$activeMenu = "transactions";
$baseUrl = 'list.php';
AdminController::checkAndRedirectPermission($activeMenu);
$transactionRepository = new PaiementRepository($db);

$currentPage = isset($_GET['page']) ? intval(htmlspecialchars($_GET['page'])) : 1;
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$orderBy = isset($_GET['orderBy']) ? htmlspecialchars($_GET['orderBy']) : 'date_transaction';
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
    $resultRequete = $transactionRepository->search(
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
        'Transactions',
        []
    ); ?>

    <!-- Card de recherche et filtres -->
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

        // Affichage de la table
        echo TableHelper::table(
            TableHelper::actionsBar(
                $resultRequete['total'],
                count($data),
                DropdownHelper::render(
                    'Plus de filtres',
                    'bi bi-funnel',
                    [
                        ['label' => 'Date', 'url' => $baseUrl . '?orderBy=date_transaction'],
                        ['label' => 'Montant', 'url' => $baseUrl . '?orderBy=montant'],
                        ['label' => 'Méthode de paiement', 'url' => $baseUrl . '?orderBy=methode_paiement'],
                        ['label' => 'Statut', 'url' => $baseUrl . '?orderBy=statut'],
                    ],
                    $orderBy
                )
            ),
            TableHelper::column(
                ['ID', 'Date', 'Utilisateur', 'Montant', 'Méthode', 'Statut', 'Actions'],
                true,
                true
            ),
            TableHelper::body(
                array_map(function ($transaction) {
                    return [
                        htmlspecialchars($transaction->getId()), // ID
                        htmlspecialchars($transaction->getDateTransaction()), // Date
                        htmlspecialchars($transaction->getCitoyenId()), // Utilisateur (à remplacer par le nom si disponible)
                        htmlspecialchars($transaction->getMontantFormatte()), // Montant
                        htmlspecialchars($transaction->getMethodePaiement()), // Méthode
                        TableHelper::renderStatut($transaction->getStatut(), $transaction->getStatut() === 'paye' ? 'green' : 'red'), // Statut
                        ActionHelper::generateActions([ // Actions
                            ActionHelper::bntIcon('bi bi-eye', "details.php?id=" . $transaction->getId(), 'btn-outline-info'),
                            ActionHelper::bntIcon('bi bi-trash', "delete.php?id=" . $transaction->getId(), 'btn-outline-danger'),
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