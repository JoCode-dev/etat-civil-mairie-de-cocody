<?php

class StatsHelper
{
    /**
     * Génère une carte de statistique avec un nombre et une icône.
     *
     * @param string $title Le titre de la statistique.
     * @param int $value La valeur numérique.
     * @param string|null $iconClass La classe CSS de l'icône (facultatif).
     * @param string $bgColor La couleur de fond (par défaut : 'bg-primary').
     * @return string Le code HTML de la carte.
     */
    public static function statCard(string $title, int $value, ?string $iconClass = null, string $bgColor = 'bg-primary'): string
    {
        $iconHtml = $iconClass ? "<i class='{$iconClass} me-2'></i>" : '';
        return <<<HTML
        <div class="card text-white {$bgColor} mb-3" style="max-width: 18rem;">
            <div class="card-body">
                <h5 class="card-title">{$title}</h5>
                <p class="card-text display-4">{$iconHtml}{$value}</p>
            </div>
        </div>
HTML;
    }

    /**
     * Génère une carte de statistique avec un statut (actif/inactif).
     *
     * @param string $title Le titre de la statistique.
     * @param int $activeCount Le nombre d'éléments actifs.
     * @param int $inactiveCount Le nombre d'éléments inactifs.
     * @return string Le code HTML de la carte.
     */
    public static function statusCard(string $title, int $activeCount, int $inactiveCount): string
    {
        return <<<HTML
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{$title}</h5>
                <p class="card-text">
                    <span class="badge bg-success">Actifs : {$activeCount}</span>
                    <span class="badge bg-danger">Inactifs : {$inactiveCount}</span>
                </p>
            </div>
        </div>
HTML;
    }

    /**
     * Génère un graphique en utilisant Chart.js.
     *
     * @param string $chartId L'ID unique du graphique.
     * @param array $labels Les étiquettes du graphique.
     * @param array $data Les données du graphique.
     * @param string $type Le type de graphique (par défaut : 'bar').
     * @param array $colors Les couleurs des barres ou des segments.
     * @return string Le code HTML et JavaScript du graphique.
     */
    public static function chart(string $chartId, array $labels, array $data, string $type = 'bar', array $colors = []): string
    {
        $labelsJson = json_encode($labels);
        $dataJson = json_encode($data);
        $colorsJson = json_encode($colors);

        return <<<HTML
        <canvas id="{$chartId}" width="400" height="200"></canvas>
        <script>
            const ctx = document.getElementById('{$chartId}').getContext('2d');
            new Chart(ctx, {
                type: '{$type}',
                data: {
                    labels: {$labelsJson},
                    datasets: [{
                        label: 'Statistiques',
                        data: {$dataJson},
                        backgroundColor: {$colorsJson},
                        borderColor: {$colorsJson},
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });
        </script>
HTML;
    }

    /**
     * Génère une liste de statistiques sous forme de badges.
     *
     * @param array $stats Un tableau associatif où la clé est le label et la valeur est le nombre.
     * @return string Le code HTML des badges.
     */
    public static function badgeList(array $stats): string
    {
        $html = '<div class="d-flex flex-wrap gap-2">';
        foreach ($stats as $label => $value) {
            $html .= <<<HTML
            <span class="badge bg-info text-dark">{$label} : {$value}</span>
HTML;
        }
        $html .= '</div>';
        return $html;
    }
}