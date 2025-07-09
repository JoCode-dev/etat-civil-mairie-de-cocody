<?php
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/repositories/paiement_repository.php';

class PaiementInfoHelper
{
    private static ?PaiementRepository $paiementrepository = null;

    /**
     * Initialise le repository si nécessaire
     */
    private static function initRepository(): void
    {
        if (self::$paiementrepository === null) {
            global $db; // À remplacer par une injection de dépendance si possible
            self::$paiementrepository = new PaiementRepository($db);
        }
    }

    /**
     * Génère l'affichage HTML des informations de paiement
     */
    public static function showHtml(?int $demandeId): string
    {
        self::initRepository();
        
        if ($demandeId === null) {
            return self::renderNoPayment('ID de demande invalide');
        }

        try {
            $paiement = self::$paiementrepository->findByDemandeId($demandeId);
            return self::renderPaymentInfo($paiement);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération du paiement: " . $e->getMessage());
            return self::renderNoPayment('Erreur de chargement des informations de paiement');
        }
    }

    /**
     * Rendu des informations de paiement
     */
    private static function renderPaymentInfo(?PaiementModel $paiement): string
    {
        if ($paiement === null) {
            return self::renderNoPayment();
        }

        $montant = number_format($paiement->getMontant(), 0, ',', ' ');
        $methodePaiement = strtoupper(str_replace('_', ' ', $paiement->getMethodePaiement()));
        $reference = htmlspecialchars($paiement->getReference());
        $datePaiement = date('d/m/Y H:i', strtotime($paiement->getDateTransaction()));
        $statut = $paiement->getStatut();
        $badgeColor = $statut === 'complete' ? 'success' : 'warning';
        $statutLibelle = ucfirst($statut);

        return <<<HTML
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Informations de paiement</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Montant payé</dt>
                    <dd class="col-sm-8">{$montant} FCFA</dd>

                    <dt class="col-sm-4">Méthode</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-light text-dark">{$methodePaiement}</span>
                    </dd>

                    <dt class="col-sm-4">Référence</dt>
                    <dd class="col-sm-8"><code>{$reference}</code></dd>

                    <dt class="col-sm-4">Date paiement</dt>
                    <dd class="col-sm-8">{$datePaiement}</dd>

                    <dt class="col-sm-4">Statut</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-{$badgeColor}">{$statutLibelle}</span>
                    </dd>
                </dl>
            </div>
        </div>
HTML;
    }

    /**
     * Rendu quand aucun paiement n'est disponible
     */
    private static function renderNoPayment(?string $message = null): string
    {
        $msg = $message ?? 'Aucun paiement enregistré pour cette demande';
        
        return <<<HTML
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Informations de paiement</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {$msg}
                </div>
            </div>
        </div>
HTML;
    }

    /**
     * Récupère les informations de paiement (pour usage externe si nécessaire)
     */
    public static function getPaymentInfo(int $demandeId): ?PaiementModel
    {
        self::initRepository();
        return self::$paiementrepository->findByDemandeId($demandeId);
    }
}