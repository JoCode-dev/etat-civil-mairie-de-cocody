<?php
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/repositories/demande_repository.php';
require_once __DIR__ . '/../../app/repositories/coursier_repository.php';

class LivraisonHelper
{
    private static ?DemandeRepository $demandeRepository = null;
    private static ?CoursierRepository $coursierRepository = null;

    private static function initRepositories(): void
    {
        if (self::$demandeRepository === null) {
            global $db;
            self::$demandeRepository = new DemandeRepository($db);
            self::$coursierRepository = new CoursierRepository($db);
        }
    }

    public static function showHtml(?int $demandeId): string
    {
        self::initRepositories();

        if ($demandeId === null) {
            return self::renderNo('ID de demande invalide');
        }

        try {
            $demande = self::$demandeRepository->findById($demandeId, true);
            
            // Ne pas afficher si retrait guichet ou demande non trouvée
            if ($demande === null || $demande->getMethodeLivraison() === 'retrait_guichet') {
                return '';
            }
            
            return self::renderInfo($demande);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des infos de livraison: " . $e->getMessage());
            return self::renderNo('Erreur de chargement des informations de livraison');
        }
    }

    private static function renderInfo(DemandeModel $demande): string
    {
        $methodeLivraison = self::getMethodeLivraisonLibelle($demande->getMethodeLivraison());
        $fraisLivraison = number_format($demande->getFraisLivraison(), 0, ',', ' ');
        $adresseLivraison = htmlspecialchars($demande->getAdresseLivraison() ?? 'Non spécifiée');
        
        // Coursier info
        $coursierInfo = 'Non assigné';
        if ($demande->getCoursierId()) {
            $coursier = self::$coursierRepository->findById($demande->getCoursierId());
            if ($coursier) {
                $coursierInfo = htmlspecialchars($coursier->getNomComplet()) . 
                               ' (' . htmlspecialchars($coursier->getTransportLibelle()) . ')';
            }
        }
        
        // Dates
        $datePrevue = $demande->getDateLivraisonPrevue() 
            ? date('d/m/Y', strtotime($demande->getDateLivraisonPrevue())) 
            : 'Non planifiée';
            
        $dateEffectuee = $demande->getDateLivraisonEffectuee()
            ? date('d/m/Y H:i', strtotime($demande->getDateLivraisonEffectuee()))
            : ($demande->getStatut() === 'livre' ? 'Livré (date non enregistrée)' : 'En attente');

        // Numéro de suivi
        $numeroSuivi = $demande->getNumeroSuivi() ? htmlspecialchars($demande->getNumeroSuivi()) : 'Non généré';

        return <<<HTML
        <div class="card border-primary mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Informations de livraison</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Méthode</dt>
                    <dd class="col-sm-8">{$methodeLivraison}</dd>
                    
                    <dt class="col-sm-4">Frais</dt>
                    <dd class="col-sm-8">{$fraisLivraison} FCFA</dd>
                    
                    <dt class="col-sm-4">Adresse</dt>
                    <dd class="col-sm-8">{$adresseLivraison}</dd>
                    
                    <dt class="col-sm-4">Coursier</dt>
                    <dd class="col-sm-8">{$coursierInfo}</dd>
                    
                    <dt class="col-sm-4">Numéro de suivi</dt>
                    <dd class="col-sm-8">{$numeroSuivi}</dd>
                    
                    <dt class="col-sm-4">Date prévue</dt>
                    <dd class="col-sm-8">{$datePrevue}</dd>
                    
                    <dt class="col-sm-4">Date effective</dt>
                    <dd class="col-sm-8">{$dateEffectuee}</dd>
                </dl>
                
                <div class="mt-3 text-center">
                    <button class="btn btn-success" onclick="confirmerLivraison({$demande->getId()})">
                        <i class="bi bi-check-circle me-2"></i>Confirmer la livraison
                    </button>
                </div>
            </div>
        </div>
        
        <script>
        function confirmerLivraison(demandeId) {
            if (confirm("Confirmez-vous que cette livraison a été effectuée ?")) {
                fetch("actions/confirmer_livraison.php?demande_id=" + demandeId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert("Erreur: " + (data.message || "Échec de la confirmation"));
                        }
                    })
                    .catch(error => {
                        alert("Erreur réseau: " + error);
                    });
            }
        }
        </script>
HTML;
    }

    private static function renderNo(?string $message = null): string
    {
        $msg = $message ?? 'Aucune information de livraison disponible';

        return <<<HTML
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Informations de livraison</h5>
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

    private static function getMethodeLivraisonLibelle(string $methode): string
    {
        switch ($methode) {
            case 'livraison_domicile':
                return '<i class="bi bi-house-door me-2"></i>Livraison à domicile';
            case 'livraison_point_relais':
                return '<i class="bi bi-shop me-2"></i>Point relais';
            default:
                return '<i class="bi bi-building me-2"></i>Retrait à la mairie';
        }
    }
}