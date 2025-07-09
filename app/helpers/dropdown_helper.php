<?php

class DropdownHelper
{
    /**
     * Génère un menu déroulant Bootstrap.
     *
     * @param string $buttonLabel Le texte par défaut du bouton principal.
     * @param string $iconClass La classe CSS de l'icône du bouton.
     * @param array $items Les éléments du menu (chaque élément peut être un texte, un lien ou un séparateur).
     * @param string $selectedValue La valeur actuellement sélectionnée.
     * @param string $buttonClass La classe CSS du bouton principal.
     * @param string $dropdownId L'ID unique du menu déroulant.
     * @return string Le code HTML du menu déroulant.
     */
    public static function render(
        string $buttonLabel = 'Plus de filtres',
        string $iconClass = 'bi bi-funnel',
        array $items = [],
        string $selectedValue = '',
        string $buttonClass = 'btn btn-sm btn-outline-secondary',
        string $dropdownId = 'moreFilters'
    ): string {
        // Si une valeur est sélectionnée, elle remplace le texte du bouton
        $buttonLabel = $selectedValue ?: $buttonLabel;

        $html = <<<HTML
        <div class="dropdown">
            <button class="$buttonClass dropdown-toggle" type="button" id="$dropdownId" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="$iconClass"></i> $buttonLabel
            </button>
            <ul class="dropdown-menu" aria-labelledby="$dropdownId">
HTML;

        $html .= self::generateDropdownItems($items, $selectedValue);

        $html .= <<<HTML
            </ul>
        </div>
HTML;

        return $html;
    }

    /**
     * Génère les éléments du menu déroulant.
     *
     * @param array $items Les éléments du menu (chaque élément peut être un texte, un lien ou un séparateur).
     * @param string $selectedValue La valeur actuellement sélectionnée.
     * @return string Le code HTML des éléments du menu déroulant.
     */
    public static function generateDropdownItems(array $items, string $selectedValue = ''): string
    {
        $html = '';

        foreach ($items as $item) {
            if (is_array($item)) {
                // Si l'élément est un lien avec un texte et une URL
                $label = htmlspecialchars($item['label'] ?? '');
                $url = htmlspecialchars($item['url'] ?? '#');
                $activeClass = ($label === $selectedValue) ? 'active' : '';
                $html .= '<li><a class="dropdown-item ' . $activeClass . '" href="' . $url . '">' . $label . '</a></li>';
            } elseif ($item === '---') {
                // Ajouter un séparateur
                $html .= '<li><hr class="dropdown-divider"></li>';
            } else {
                // Ajouter un élément de texte simple
                $activeClass = ($item === $selectedValue) ? 'active' : '';
                $html .= '<li><a class="dropdown-item ' . $activeClass . '" href="#">' . htmlspecialchars($item) . '</a></li>';
            }
        }

        return $html;
    }
}