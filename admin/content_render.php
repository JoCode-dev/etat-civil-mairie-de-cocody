<?php


class ContentRendu
{

    //<img src="assets/img/favicon.png" alt="" width="30px">
 public static function header(String $title, array $actions = []): String
    {
        $actionshtml = '';
        foreach ($actions as $data) {
            $actionshtml .= ' ' . $data;
        }
        return <<<HTML
        <section class="app-bar">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="app-title">{$title}</h1>
                <div class="d-flex">
                    $actionshtml
                </div>
          </div>
       </section>
HTML;
    }






    public static function render(String $headerHtml, String $bodyHtml, String $statHtml = '', String $alert = ''): String
    {
        return <<<HTML
         <main class="main-content" >
            {$headerHtml}
          
                {$statHtml} 
                {$alert} 
            <div class="card mx-2">
                {$bodyHtml} 
            </div>
         </div>
        </main>
HTML;
    }

   

    static  public function itemCard($title, $contenu, String $src): String
    {

        return <<<HTML
   <div class="col">
        <div class="card shadow-sm">
          <img class="bd-placeholder-img card-img-top" width="100%" height="225" src="{$src}">
          
          <div class="card-body">
            <p class="card-title">{$title}</p>
            <p class="card-text">{$contenu}</p>
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted">9 mins</small>
            </div>
          </div>
        </div>
      </div>
 HTML;
    }


   /**
     * Génère un champ de recherche.
     *
     * @return string Le code HTML du champ de recherche.
     */
    public static function searchField(string $hint='Rechercher ici'): string
    {

        return <<<HTML
        <form method="GET" action="">
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control" name="search" placeholder="{$hint}">
                    
                </div>
    </form>
HTML;
    }

}
