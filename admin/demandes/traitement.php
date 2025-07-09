<?php


require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/repositories/demande_repository.php';
require_once __DIR__ . '/../../app/repositories/paiement_repository.php';
require_once __DIR__ . '/../../app/repositories/demande_statut_historique_repository.php';
require_once __DIR__ . '/../../app/helpers/action_helper.php';
require_once __DIR__ . '/../../admin/table_helper.php';
require_once __DIR__ . '/../../admin/content_render.php';
require_once __DIR__ . '/../../app/helpers/pagination_helper.php';
require_once __DIR__ . '/../../app/helpers/alert_helper.php';
require_once __DIR__ . '/../../app/controllers/admin_controller.php';
require_once 'statut_historique_helper.php';
require_once 'paiement_info_helper.php';
require_once 'demande_helper.php';
require_once 'livraison_helper.php';

AdminController::requirelogin();
$activeMenu = "traitement";
AdminController::checkAndRedirectPermission($activeMenu);
$demandeRepository = new DemandeRepository($db);
$demandeStatutHistoriqueRepository = new DemandeStatutHistoriqueRepository($db);
$coursierRepository = new CoursierRepository($db);

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

        if (isset($_POST['update_statut'])) {
            // Mise à jour du statut
            $statut = $_POST['statut'] ?? '';
            $commentaire = $_POST['commentaire'] ?? '';

            // Validation du statut
            $statutsValides = ['en_attente', 'en_traitement', 'pret', 'recupere', 'annule', 'en_livraison', 'livre'];
            if (!in_array($statut, $statutsValides)) {
                throw new Exception('Statut invalide');
            }

            $demandeRepository->updateStatus($demande->getId(), $statut);
            $demandeStatutHistoriqueRepository->create(
                $demande->getId(),
                $_SESSION['admin_id'],
                $statut,
                $commentaire,
            );

            if ($statut === 'pret') {
                // Ici vous pourriez appeler un service de génération de document
            }

            $successMessage = 'Le statut a été mis à jour avec succès';
        } elseif (isset($_POST['preparer_livraison'])) {
            // Préparation de la livraison
            $coursierId = $_POST['coursier_id'] ?? null;
            $dateLivraison = $_POST['date_livraison'] ?? '';

            if (empty($coursierId) || empty($dateLivraison)) {
                throw new Exception('Veuillez sélectionner un coursier et une date de livraison');
            }

            $demandeRepository->preparerLivraison($demande->getId(), $coursierId, $dateLivraison);
            $successMessage = 'La livraison a été programmée avec succès';
        }

        // Recharger les données à jour
        $demande = $demandeRepository->findById($demande->getId(), true);
    } catch (Exception $e) {
        $errorMessage = 'Erreur lors de la mise à jour : ' . $e->getMessage();
    }
}
?>

<?php include '../partials/header.php'; ?>

<main class="main-content position-relative">
    <?= ContentRendu::header(
        "Traitement des demandes",
        [
            ActionHelper::bntIconLabel(
                'Nouvelle recherche',
                'bi bi-search',
                "traitement.php",
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
                            <i class="bi bi-gear me-2"></i>Traitement de la demande
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($successMessage): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                                <p class="mt-3">La demande a été mise à jour avec succès</p>
                                <a href="traitement.php" class="btn btn-primary mt-2">
                                    <i class="bi bi-arrow-left"></i> Retour
                                </a>
                            </div>
                        <?php elseif ($demande->getStatut() === 'en_traitement' || $demande->getStatut() === 'pret'): ?>
                            <?php if ($demande->getMethodeLivraison() !== 'retrait_guichet' && $demande->getStatut() === 'pret'): ?>
                                <!-- Formulaire de préparation de livraison -->
                                <form method="POST">
                                    <input type="hidden" name="preparer_livraison" value="1">
                                    
                                    <h5 class="fw-bold mb-3"><i class="bi bi-truck me-2"></i>Préparation de la livraison</h5>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Coursier</label>
                                        <select name="coursier_id" class="form-select" required>
                                            <option value="">-- Sélectionnez un coursier --</option>
                                            <?php foreach ($coursierRepository->findAvailableForDelivery() as $coursier): ?>
                                                <option value="<?= $coursier->getId() ?>">
                                                    <?= htmlspecialchars($coursier->getNomComplet()) ?> 
                                                    (<?= htmlspecialchars($coursier->getTransportLibelle()) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Date de livraison prévue</label>
                                        <input type="date" name="date_livraison" class="form-control" required
                                               min="<?= date('Y-m-d') ?>">
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-send-check me-2"></i>Programmer la livraison
                                        </button>
                                        <a href="traitement.php?search=<?= $demande->getReference() ?>" class="btn btn-outline-secondary">
                                            <i class="bi bi-x-circle me-2"></i>Annuler
                                        </a>
                                    </div>
                                </form>
                                
                                <hr class="my-4">
                            <?php endif; ?>
                            
                            <!-- Formulaire de mise à jour du statut -->
                            <form method="POST">
                                <input type="hidden" name="update_statut" value="1">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Statut</label>
                                    <select name="statut" class="form-select" required>
                                        <?php 
                                        $statutsDisponibles = ['pret', 'annule'];
                                        if ($demande->getMethodeLivraison() !== 'retrait_guichet') {
                                            $statutsDisponibles = ['pret', 'en_livraison', 'livre', 'annule'];
                                        }
                                        
                                        foreach ($statutsDisponibles as $statut): ?>
                                            <option value="<?= $statut ?>"
                                                <?= $demande->getStatut() === $statut ? 'selected' : '' ?>>
                                                <?= ucfirst(str_replace('_', ' ', $statut)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Commentaire (optionnel)</label>
                                    <textarea name="commentaire" class="form-control" rows="3"
                                        placeholder="Ajoutez un commentaire si nécessaire..."></textarea>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Enregistrer les modifications
                                    </button>

                                    <?php if ($demande->getStatut() === 'pret'): ?>
                                        <a href="#" class="btn btn-success" id="generateDocBtn">
                                            <i class="bi bi-file-earmark-pdf me-2"></i>Générer le document
                                        </a>
                                    <?php endif; ?>

                                    <a href="traitement.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Annuler
                                    </a>
                                </div>
                            </form>
                        <?php elseif ($demande->getStatut() === 'en_livraison' || $demande->getStatut() === 'livre'): ?>
                            <!-- Affichage des informations de livraison -->
                            <?php echo LivraisonHelper::showHtml($demande->getId()) ?>
                            
                            <div class="text-center mt-3">
                                <a href="traitement.php" class="btn btn-primary">
                                    <i class="bi bi-arrow-left me-2"></i>Retour
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning text-center py-4">
                                <i class="bi bi-hourglass-split display-4 text-warning mb-3"></i>
                                <h3 class="h4">Document non disponible pour traitement</h3>
                                <p>Statut actuel : <strong><?= htmlspecialchars($demande->getStatutLibelle()) ?></strong></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<script>
    // Script pour la génération de document
    document.getElementById('generateDocBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        // Ici vous pourriez ajouter un appel AJAX pour générer le document
        alert('Fonctionnalité de génération de document à implémenter');
    });
</script>

    <link href="/etatcivil/assets/css/timeline.css" rel="stylesheet">
<?php include '../partials/footer.php'; ?>