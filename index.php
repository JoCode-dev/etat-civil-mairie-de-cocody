<?php
require_once __DIR__ . '/app/config/constants.php';
require_once 'app/controllers/citoyen_controller.php';


// Définir l'URL de base
$baseUrl = '/etatcivil/';

require_once __DIR__ . '/citoyen/includes/header.php';

?>

<section class="hero">
    <h1>Bienvenue à la <?php echo COMMUNE_NAME; ?></h1>
    <p>Une commune où il fait bon vivre, entre tradition et modernité</p>
    <div class="d-flex justify-content-center mt-4 ">
        <a href="acte_demande.php" class="btn btn-primary me-2 rounded-pill" style="background-color: var(--orange)">Faire une demande d'acte</a>
        <a href="contacts.php" class="btn btn-secondary rounded-pill" style="opacity: 0.8;">Nous contacter</a>
    </div>
</section>


<div class="container">
    <section class="services">
        <div class="service-card">
                            <img src="<?= $baseUrl ?>assets/img/doc.png" alt="Extrait de naissance">
            <div class="service-content">
                <h3>Extrait de Naissance</h3>
                <p>Demandez un extrait d'acte de naissance en ligne rapidement et facilement.</p>
                <a href="acte_demande.php" class="btn">Faire une demande</a>
            </div>
        </div>

        <div class="service-card">
                            <img src="<?= $baseUrl ?>assets/img/doc.png" alt="Extrait de mariage">
            <div class="service-content">
                <h3>Extrait de Mariage</h3>
                <p>Obtenez un extrait d'acte de mariage pour vos démarches administratives.</p>
                <a href="acte_demande.php" class="btn">Faire une demande</a>
            </div>
        </div>

        <div class="service-card">
                            <img src="<?= $baseUrl ?>assets/img/doc.png" alt="Extrait de décès">
            <div class="service-content">
                <h3>Extrait de Décès</h3>
                <p>Faites une demande d'extrait d'acte de décès en toute simplicité.</p>
                <a href="acte_demande.php" class="btn">Faire une demande</a>
            </div>
        </div>
    </section>
    <section class="urgence">
        <h2>Urgence Municipale</h2>
        <p>En cas d'urgence (coupure d'eau, éclairage public, etc.), contactez le service technique :</p>
        <p><strong>01 23 45 67 89 (24h/24)</strong></p>
    </section>

    <section class="actualites">
        <h2>Dernières actualités</h2>
        <div class="actus-grid">
            <div class="actu-card">
                <img src="assets/images/actu-fete.jpg" alt="Fête de la musique">
                <div class="actu-content">
                    <div class="actu-date">15 juin 2023</div>
                    <h3>Fête de la musique 2023</h3>
                    <p>Programme complet des animations prévues pour la fête de la musique dans notre commune.</p>
                    <a href="#" class="btn">Lire la suite</a>
                </div>
            </div>

            <div class="actu-card">
                <img src="assets/images/actu-travaux.jpg" alt="Travaux routiers">
                <div class="actu-content">
                    <div class="actu-date">10 juin 2023</div>
                    <h3>Travaux routiers en juillet</h3>
                    <p>Planning des travaux prévus sur les axes principaux de la commune cet été.</p>
                    <a href="#" class="btn">Lire la suite</a>
                </div>
            </div>

            <div class="actu-card">
                <img src="assets/images/actu-conseil.jpg" alt="Conseil municipal">
                <div class="actu-content">
                    <div class="actu-date">5 juin 2023</div>
                    <h3>Compte-rendu du conseil municipal</h3>
                    <p>Retour sur les principales décisions prises lors du dernier conseil municipal.</p>
                    <a href="#" class="btn">Lire la suite</a>
                </div>
            </div>
        </div>
        <div style="text-align: center; margin-top: 30px;">
            <a href="#" class="btn">Voir toutes les actualités</a>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/citoyen/includes/footer.php'; ?>