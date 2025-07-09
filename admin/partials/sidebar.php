<?php

$navs = [
    ["name" => "Tableau de bord", "id" => "dashbord", "href" => "/etatcivil/admin/dashboard.php", "icon" => "fas fa-tachometer-alt"],
    ["name" => "Demandes", "id" => "demandes", "href" => "/etatcivil/admin/demandes/list.php", "icon" => "fas fa-clipboard-list"],
    ["name" => "Traitement démandes", "id" => "traitement", "href" => "/etatcivil/admin/demandes/traitement.php", "icon" => "fas fa-clipboard-list"],
    ["name" => "Retrait démandes", "id" => "recuperation", "href" => "/etatcivil/admin/demandes/recuperation.php", "icon" => "fas fa-clipboard-list"],
    ["name" => "Administrateurs", "id" => "admins",  "href" => "/etatcivil/admin/admins/list.php", "icon" => "fas fa-user-shield"],
    ["name" => "Compte des rôles", "id" => "roles",  "href" => "/etatcivil/admin/roles/list.php", "icon" => "fas fa-user-shield"],
    ["name" => "Comptes citoyens", "id" => "citoyens", "href" => "/etatcivil/admin/citoyens/list.php", "icon" => "fas fa-users"],
    ["name" => "Actes de naissance", "id" => "naissances", "href" => "/etatcivil/admin/actes_naissance/list.php", "icon" => "fas fa-baby"],
    ["name" => "Actes de mariage", "id" => "mariages", "href" => "/etatcivil/admin/actes_mariages/list.php", "icon" => "fas fa-heart"],
    ["name" => "Actes de déces", "id" => "deces", "href" => "/etatcivil/admin/actes_deces/list.php", "icon" => "fas fa-cross"],
    ["name" => "Type d'actes", "id" => "type_actes", "href" => "/etatcivil/admin/type_actes/list.php", "icon" => "fas fa-exchange-alt"],
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

