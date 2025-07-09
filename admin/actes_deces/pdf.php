<?php
require_once __DIR__ . '/../../app/config/constants.php';
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/dropdown_helper.php';
require_once __DIR__ . '/../../app/helpers/pagination_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';;
require_once __DIR__ . '/../../app/controllers/admin_controller.php';
require_once __DIR__ . '/../../app/repositories/acte_deces_repository.php';


AdminController::requirelogin();
$activeMenu = "deces";
AdminController::checkAndRedirectPermission($activeMenu);
$acteDecesRepository = new ActeDecesRepository($db);
$actedecesmodel = new ActeDecesModel();

$errors = [];
$success = false;
$exceptionMessage = null;

$id = intval($_GET['id']);
$actedecesmodel = $acteDecesRepository->findById($id);



?>

<!DOCTYPE html>
<html lang="fr">

<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extrait d'Acte de Naissance</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: "Times New Roman", serif;
            background-color: #f8f9fa;
            padding-top: 20px;
        }

        .container {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            max-width: 800px;
        }

        h2,
        h4 {
            color: #343a40;
            text-align: center;
            margin-bottom: 20px;
        }

        .row {
            margin-bottom: 15px;
        }

        .label {
            font-weight: bold;
            color: #495057;
        }

        .value {
            color: #212529;
            text-decoration: underline;
        }

        .mentions {
            margin-top: 30px;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }

        .official-info {
            margin-top: 30px;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .header-section {
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        @media print {
            body {
                background: none;
                font-size: 12pt;
            }

            .container {
                box-shadow: none;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- En-tête -->
        <div class="row header-section">
            <div class="col-md-6 text-center">
                <div class="label">COMMUNE DE <?php echo strtoupper(COMMUNE); ?></div>
                <img src="/etatcivil/assets/img/favicon.png" alt="Logo" width="60" height="70" class="mt-2">
                <h5>ETAT CIVIL</h5>
                <div>------</div>
                <div>Centre d'état civil <?php echo COMMUNE_NAME; ?> 3</div>
            </div>

            <div class="col-md-6 text-center">
                <div class="label">REPUBLIQUE DE COTE D'IVOIRE</div>
                <div>------</div>
                <h3 class="label">EXTRAIT</h3>
                <div class="label">Du registre des actes de décès de l'Etat Civil</div>
                <div class="label">Pour l'année <?= htmlspecialchars($actedecesmodel->getAnneeRegistre()) ?></div>
            </div>
        </div>




        <div class="row">
            <div class="col-5">
                <p class="text-center">
                    <span>N°</span>
                    <span class="label"><?= htmlspecialchars($actedecesmodel->getNumeroRegistre()) ?></span>
                    <span>DU</span>
                    <span class="label"><?= date('d/m/Y', strtotime($actedecesmodel->getDateDeces())) ?> DU REGISTRE</span>
                </p>

                <h5 class="text-center">NAISSANCE DE</h5>
                <h3 class="text-center text-uppercase"><?= htmlspecialchars($actedecesmodel->getNom()) ?></h3>
                <h4 class="text-center"><?= htmlspecialchars($actedecesmodel->getPrenoms()) ?>./</h4>
            </div>
            <div class="col-7">

                <h5 class="label">Le <?= $actedecesmodel->getDateDeces() ?>./.</h5>
                <h5 class="label">est décédé <?= htmlspecialchars($actedecesmodel->getNom()) ?> <?= htmlspecialchars($actedecesmodel->getPrenoms()) ?>./.</h5>
                <h5 class="label">à  <?= $actedecesmodel->getLieuDeces() ?>./.</h5>
            </div>
        </div> 


      

        <!-- Certification -->
        <div class="mt-4">
            <p><span class="label">Certifié le présent extrait conforme aux indications portées au registre</span></p>
        </div>

        <!-- Pied de page -->
        <div class="official-info mt-5">
            <div class="row">
                <div class="col-md-6 offset-md-6 text-center">
                    <p>Délivré à <?php echo strtoupper(COMMUNE); ?>, le <?= date('d/m/Y') ?></p>
                    <p>L'Officier de l'ETAT CIVIL,</p>
                     <img src="/etatcivil/assets/img/signature.png" alt="Logo" width="100" height="100" class="mt-2">
                </div>
            </div>
        </div>

        <!-- Bouton d'impression -->
        <div class="row mt-4 no-print">
            <div class="col-12 text-center">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Imprimer l'extrait
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>