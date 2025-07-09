<?php

class PaginationHelper
{
    /**
     * Génère une pagination Bootstrap.
     *
     * @param int $currentPage La page actuelle.
     * @param int $totalPages Le nombre total de pages.
     * @param string $baseUrl L'URL de base pour les liens de pagination.
     * @return string Le code HTML de la pagination.
     */
    public static function render(int $currentPage, int $totalPages, string $baseUrl): string
    {
        if ($totalPages <= 1) {
            return ''; // Pas de pagination si une seule page
        }

        $html = '<nav aria-label="Page navigation" class="mt-4">';
        $html .= '<ul class="pagination justify-content-center">';

        // Bouton "Précédent"
        if ($currentPage > 1) {
            $prevPage = $currentPage - 1;
            $html .= '<li class="page-item">';
            $html .= '<a class="page-link" href="' . htmlspecialchars($baseUrl . '?page=' . $prevPage) . '">Précédent</a>';
            $html .= '</li>';
        } else {
            $html .= '<li class="page-item disabled">';
            $html .= '<a class="page-link" href="#" tabindex="-1">Précédent</a>';
            $html .= '</li>';
        }

        // Pages numérotées
        for ($i = 1; $i <= $totalPages; $i++) {
            if ($i == $currentPage) {
                $html .= '<li class="page-item active">';
                $html .= '<a class="page-link" href="#">' . $i . '</a>';
                $html .= '</li>';
            } else {
                $html .= '<li class="page-item">';
                $html .= '<a class="page-link" href="' . htmlspecialchars($baseUrl . '?page=' . $i) . '">' . $i . '</a>';
                $html .= '</li>';
            }
        }

        // Bouton "Suivant"
        if ($currentPage < $totalPages) {
            $nextPage = $currentPage + 1;
            $html .= '<li class="page-item">';
            $html .= '<a class="page-link" href="' . htmlspecialchars($baseUrl . '?page=' . $nextPage) . '">Suivant</a>';
            $html .= '</li>';
        } else {
            $html .= '<li class="page-item disabled">';
            $html .= '<a class="page-link" href="#" tabindex="-1">Suivant</a>';
            $html .= '</li>';
        }

        $html .= '</ul>';
        $html .= '</nav>';

        return $html;
    }
}