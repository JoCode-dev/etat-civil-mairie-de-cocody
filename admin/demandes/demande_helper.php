<?php
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/repositories/demande_repository.php';

class DemandeHelper
{
    private static ?DemandeRepository $demandeRepository = null;

    
    private static function initRepository(): void
    {
        if (self::$demandeRepository === null) {
            global $db; 
            self::$demandeRepository = new DemandeRepository($db);
        }
    }

   
    public static function showHtml(?int $demandeId): string
    {
        self::initRepository();

        if ($demandeId === null) {
            return self::renderNo('ID de demande invalide');
        }

        try {
            $demande = self::$demandeRepository->findById($demandeId);
            return self::renderInfo($demande);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération de la demande: " . $e->getMessage());
            return self::renderNo('Erreur de chargement des informations de demande');
        }
    }

    /**
     * Rendu des informations de paiement
     */
    private static function renderInfo(?DemandeModel $demande): string
    {
        if ($demande === null) {
            return self::renderNo();
        }

        $reference = htmlspecialchars($demande->getReference());
        $citoyenNom = htmlspecialchars($demande->getCitoyenNom());
        $dateDemande = date('d/m/Y H:i', strtotime($demande->getDateDemande()));

        $statutColor =  $demande->getStatut() === 'en_attente' ? 'warning' : ($demande->getStatut() === 'pret' ? 'success' : ($demande->getStatut() === 'annule' ? 'danger' : 'info'));
        $statutLibelle = ucfirst($demande->getStatut());
        $nombreActes = $demande->getNombreActes();
        $totalFrais = number_format($demande->getTotalFrais(), 0, ',', ' ');
        $fraisUnitaire = number_format($demande->getFraisUnitaire(), 0, ',', ' ');


        return <<<HTML
        <div class="card shadow-sm mb-4">
           <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Informations de la demande</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Référence</dt>
                   <dd class="col-sm-8">
                            <span class="badge bg-secondary">
                                {$reference}
                            </span>
                </dd>

                    <dt class="col-sm-4">Citoyen</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-light text-dark">{$citoyenNom}</span>
                    </dd>

                   <dt class="col-sm-4">Date demande</dt>
                        <dd class="col-sm-8">
                           {$dateDemande}
                        </dd>

                    

                    <dt class="col-sm-4">Statut</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{$statutColor}">
                                {$statutLibelle}
    
                            </span>
                        </dd>

                        <dt class="col-sm-4">Nombre d'actes</dt>
                        <dd class="col-sm-8">
                            {$nombreActes} FCFA
                        </dd>   

                     <dt class="col-sm-4">Frais unitaire</dt>
                        <dd class="col-sm-8">
                            {$fraisUnitaire} FCFA
                        </dd>   
                         <dt class="col-sm-4">Frais total</dt>
                        <dd class="col-sm-8">
                            {$totalFrais} FCFA
                        </dd>  
                </dl>
            </div>
        </div>
HTML;
    }

    /**
     * Rendu quand aucune démande n'est disponible
     */
    private static function renderNo(?string $message = null): string
    {
        $msg = $message ?? 'Aucun paiement enregistré pour cette demande';

        return <<<HTML
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Informations de la demande</h5>
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
     * Récupère les informations de Demande (pour usage externe si nécessaire)
     */
    public static function getInfo(int $demandeId): ?DemandeModel
    {
        self::initRepository();
        return self::$demandeRepository->findById($demandeId);
    }
}
