<?php
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/dropdown_helper.php';
require_once __DIR__ . '/../../app/helpers/pagination_helper.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/repositories/acte_naissance_repository.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';

AdminController::requirelogin(); 
$activeMenu = "naissances";
$baseUrl = 'list.php';
AdminController::checkAndRedirectPermission($activeMenu);
$acteNaissanceRepository = new ActeNaissanceRepository($db);

$currentPage = isset($_GET['page']) ? intval(htmlspecialchars($_GET['page'])) : 1;
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$orderBy = isset($_GET['orderBy']) ? htmlspecialchars($_GET['orderBy']) : 'nom';
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
    $resultRequete = $acteNaissanceRepository->search(
        $search,
        $currentPage,
        20,
        $orderBy
    );
    $data = $resultRequete['data'];
} catch (Exception $e) {
    $exceptionMessage = $e->getMessage();
}
 if(true){

 }

?>

<?php include '../partials/header.php'; ?>
    <main class="main-content">
        <?= ContentRendu::header(
            'Actes de Naissance',
            [
                ActionHelper::bntIconLabel('Ajouter', 'bi bi-plus', "edit.php", 'btn-success'),
            ]
    )   ; ?>

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
                        ['label' => 'Nom', 'url' => $baseUrl . '?orderBy=nom'],
                        ['label' => 'Date de Naissance', 'url' => $baseUrl . '?orderBy=date_naissance'],
                        ['label' => 'Lieu de Naissance', 'url' => $baseUrl . '?orderBy=lieu_naissance'],
                        ['label' => 'Date de Création', 'url' => $baseUrl . '?orderBy=created_at'],
                    ],
                    $orderBy
                )
            ),
            TableHelper::column(
                ['ID', 'Numéro', 'Nom et Prénoms', 'Date de Naissance', 'Lieu de Naissance', 'Actions'],
                true,
                true
            ),
            TableHelper::body(
                array_map(function ($acte) {
                    return [
                        htmlspecialchars($acte->getId()), // ID
                        htmlspecialchars($acte->getNumeroRegistre()), // Numéro
                        htmlspecialchars($acte->getNomComplet()), // Nom et Prénoms
                        htmlspecialchars($acte->getDateNaissance()), // Date de Naissance
                        htmlspecialchars($acte->getLieuNaissance()), // Lieu de Naissance
                          ActionHelper::generateActions([ // Actions
                            ActionHelper::bntIcon('bi bi-file-pdf', "pdf.php?id=" . $acte->getId(), 'btn-outline-info'),
                            ActionHelper::bntIcon('bi bi-info', "details.php?id=" . $acte->getId(), 'btn-outline-info'),
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

  <script> function printExternal(url){
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = url;
    document.body.appendChild(iframe);
    iframe.onload = function(){
        setTimeout(()=>{
            iframe.contentWindow.print();
            document.body.removeChild(iframe);
        }, 500);
    }
 }
    </script>
<?php include '../partials/footer.php'; ?>