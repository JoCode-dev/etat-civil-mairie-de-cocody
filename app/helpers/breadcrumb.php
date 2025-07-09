<?php

class Breadcrumb{
    private $items = [];

    /**
     * Ajoute un élément au breadcrumb
     *
     * @param string $label Le label de l'élément
     * @param string|null $url L'URL de l'élément, null si l'élément est actif
     * @return void
     */
    public function addItem(string $label, string $url = null)
    {
        $this->items[] = [
            'label' => $label,
            'url' => $url
        ];
    }

    /**
     * Génère le HTML du breadcrumb
     *
     * @return string Le HTML du breadcrumb
     */
    public function render(): string
    {
        $html = '<nav aria-label="breadcrumb">';
        $html .= '<ol class="breadcrumb">';

        $count = count($this->items);
        foreach ($this->items as $index => $item) {
            if ($item['url'] && $index !== $count - 1) {
                $html .= '<li class="breadcrumb-item"><a href="' . htmlspecialchars($item['url']) . '">' . htmlspecialchars($item['label']) . '</a></li>';
            } else {
                $html .= '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($item['label']) . '</li>';
            }
        }
        $html .= '</ol>';
        $html .= '</nav>';
        return $html;
    }
}


class BreadcrumbHelper {
    private $rootPath ;
    private $entityExt ;
    private $entityTitle ;
    private $entityName ;

    public function __construct(String $entityTitle,String $rootPath = '/'.'admin/',String $entityExt='', String $entityName='') {
        $this->entityTitle = $entityTitle;
        $this->rootPath = $rootPath;
        $this->entityExt = $entityExt;
        $this->entityName = $entityName;
    }

    public function listLink(): String {
        return $this->rootPath.''.$this->entityName.''.$this->entityExt . ".php";
    }

    public function addLink(): String {
        return $this->rootPath.''.$this->entityName . "_edit.php";
    }

    public function editLink(int $id): String {
        return static::addLink() . "?id=$id";
    }

    public function detailsLink(int $id): String {
        return $this->rootPath . $this->entityName . "_details?id=$id";
    }

    public function deleteLink(int $id): String {
        return $this->rootPath . $this->entityName . "_del?id=$id";
    }


    public function list_breadcrumb(): Breadcrumb {
        $breadcrumb = new Breadcrumb();
        $breadcrumb->addItem('Home', '/');
        $breadcrumb->addItem($this->entityTitle, static::listLink());
        return $breadcrumb;
    }

    public function dowm_breadcrumb(String $title): Breadcrumb {
        $breadcrumb = static::list_breadcrumb();
        $breadcrumb->addItem($title, '#');
        return $breadcrumb;
    }

    public function add_breadcrumb(): Breadcrumb {
        return static::dowm_breadcrumb('Ajouter');
    }

    public function edit_breadcrumb(): Breadcrumb {
        return static::dowm_breadcrumb('Modifier');
    }

    public function details_breadcrumb(): Breadcrumb {
        return static::dowm_breadcrumb('Détails');
    }

    public function delete_breadcrumb(): Breadcrumb {
        return static::dowm_breadcrumb('Suppression');
    }
}




