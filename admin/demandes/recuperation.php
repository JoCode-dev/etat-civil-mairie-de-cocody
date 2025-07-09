<?php
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/repositories/demande_repository.php';
require_once __DIR__ . '/../../app/repositories/paiement_repository.php';
require_once __DIR__ . '/../../app/repositories/demande_statut_historique_repository.php';
require_once __DIR__ . '/../../app/repositories/citoyen_repository.php';
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/pagination_helper.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';
require_once 'statut_historique_helper.php';
require_once 'paiement_info_helper.php';
require_once 'demande_helper.php';

AdminController::requirelogin();
$activeMenu = "recuperation";
AdminController::checkAndRedirectPermission($activeMenu);
$demandeRepository = new DemandeRepository($db);
$demandeStatutHistoriqueRepository = new DemandeStatutHistoriqueRepository($db);
$citoyenRepository = new CitoyenRepository($db);

$successMessage = '';
$errorMessage = '';
$demande = null;

// Traitement de la recherche
try {
    if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
        $reference = trim($_GET['search']);
        $demande = $demandeRepository->findByRef($reference, true);

        if ($demande === null) {
            $errorMessage = 'Aucune demande trouvée avec cette référence';
        } elseif ($demande->getMethodeLivraison() !== 'retrait_guichet') {
            $errorMessage = 'Cette demande n\'est pas pour un retrait à la mairie';
            $demande = null;
        }
    }
} catch (Exception $e) {
    $errorMessage = 'Erreur lors de la recherche : ' . $e->getMessage();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($demande)) {
            throw new Exception('Demande non trouvée');
        }

        if (isset($_POST['confirmer_retrait'])) {
            // Validation de l'identité
            $pieceIdentite = $_POST['piece_identite'] ?? '';
            $numeroPiece = $_POST['numero_piece'] ?? '';
            
            if (empty($pieceIdentite) || empty($numeroPiece)) {
                throw new Exception('Veuillez renseigner les informations de la pièce d\'identité');
            }

            // Vérification de l'identité du citoyen
            $citoyen = $citoyenRepository->findById($demande->getCitoyenId());
            if (!$citoyen) {
                throw new Exception('Citoyen non trouvé');
            }

            // Mise à jour du statut
            $demandeRepository->updateStatus($demande->getId(), 'recupere');
            $demandeStatutHistoriqueRepository->create(
                $demande->getId(),
                $_SESSION['admin_id'],
                'recupere',
                "Retrait confirmé. Pièce d'identité: $pieceIdentite, N°: $numeroPiece"
            );

            $successMessage = 'Le retrait a été confirmé avec succès';
        }
        
        // Recharger les données à jour
        $demande = $demandeRepository->findById($demande->getId(), true);
    } catch (Exception $e) {
        $errorMessage = 'Erreur lors de la confirmation : ' . $e->getMessage();
    }
}
?>

<?php include '../partials/header.php'; ?>

<main class="main-content position-relative">
    <?= ContentRendu::header(
        "Retrait des documents à la mairie",
        [
            ActionHelper::bntIconLabel(
                'Nouvelle recherche',
                'bi bi-search',
                "recuperation.php",
                'btn-outline-primary'
            ),
        ]
    ); ?>
    
    <div class="row mb-4">
        <div class="col-md-6 mx-auto">
            <form method="GET" class="input-group">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Référence de la demande..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                    required>
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search"></i> Rechercher
                </button>
            </form>
        </div>
    </div>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger mx-3"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <?php if ($successMessage): ?>
        <div class="alert alert-success mx-3"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>

    <?php if ($demande): ?>
        <div class="row g-3 mx-1">
            <!-- Colonne Informations générales -->
            <div class="col-lg-6">
                <?php echo DemandeHelper::showHtml($demande->getId()) ?>
                <?php echo PaiementInfoHelper::showHtml($demande->getId()) ?>
                <?php echo StatutHistoriqueHelper::showHtml($demande->getId()) ?>
            </div>

            <!-- Colonne Traitement -->
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-file-earmark-check me-2"></i>Procédure de retrait
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($successMessage): ?>
                            <div class="alert alert-success text-center py-4">
                                <i class="bi bi-check-circle-fill display-4 text-success mb-3"></i>
                                <h3 class="h4">Retrait confirmé avec succès</h3>
                                <p>Document remis à <?= htmlspecialchars($demande->getCitoyenNom()) ?></p>
                                
                                <div class="mt-4">
                                    <button class="btn btn-outline-primary" onclick="window.print()">
                                        <i class="bi bi-printer me-2"></i>Imprimer la confirmation
                                    </button>
                                    <a href="recuperation.php" class="btn btn-primary ms-2">
                                        <i class="bi bi-plus-circle me-2"></i>Nouvelle recherche
                                    </a>
                                </div>
                            </div>
                        <?php elseif ($demande->getStatut() === 'pret'): ?>
                            <div class="alert alert-info">
                                <h5 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Instructions</h5>
                                <p>Veuillez vérifier l'identité du citoyen avant de confirmer le retrait.</p>
                                <hr>
                                <p class="mb-0">Le citoyen doit présenter une pièce d'identité valide.</p>
                            </div>
                            
                            <form method="POST">
                                <input type="hidden" name="confirmer_retrait" value="1">
                                
                                <h5 class="fw-bold mt-4 mb-3">Vérification d'identité</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Type de pièce d'identité <span class="text-danger">*</span></label>
                                    <select name="piece_identite" class="form-select" required>
                                        <option value="">-- Sélectionnez --</option>
                                        <option value="CNI">Carte Nationale d'Identité</option>
                                        <option value="Passeport">Passeport</option>
                                        <option value="Permis">Permis de conduire</option>
                                        <option value="Autre">Autre document</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Numéro de la pièce <span class="text-danger">*</span></label>
                                    <input type="text" name="numero_piece" class="form-control" required
                                           placeholder="Numéro complet de la pièce">
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="verifie_identite" required>
                                    <label class="form-check-label" for="verifie_identite">Je certifie avoir vérifié l'identité du citoyen</label>
                                </div>
                                
                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="bi bi-check-circle me-2"></i>Confirmer le retrait
                                    </button>
                                    <a href="recuperation.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Annuler
                                    </a>
                                </div>
                            </form>
                        <?php elseif ($demande->getStatut() === 'recupere'): ?>
                            <div class="alert alert-success text-center py-4">
                                <i class="bi bi-check-circle-fill display-4 text-success mb-3"></i>
                                <h3 class="h4">Document déjà récupéré</h3>
                                <p>Le document a été remis ></p>
                                
                                <div class="mt-4">
                                    <a href="recuperation.php" class="btn btn-primary">
                                        <i class="bi bi-search me-2"></i>Nouvelle recherche
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning text-center py-4">
                                <i class="bi bi-hourglass-split display-4 text-warning mb-3"></i>
                                <h3 class="h4">Document non disponible</h3>
                                <p>Statut actuel : <strong><?= htmlspecialchars($demande->getStatutLibelle()) ?></strong></p>
                                
                                <div class="mt-4">
                                    <a href="recuperation.php" class="btn btn-outline-primary">
                                        <i class="bi bi-arrow-left me-2"></i>Retour
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center py-4">
            <i class="bi bi-info-circle display-4 text-info mb-3"></i>
            <h3 class="h4">Recherche de demande</h3>
            <p>Veuillez entrer la référence d'une demande pour le retrait à la mairie</p>
        </div>
    <?php endif; ?>
</main>

<?php include '../partials/footer.php'; ?>