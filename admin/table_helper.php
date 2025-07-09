<?php

class TableHelper
{


    public static function renderStatut(string $title, string $color): string
    {
        $style = "display: inline-block; padding: 3px 8px; border-radius: 5px; color: white; font-weight: bold; background-color: " . htmlspecialchars($color) . ";";
        return '<span style="' . $style . '">' . htmlspecialchars($title) . '</span>';
    }

    

    /**
     * Génère l'ensemble de la table avec l'en-tête et le corps.
     *
     * @param array $columns Les noms des colonnes de la table.
     * @param array $data Les données à afficher dans les lignes de la table.
     * @return string Le code HTML complet de la table.
     */
    public static function table(
        string $actionsBar,
      string $column, 
     string $body,
     string $pagination=''): string
    {
        return <<<HTML
            <div class="card">
                {$actionsBar}
                <div class="card-body">
                     <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            {$column}
                            {$body}
                        </table>
                    </div>
                </div>
                $pagination
            </div> 
HTML;
    }

    public static function actionsBar(int $totalItems, int $dataCount,string $leftaction='',string $rigthAction=''): string
    {
        /*     $html .= '<select class="form-control form-control-sm" id="idstatus" name="idstatus">
                    <option value="tous">Tous</option>
                    <option value="actif">Actif</option>
                    <option value="inactif">Inactif</option>
                </select>'; */
       
        $refrechhtml = self::generateIconButton("?offset=0", "fa-sync-alt", "btn-sm mr-2", "Refresh");

        return <<<HTML
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <span class="mr-2 pr-2">{$dataCount}/{$totalItems}</span>
            <span class="ml-2">{$leftaction}</span>
        </div>
        <div>
          {$rigthAction}
          {$refrechhtml}
        </div>
    </div>
HTML;
    }


    public static function column(array $columns, bool $firstColId = true, bool $lastColAction = true): string
{
    $html = '<thead class="table-light"><tr>';

    foreach ($columns as $index => $column) {
        // Si la première colonne est "ID"
        if ($firstColId && $index === 0 && strtolower($column) === 'id') {
            $html .= '<th style="width: 50px;">#</th>';
        }
       
        // Colonnes normales
        else {
            $html .= '<th>' . htmlspecialchars($column) . '</th>';
        }
    }

    $html .= '</tr></thead>';
    return $html;
}

    /**
     * Génère le corps d'une table avec les données spécifiées.
     *
     * @param array $data Les données à afficher dans les lignes de la table.
     * @return string Le code HTML du corps de la table.
     */
    public static function body(array $data): string
    {
        $html = '<tbody>';
        foreach ($data as $rowData) {
            $html .= self::rows($rowData);
        }
        $html .= '</tbody>';
        return $html;
    }

    /**
     * Génère une ligne de données pour une table avec les valeurs spécifiées.
     *
     * @param array $rowData Les données à afficher dans la ligne.
     * @return string Le code HTML de la ligne de données.
     */
    private static function rows(array $rowData): string
    {
        if (empty($rowData)) {
            return  '<tr> <td colspan="8" class="text-center">Aucune donnée disponible</td></tr>';
        }
        $html = '<tr>';
        foreach ($rowData as $data) {
            $html .= '<td>' . $data . '</td>';
        }
        $html .= '</tr>';
        return $html;
    }

  
   

    /**
     * Génère un bouton d'action pour une ligne de table.
     *
     * @param string $url L'URL vers laquelle le bouton doit pointer.
     * @param double $width (optionnel).
     * @param double $height  (optionnel).
     * @return alt .
     */
    static public function generateImg(String $url, String $alt = '', $width = 50, $height = 50): String
    {
        return '<img src="' . $url . '"  alt="' . $alt . '" width="' . $width . '" height="' . $height . '">';
    }

    


    /**
     * Génère un bouton d'action avec une icône seule pour une ligne de table.
     *
     * @param string $url L'URL vers laquelle le bouton doit pointer.
     * @param string $iconClass La classe CSS de l'icône à afficher.
     * @param string $class La classe CSS supplémentaire à appliquer au bouton.
     * @param string $title Le titre du bouton (tooltip).
     * @return string Le code HTML du bouton d'action avec icône.
     */
    static public function generateIconButton($url, $iconClass, $class = 'primary', $title = ''): String
    {
        return '<a href="' . $url . '" class="btn ' . $class . '" title="' . $title . '"><span class="fas ' . $iconClass . '"></span></a>';
    }


  
  
}
