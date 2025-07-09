<?php
require_once __DIR__ . '/../../app/repositories/demande_statut_historique_repository.php';
require_once __DIR__ . '/../../app/config/database.php';
class StatutHistoriqueHelper
{
    private static ?DemandeStatutHistoriqueRepository $statutRepo = null;

    /**
     * Initialise le repository si nécessaire
     */
    private static function initRepository(): void
    {
        if (self::$statutRepo === null) {
            global $db; // À remplacer par une injection de dépendance si possible
            self::$statutRepo = new DemandeStatutHistoriqueRepository($db);
        }
    }

    /**
     * Génère l'affichage HTML de l'historique des statuts
     */
    public static function showHtml(?int $demandeId): string
    {
        self::initRepository();
        
        if ($demandeId === null) {
            return self::renderNoHistory('ID de demande invalide');
        }

        try {
            $historiqueStatuts = self::$statutRepo->getHistoriqueByDemandeId($demandeId);
            
            if (empty($historiqueStatuts)) {
                return self::renderNoHistory();
            }

            return self::renderTimeline($historiqueStatuts);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération de l'historique: " . $e->getMessage());
            return self::renderNoHistory('Erreur de chargement de l\'historique');
        }
    }

    /**
     * Rendu de la timeline des statuts
     */
    private static function renderTimeline(array $historiqueStatuts): string
    {
        $items = '';
        foreach ($historiqueStatuts as $historique) {
            $items .= self::renderTimelineItem(
                $historique->getStatut(),
                $historique->getStatutLibelle(),
                $historique->getDateModification(),
                $historique->getOfficierNom(),
                $historique->getCommentaire()
            );
        }

        return <<<HTML
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historique des statuts</h5>
            </div>
            <div class="card-body">
                <div class="timeline">$items</div>
            </div>
        </div>
HTML;
    }

    /**
     * Rendu d'un élément de timeline
     */
    private static function renderTimelineItem(
        string $statut,
        string $statutLibelle,
        string $dateModification,
        string $officierNom,
        ?string $commentaire
    ): string {
        $badgeColor = self::getBadgeColor($statut);
        $dateFormatted = date('d/m/Y H:i', strtotime($dateModification));
        $commentHtml = $commentaire ? 
            '<p class="mt-1 small">' . nl2br(htmlspecialchars($commentaire)) . '</p>' : '';

        return <<<HTML
        <div class="timeline-item">
            <div class="timeline-badge bg-$badgeColor"></div>
            <div class="timeline-content">
                <h6>{$statutLibelle}</h6>
                <small class="text-muted">
                    {$dateFormatted} par {$officierNom}
                </small>
                {$commentHtml}
            </div>
        </div>
HTML;
    }

    /**
     * Rendu quand aucun historique n'est disponible
     */
    private static function renderNoHistory(?string $message = null): string
    {
        $msg = $message ?? 'Aucun historique de statut disponible';
        
        return <<<HTML
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historique des statuts</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    {$msg}
                </div>
            </div>
        </div>
HTML;
    }

    /**
     * Détermine la couleur du badge selon le statut
     */
    private static function getBadgeColor(string $statut): string
    {
        switch ($statut) {
            case 'pret': return 'success';
            case 'annule': return 'danger';
            case 'en_attente': return 'warning';
            case 'recupere': return 'primary';
            default: return 'info';
        }
    }

    
}