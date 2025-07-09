


<?php require_once __DIR__ . '/citoyen/includes/header.php'; ?>
<div class="container">
    <!-- En-tête -->
    <header class="contact-header text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Contactez la Mairie de Cocody</h1>
            <p class="lead">Nous sommes à votre écoute pour toutes vos demandes</p>
        </div>
    </header>

    <!-- Section principale -->
    <main class="container mb-5">
        <div class="row g-4">
            <!-- Cartes de contact -->
            <div class="col-md-4">
                <div class="card contact-card h-100 p-4 text-center">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Adresse</h3>
                    <p class="text-muted">Boulevard François Mitterrand, Cocody<br>Abidjan, Côte d'Ivoire</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card contact-card h-100 p-4 text-center">
                    <div class="contact-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h3>Téléphone</h3>
                    <p class="text-muted">
                        Standard: +225 27 22 44 00 00<br>
                        Urgences: +225 27 22 44 11 11
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card contact-card h-100 p-4 text-center">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Email</h3>
                    <p class="text-muted">
                        Contact: contact@mairie-cocody.ci<br>
                        Administration: admin@mairie-cocody.ci
                    </p>
                </div>
            </div>
        </div>

        <!-- Formulaire et carte -->
        <div class="row mt-5 g-4">
            <!-- Formulaire de contact -->
            <div class="col-lg-6">
                <div class="card shadow-sm p-4">
                    <h2 class="mb-4">Envoyez-nous un message</h2>
                    <form>
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom complet</label>
                            <input type="text" class="form-control" id="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="sujet" class="form-label">Sujet</label>
                            <select class="form-select" id="sujet" required>
                                <option value="" selected disabled>Choisir un sujet</option>
                                <option value="etat-civil">État civil</option>
                                <option value="urbanisme">Urbanisme</option>
                                <option value="propreté">Propreté</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary px-4">Envoyer</button>
                    </form>
                </div>
            </div>

            <!-- Carte Google Maps -->
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="map-container">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3972.23456789!2d-3.987654321!3d5.3456789!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNcKwMjAnNDQuNCJOIDPCsDU5JzEwLjgiVw!5e0!3m2!1sfr!2sci!4v1234567890123!5m2!1sfr!2sci" 
                                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>

                <!-- Horaires d'ouverture -->
                <div class="card mt-4 shadow-sm p-4">
                    <h3 class="mb-3">Horaires d'ouverture</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2"><strong>Lundi - Vendredi:</strong> 7h30 - 16h30</li>
                        <li class="mb-2"><strong>Samedi:</strong> 8h00 - 12h00</li>
                        <li><strong>Dimanche:</strong> Fermé</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
    </div>

<?php include 'citoyen/includes/footer.php'; ?>