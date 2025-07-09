<?php

require_once 'app/config/database.php';
require_once 'app/repositories/demande_repository.php';
require_once 'app/repositories/paiement_repository.php';
require_once 'app/repositories/type_acte_repository.php';
require_once 'app/controllers/citoyen_controller.php';

CitoyenController::requirelogin();


// Initialisation des repositories
$demandeRepository = new DemandeRepository($db);
$paiementRepository = new PaiementRepository($db);
$success = false;
$successMessage = '';
$errorMessage = '';

// Récupération de la demande à payer

if (!isset($_GET['reference'])) {
    $errorMessage = "Demande d'actes non trouvée";
} else {
    $demandeRef = htmlspecialchars($_GET['reference']);
    try {
        $demande = $demandeRepository->findByRef($demandeRef);
        if (isset($demande)) {
            if ($demande->getCitoyenId() != $_SESSION['citoyen_id']) {
                $errorMessage = 'Demande reférence ' . $demandeRef . ' accès refusé' . $_SESSION['citoyen_id'] . '=' . $demande->getCitoyenId();
            } else if ($demande->getStatut() === 'paye') {
                $successMessage = 'Cette demande a déjà été payée.';
            }
        } else {
            $errorMessage = 'Demande reférence ' . $demandeRef . ' non trouvée';
        }
    } catch (Exception $e) {
        $errorMessage = 'Erreur : ' . $e->getMessage();
    }
}



// Traitement du paiement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errorMessage)) {
    try {
        // Validation des données
        $montant = floatval($_POST['montant']);
        $methodePaiement = htmlspecialchars($_POST['methode_paiement']);
        $paiementformValide = true;
        if (!$montant || !$methodePaiement) {
            $paiementformValide = false;
            $errorMessage = 'Tous les champs sont obligatoires';
        }
        if (abs($montant - $demande->getTotalFrais()) > 0.01) {
            $paiementformValide = false;
            $errorMessage = 'Le montant payé ne correspond pas au montant demandé';
        }


        if ($paiementformValide) {
            $paiement = new PaiementModel(
                null,
                $demande->getId(),
                $demande->getCitoyenId(),
                $montant,
                $methodePaiement,
                '',
                'complete',
            );
            $transactionId = $paiementRepository->create($paiement);

            $demandeRepository->updateStatus($demande->getId(), 'en_traitement');
            $successMessage = 'Paiement effectué avec succès. Référence: PAY-' . $transactionId->getReference();
            $success = true;
        }
    } catch (Exception $e) {
        $errorMessage = 'Erreur lors du paiement : ' . $e->getMessage();
    }
}
?>

<?php require_once 'citoyen/includes/header.php'; ?>

<div class="m-5">
     <h4 class="m-auto text-body">Paiement d'actes</h4>
    <div class="row">
        <div class="col-md-4">
            
                <div >
                    <div class="m-auto card shadow-none" >
                        <div class=" card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-list me-2"></i>Paiement Informations</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Référence</th>
                                        <td><?= htmlspecialchars($demande->getReference()) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Type d'acte</th>
                                        <td><?= htmlspecialchars($demande->getActeLibelle()) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Montant à payer</th>
                                        <td class="fw-bold">
                                            <?= number_format($demande->getTotalFrais(), 2, ',', ' ') ?> FCFA
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Statut</th>
                                        <td>
                                            <span class="badge bg-<?= $demande->getStatut() === 'en_attente' ? 'warning' : 'success' ?>">
                                                <?= htmlspecialchars($demande->getStatutLibelle()) ?>
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <ol class="mt-4 list-group list-group-numbered list-group-flush card shadow-none">
                         <div class=" card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-list me-2"></i>Paiement Informations</h5>
                        </div>
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
                            <i class="bi bi-house-door text-danger me-2"></i>Récupérez le document physique à la mairie ou optez pour une livraison.
                        </li>
                    </ol>
                </div>
           
        </div>


        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0"><i class="bi bi-credit-card me-2"></i>Paiement des frais</h2>
                </div>

                <div class="card-body">

                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                            <div class="text-center">
                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                       <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="/etatcivil/citoyen/demandes.php" class="btn btn-success">Voir mes démandes</a>
                        </div>
                    </div>

                    <?php else: ?>

                        <form method="POST" id="paymentForm">
                            <h4 class="mb-3">Méthode de paiement</h4>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="methode_paiement" id="mobileMoney" value="mobile_money" checked>
                                    <label class="form-check-label" for="mobileMoney">
                                        <i class="bi bi-phone me-2"></i>Mobile Money
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="methode_paiement" id="carteCredit" value="carte_credit">
                                    <label class="form-check-label" for="carteCredit">
                                        <i class="bi bi-credit-card me-2"></i>Carte de crédit
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="methode_paiement" id="especes" value="especes">
                                    <label class="form-check-label" for="especes">
                                        <i class="bi bi-cash-coin me-2"></i>Espèces (en mairie)
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="montant" class="form-label fw-bold">Montant</label>
                                <input type="number" class="form-control" id="montant" name="montant"
                                    value="<?= htmlspecialchars($demande->getTotalFrais()) ?>"
                                    step="0.01" min="<?= $demande->getTotalFrais() ?>"
                                    max="<?= $demande->getTotalFrais() ?>" readonly>
                            </div>

                            <div id="mobileMoneyFields" class="payment-method-fields">
                                <div class="mb-3">
                                    <label for="mobileNumber" class="form-label">Numéro Mobile Money</label>
                                    <input type="tel" class="form-control" id="mobileNumber" placeholder="Ex: 07 12 34 56 78">
                                </div>
                            </div>

                            <div id="carteCreditFields" class="payment-method-fields" style="display: none;">
                                <div class="mb-3">
                                    <label for="mobileNumber" class="form-label">Numéro Carte de bancaire</label>
                                    <input type="tel" class="form-control" id="mobileNumber" placeholder="Ex: 00309 07 12 34 56 78">
                                </div>
                            </div>



                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>Payer maintenant
                                </button>
                                <a href="/etatcivil/citoyen/demandes.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Retour
                                </a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Afficher les champs spécifiques à la méthode de paiement
    document.querySelectorAll('input[name="methode_paiement"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.payment-method-fields').forEach(field => {
                field.style.display = 'none';
            });

            if (this.value === 'mobile_money') {
                document.getElementById('mobileMoneyFields').style.display = 'block';
            } else if (this.value === 'carte_credit') {
                document.getElementById('carteCreditFields').style.display = 'block';
            }
        });
    });

    // Validation du formulaire
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        const method = document.querySelector('input[name="methode_paiement"]:checked').value;

        if (method === 'mobile_money' && !document.getElementById('mobileNumber').value) {
            e.preventDefault();
            alert('Veuillez entrer votre numéro Mobile Money');
        }
    });
</script>

<?php require_once 'citoyen/includes/footer.php'; ?>