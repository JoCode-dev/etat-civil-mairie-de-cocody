<?php

$navs = [
    ["name" => "Tableau de bord", "id" => "dashbord", "href" => "/admin/dashboard", "icon" => "fas fa-tachometer-alt"],
    ["name" => "Demandes", "id" => "demandes", "href" => "/admin/demandes/list", "icon" => "fas fa-clipboard-list"],
    ["name" => "Traitement démandes", "id" => "traitement", "href" => "/admin/demandes/traitement", "icon" => "fas fa-clipboard-list"],
    ["name" => "Retrait démandes", "id" => "recuperation", "href" => "/admin/demandes/recuperation", "icon" => "fas fa-clipboard-list"],
    ["name" => "Administrateurs", "id" => "admins",  "href" => "/admin/admins/list", "icon" => "fas fa-user-shield"],
    ["name" => "Compte des rôles", "id" => "roles",  "href" => "/admin/roles/list", "icon" => "fas fa-user-shield"],
    ["name" => "Comptes citoyens", "id" => "citoyens", "href" => "/admin/citoyens/list", "icon" => "fas fa-users"],
    ["name" => "Actes de naissance", "id" => "naissances", "href" => "/admin/actes_naissance/list", "icon" => "fas fa-baby"],
    ["name" => "Actes de mariage", "id" => "mariages", "href" => "/admin/actes_mariages/list", "icon" => "fas fa-heart"],
    ["name" => "Actes de déces", "id" => "deces", "href" => "/admin/actes_deces/list", "icon" => "fas fa-cross"],
    ["name" => "Type d'actes", "id" => "type_actes", "href" => "/admin/type_actes/list", "icon" => "fas fa-exchange-alt"],
];

?>


 <aside class="sidebar">
    <div class="app-brand">Gestion Etat Civil</div>
    <div class="sidebar-menu">
        <?php foreach ($navs as $nav): ?>
            <a class="sidebar-item <?php if ($activeMenu === strtolower($nav['id'])) echo 'active'; ?>" href="<?php echo $nav['href']; ?>">
                <i class="<?php echo $nav['icon']; ?> sidebar-icon"></i>
                <span><?php echo $nav['name']; ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</aside>

