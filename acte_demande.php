<?php
require_once __DIR__ . '/app/config/database.php';
require_once __DIR__ . '/app/repositories/demande_repository.php';
require_once __DIR__ . '/app/repositories/type_acte_repository.php';
require_once __DIR__ . '/app/controllers/citoyen_controller.php';
require_once __DIR__ . '/app/helpers/file_helper.php';

CitoyenController::requirelogin();
$repository = new DemandeRepository($db);
$typeActeRepository = new TypeActeRepository($db);

$successMessage = '';
$errorMessage = '';
$success = false;

// Récupération des types d'actes
try {
    $resultRequteTypeActes = $typeActeRepository->search();
    $typesActes = $resultRequteTypeActes['data'] ?? [];
} catch (Exception $e) {
    $errorMessage = 'Erreur lors de la récupération des types d\'actes : ' . $e->getMessage();
    $typesActes = [];
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validation des données
        $typeActeId = filter_input(INPUT_POST, 'type_acteId', FILTER_VALIDATE_INT);
        $numeroActes = htmlspecialchars($_POST["numero_acte"]);

        if (!$typeActeId || empty($numeroActes)) {
            throw new Exception('Veuillez remplir tous les champs obligatoires');
        }

        // Récupération des détails du type d'acte
        $typeActeDetails = $typeActeRepository->findById($typeActeId);
        if (!$typeActeDetails) {
            throw new Exception('Type d\'acte invalide');
        }

        $fraisUnitaire = $typeActeDetails->getFrais();
        $nombreActes = filter_input(INPUT_POST, 'nombre_acte', FILTER_VALIDATE_INT);

        // Gestion du fichier uploadé
        $fichierPath = FileHelper::upload();

        // Options de livraison
        $methodeLivraison = $_POST['mode_recuperation'] ?? 'retrait_guichet';
        $fraisLivraison = 0;

        // Calcul des frais de livraison si nécessaire
        if ($methodeLivraison === 'livraison_domicile') {
            $fraisLivraison = 2000; // Frais fixes de livraison à domicile
        } elseif ($methodeLivraison === 'livraison_point_relais') {
            $fraisLivraison = 1000; // Frais fixes pour point relais
        }

        $totalFrais = ($fraisUnitaire * $nombreActes) + $fraisLivraison;

        $demande = new DemandeModel(
            id: null,
            reference: '',
            citoyenId: intval($_SESSION['citoyen_id']),
            acteLibelle: $typeActeDetails->getLibelle(),
            citoyenNom: $_SESSION['citoyen_name'] ?? 'Citoyen',
            numeroActes: $numeroActes,
            typeActesId: $typeActeId,
            dateDemande: date('Y-m-d H:i:s'),
            statut: 'en_attente',
            fichierPath: $fichierPath,
            fraisUnitaire: $fraisUnitaire,
            fraisLivraison: $fraisLivraison,
            totalFrais: $totalFrais,
            nombreActes: $nombreActes,
            methodeLivraison: $methodeLivraison,
            adresseLivraison: ($methodeLivraison !== 'retrait_guichet') ? $_SESSION['citoyen_adresse'] : null
        );

        // Génération de la référence et sauvegarde
        $demande->generateReference();
        $repository->create($demande);
        $success = true;
        $successMessage = 'Votre demande a été enregistrée avec succès. Référence: ' . $demande->getReference();
    } catch (Exception $e) {
        $errorMessage = 'Erreur : ' . $e->getMessage();
    }
}
?>

<?php require_once __DIR__ . '/citoyen/includes/header.php'; ?>

