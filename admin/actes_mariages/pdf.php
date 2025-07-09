<?php 
require_once __DIR__ . '/../../app/config/constants.php';
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
AdminController::checkAndRedirectPermission($activeMenu);
$acteMariageRepository = new ActeMariageRepository($db);
$acteMariageModel = new ActeMariageModel();

$errors = [];
$success = false;
$exceptionMessage = null;

$id = intval($_GET['id']);
$acteMariageModel = $acteMariageRepository->findById($id);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Extrait d'Acte de Mariage</title>
   <link href="/etatcivil/assets/img/favicon.png" rel="icon">
    <!-- Bootstrap CSS -->
    <link href="/etatcivil/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/etatcivil/assets/css/admin.css" rel="stylesheet">
     <link href="/etatcivil/assets/css/bootstrap-icons.css" rel="stylesheet">    
   
    <link href="/etatcivil/assets/css/fontawesome.all.css" rel="stylesheet">
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
            max-width: 800px;
            margin: auto;
        }

        h2, h4 {
            text-align: center;
            color: #343a40;
        }

        .label {
            font-weight: bold;
        }

        .value {
            text-decoration: underline;
        }

        @media print {
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
                <div class="label">Du registre des actes de naissance de l'Etat Civil</div>
                <div>------</div>
                 <p><span class="label">Date du mariage :</span> <?= htmlspecialchars($acteMariageModel->getDateMariageLettre()) ?></p>
            <p><span class="label">Lieu du mariage :</span> <?= htmlspecialchars($acteMariageModel->getLieuMariage()) ?></p>
            </div>
        </div>

        <div class="row">
            <div class="col-5">
                <p class="text-center">
                    <span>N°</span>
                    <span class="label"><?= htmlspecialchars($acteMariageModel->getNumeroRegistre()) ?></span>
                    <span>DU</span>
                    <span class="label"><?= date('d/m/Y', strtotime($acteMariageModel->getDateMariage())) ?> DU REGISTRE</span>
                </p>

                <h5 class="text-center">MARIAGE</h5>
                <h3 class="text-center text-uppercase">De <?= htmlspecialchars($acteMariageModel->getNomPrenomsEpoux()) ?>./</h3>
                <h4 class="text-center">ET <?= htmlspecialchars($acteMariageModel->getNomPrenomsEpouse()) ?>./</h4>
                 <div><span class="label">Temoin 1 :</span> <?= htmlspecialchars($acteMariageModel->getTemoinHomme()) ?></div>
                <div><span class="label">Temoin 2 :</span> <?= htmlspecialchars($acteMariageModel->getTemoinFemme()) ?></div>
            </div>
            <div class="col-7">

               <div class="mb-4">
            <h4>ÉPOUX</h4>
            <div><span class="label">Nom Prénoms :</span> <?= htmlspecialchars($acteMariageModel->getNomPrenomsEpoux()) ?>./</div>
            <div><span class="label">Date de naissance :</span> <?= htmlspecialchars($acteMariageModel->getDateNaissanceEpoux()) ?>./</div>
            <div><span class="label">Profession :</span> <?= htmlspecialchars($acteMariageModel->getProfessionEpoux()) ?>./</div>
            <div><span class="label">Nom du père :</span> <?= htmlspecialchars($acteMariageModel->getNomPereEpoux()) ?>./</div>
            <div><span class="label">Nom de la mère :</span> <?= htmlspecialchars($acteMariageModel->getNomMereEpoux()) ?>./</div>
        </div>

      
        <div class="mb-4">
            <h4>ÉPOUSE</h4>
            <div><span class="label">Nom Prénoms :</span> <?= htmlspecialchars($acteMariageModel->getNomPrenomsEpoux()) ?></div>
            <div><span class="label">Date de naissance :</span> <?= htmlspecialchars($acteMariageModel->getDateNaissanceEpouse()) ?></div>
            <div><span class="label">Profession :</span> <?= htmlspecialchars($acteMariageModel->getProfessionEpouse()) ?></div>
            <div><span class="label">Nom du père :</span> <?= htmlspecialchars($acteMariageModel->getNomPereEpouse()) ?></div>
            <div><span class="label">Nom de la mère :</span> <?= htmlspecialchars($acteMariageModel->getNomMereEpouse()) ?></div>
        </div>

            </div>
        </div>

        
       

       

        <!-- Mentions marginales -->
        <div class="mb-4">
            <h5>Mentions marginales</h5>
            <hr>
            <?php if ($acteMariageModel->getMentionDivorce()) : ?>
                <p><span class="label">Divorce :</span> <?= htmlspecialchars($acteMariageModel->getMentionDivorce()) ?></p>
            <?php else : ?>
                <p><span class="label">Divorce :</span> Néant</p>
            <?php endif; ?>
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
        <div class="text-center no-print mt-4">
            <button onclick="window.print()" class="btn btn-primary">Imprimer</button>
        </div>
    </div>
</body>

</html>
