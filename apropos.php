
<?php 
require_once 'app/controllers/citoyen_controller.php';

// Définir l'URL de base
$baseUrl = '/etatcivil/';

require_once __DIR__ . '/citoyen/includes/header.php'; ?>
<div class="container">
    

    <!-- Section Présentation -->
    <section class="container mb-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="mb-4">Cocody, une commune d'excellence</h2>
                <p class="lead">Située au cœur d'Abidjan, Cocody est l'une des communes les plus dynamiques et attractives de Côte d'Ivoire.</p>
                <p>Cocody se distingue par son cadre de vie exceptionnel, mélange harmonieux entre modernité et tradition. Avec ses larges avenues bordées de flamboyants, ses institutions éducatives de renom et ses quartiers résidentiels huppés, Cocody incarne l'excellence ivoirienne.</p>
                <p>La commune abrite également de nombreuses ambassades et institutions internationales, ce qui en fait un carrefour diplomatique majeur en Afrique de l'Ouest.</p>
            </div>
            <div class="col-lg-6">
                <img src="<?= $baseUrl ?>assets/img/mairie.png" alt="Panorama de Cocody" class="img-fluid rounded shadow">
            </div>
        </div>
    </section>

    <!-- Section Chiffres clés -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Cocody en chiffres</h2>
            <div class="row text-center">
                <div class="col-md-3 mb-4">
                    <div class="p-4 about-card">
                        <div class="stat-number">380,000</div>
                        <p class="mb-0">Habitants</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="p-4 about-card">
                        <div class="stat-number">78</div>
                        <p class="mb-0">km² de superficie</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="p-4 about-card">
                        <div class="stat-number">12</div>
                        <p class="mb-0">Quartiers</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="p-4 about-card">
                        <div class="stat-number">1960</div>
                        <p class="mb-0">Année de création</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Histoire -->
    <section class="container my-5">
        <h2 class="text-center mb-5">Notre histoire</h2>
        <div class="timeline">
            <div class="timeline-item">
                <h4>1950</h4>
                <p>Cocody était à l'origine un village ébrié, peuplé par les autochtones de cette ethnie. Le nom "Cocody" signifie "la maison des hommes" en langue ébrié.</p>
            </div>
            <div class="timeline-item">
                <h4>1960</h4>
                <p>Après l'indépendance de la Côte d'Ivoire, Cocody devient une commune à part entière et commence son développement urbain.</p>
            </div>
            <div class="timeline-item">
                <h4>1970-1980</h4>
                <p>Période de fort développement avec la construction de grandes infrastructures comme l'Hôtel Ivoire et l'Université Félix Houphouët-Boigny.</p>
            </div>
            <div class="timeline-item">
                <h4>2000 à aujourd'hui</h4>
                <p>Cocody s'affirme comme le quartier diplomatique et résidentiel privilégié d'Abidjan, avec un développement continu de ses infrastructures et services.</p>
            </div>
        </div>
    </section>

    <!-- Section Équipe municipale -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Notre équipe municipale</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="team-member">
                        <img src="https://example.com/maire.jpg" alt="Maire de Cocody" class="team-img">
                        <h4 class="mb-1">Jean-Marc Yacé</h4>
                        <p class="text-muted">Maire de Cocody</p>
                        <p>Élu depuis 2018, il œuvre pour une commune moderne et inclusive.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-member">
                        <img src="https://example.com/adjoint1.jpg" alt="Premier adjoint" class="team-img">
                        <h4 class="mb-1">Aminata Koné</h4>
                        <p class="text-muted">Première adjointe</p>
                        <p>En charge des affaires sociales et de l'éducation.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-member">
                        <img src="https://example.com/adjoint2.jpg" alt="Deuxième adjoint" class="team-img">
                        <h4 class="mb-1">Paul Akoto</h4>
                        <p class="text-muted">Deuxième adjoint</p>
                        <p>Responsable des infrastructures et de l'urbanisme.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Missions et valeurs -->
    <section class="container my-5">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100 about-card p-4">
                    <div class="card-body">
                        <h3 class="card-title mb-4"><i class="fas fa-bullseye me-2 text-primary"></i> Nos missions</h3>
                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Assurer la sécurité et la salubrité publique</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Développer les infrastructures urbaines</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Promouvoir l'éducation et la culture</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Soutenir le développement économique local</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Préserver l'environnement et le cadre de vie</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card h-100 about-card p-4">
                    <div class="card-body">
                        <h3 class="card-title mb-4"><i class="fas fa-heart me-2 text-primary"></i> Nos valeurs</h3>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h5><i class="fas fa-users text-info me-2"></i> Proximité</h5>
                                <p>Être à l'écoute de nos concitoyens pour mieux servir.</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h5><i class="fas fa-balance-scale text-info me-2"></i> Transparence</h5>
                                <p>Gérer les affaires communales avec rigueur et clarté.</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h5><i class="fas fa-lightbulb text-info me-2"></i> Innovation</h5>
                                <p>Innover pour améliorer les services aux administrés.</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h5><i class="fas fa-handshake text-info me-2"></i> Solidarité</h5>
                                <p>Œuvrer pour une commune inclusive et solidaire.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
   </div>

    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://example.com/cocody-banner.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 120px 0;
            margin-bottom: 50px;
        }
        .about-card {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            height: 100%;
        }
        .about-card:hover {
            transform: translateY(-5px);
        }
        .timeline {
            position: relative;
            padding-left: 50px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #0d6efd;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 30px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -38px;
            top: 5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #0d6efd;
            border: 3px solid white;
        }
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: #0d6efd;
        }
        .team-member {
            text-align: center;
            margin-bottom: 30px;
        }
        .team-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #f8f9fa;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }
    </style>

<?php include 'citoyen/includes/footer.php'; ?>