<div class="my-5">
    <h4 class="m-4 text-info">Effectuer une demande d'actes</h4>

    <form action="" method="POST" enctype="multipart/form-data" class="bg-light p-4">
        <div class="row">
            <!-- Colonne gauche : Étapes de la demande -->
            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Étapes de la demande</h5>
                    </div>
                    <div class="card-body">
                        <ol class="list-group list-group-numbered list-group-flush">
                            <li class="list-group-item">
                                <i class="bi bi-pencil-square text-primary me-2"></i>Effectuez une demande en remplissant le formulaire.
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-credit-card text-success me-2"></i>Passez au paiement des frais associés.
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-hourglass-split text-warning me-2"></i>Attendez le traitement de la demande dans le délai indiqué.
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-bell text-info me-2"></i>Recevez une notification de la disponibilité du document.
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-cloud-download text-secondary me-2"></i>Téléchargez le document dans votre espace citoyen.
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-house-door text-danger me-2"></i>Récupérez le document selon votre choix.
                            </li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Colonne droite : Formulaire -->
            <div class="col-md-6">
                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="text-center">
                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                        <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="paiement.php?reference=<?= $demande->getReference() ?>" class="btn btn-success">Passer au paiement</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-ui-checks-grid me-2"></i>Informations à renseigner</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="mt-4">
                                <div class="mb-3">
                                    <label for="type_acteId" class="form-label fw-bold">Type d'acte <span class="text-danger">*</span></label>
                                    <select class="form-select" id="type_acteId" name="type_acteId" required onchange="updateTypeActeInfo()">
                                        <option value="" disabled selected>-- Sélectionnez un type d'acte --</option>
                                        <?php foreach ($typesActes as $typeActe): ?>
                                            <option value="<?= htmlspecialchars($typeActe->getId()) ?>"
                                                data-frais="<?= htmlspecialchars($typeActe->getFrais()) ?>"
                                                data-duree="<?= htmlspecialchars($typeActe->getDelaiTraitement()) ?>"
                                                data-description="<?= htmlspecialchars($typeActe->getDescription()) ?>">
                                                <?= htmlspecialchars($typeActe->getLibelle()) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div id="typeActeInfo" class="alert alert-info mb-3" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <p class="mb-1"><strong>Frais:</strong></p>
                                            <p id="typeActeFrais" class="fw-bold"></p>
                                        </div>
                                        <div class="col-md-5">
                                            <p class="mb-1"><strong>Durée:</strong></p>
                                            <p id="typeActeDuree"></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <label for="numero_acte" class="form-label fw-bold">Numéro de l'acte <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="numero_acte" name="numero_acte" required
                                            placeholder="Ex: 2023-00123" pattern="[A-Za-z0-9-]+" title="Caractères alphanumériques et tirets uniquement">
                                    </div>

                                    <div class="col-md-4">
                                        <label for="nombre_acte" class="form-label fw-bold">Nombre d'acte <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="nombre_acte" name="nombre_acte" required min="1" value="1"
                                            placeholder="Ex: 1">
                                    </div>
                                </div>

                                <div class="mb-3"> 
                                    <label for="fichier" class="form-label fw-bold">Pièce jointe (facultatif)</label>
                                    <input type="file" class="form-control" id="fichier" name="fichier" accept=".pdf,.jpg,.jpeg,.png">
                                    <div class="form-text">Formats acceptés: PDF, JPG, PNG (max 2MB)</div>
                                </div>

                                <!-- Section Mode de récupération -->
                                <div class="mb-3 border-top pt-3">
                                    <h5 class="fw-bold mb-3">Mode de récupération</h5>

                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="mode_recuperation" id="retrait_guichet" value="retrait_guichet" checked>
                                        <label class="form-check-label" for="retrait_guichet">
                                            <i class="bi bi-building me-2"></i>Retrait au guichet
                                        </label>
                                        <div class="form-text text-muted ms-4">
                                            Présentez-vous avec votre pièce d'identité au service de l'état civil
                                        </div>
                                    </div>

                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="mode_recuperation" id="livraison_domicile" value="livraison_domicile">
                                        <label class="form-check-label" for="livraison_domicile">
                                            <i class="bi bi-truck me-2"></i>Livraison à domicile (+2 000 FCFA)
                                        </label>
                                        <div class="form-text text-muted ms-4">
                                            Livraison à l'adresse enregistrée dans votre profil
                                        </div>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="mode_recuperation" id="livraison_point_relais" value="livraison_point_relais">
                                        <label class="form-check-label" for="livraison_point_relais">
                                            <i class="bi bi-shop me-2"></i>Point relais (+1 000 FCFA)
                                        </label>
                                        <div class="form-text text-muted ms-4">
                                            Retrait dans un point relais partenaire
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-send-check me-2"></i>Soumettre la demande
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<script>
    function updateTypeActeInfo() {
        const select = document.getElementById('type_acteId');
        const selectedOption = select.options[select.selectedIndex];
        const infoDiv = document.getElementById('typeActeInfo');

        if (selectedOption && selectedOption.value) {
            document.getElementById('typeActeFrais').textContent = new Intl.NumberFormat('fr-FR').format(selectedOption.getAttribute('data-frais')) + ' FCFA';
            document.getElementById('typeActeDuree').textContent = selectedOption.getAttribute('data-duree') + ' jours';
            infoDiv.style.display = 'block';
        } else {
            infoDiv.style.display = 'none';
        }
    }
</script>

<?php require_once __DIR__ . '/citoyen/includes/footer.php'; ?>