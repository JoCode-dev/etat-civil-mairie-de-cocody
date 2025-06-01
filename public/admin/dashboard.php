<?php
require_once __DIR__ . '/../../app/Config/Constants.php';
require_once __DIR__ . '/../../app/Config/Database.php';
require_once __DIR__ . '/../../app/Helpers/alert_helper.php';
require_once __DIR__ . '/../../app/Helpers/ActionHelper.php';
require_once __DIR__ . '/../../app/Controllers/AdminController.php';
require_once __DIR__ . '/../../app/Repositories/admin_repository.php';


AdminController::requirelogin();
$activeMenu = "dashbord";


?>
<?php include 'partials/header.php'; ?>



<main class="main-content">



    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Tableau de Bord</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary">Partager</button>
                <button type="button" class="btn btn-sm btn-outline-secondary">Exporter</button>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                <i class="fas fa-calendar"></i> Cette semaine
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-file-alt card-icon"></i>
                    <h5 class="card-title">Demandes Total</h5>
                    <h2 class="card-text">1,254</h2>
                    <p class="card-text">+12% depuis hier</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle card-icon"></i>
                    <h5 class="card-title">Demandes Traitées</h5>
                    <h2 class="card-text">856</h2>
                    <p class="card-text">+8% depuis hier</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-clock card-icon"></i>
                    <h5 class="card-title">En Attente</h5>
                    <h2 class="card-text">245</h2>
                    <p class="card-text">-5% depuis hier</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle card-icon"></i>
                    <h5 class="card-title">Réclamations</h5>
                    <h2 class="card-text">32</h2>
                    <p class="card-text">+2 nouvelles</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Tables -->
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-line me-1"></i>
                    Statistiques des Demandes (30 derniers jours)
                </div>
                <div class="card-body">
                    <canvas id="demandesChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Répartition des Types d'Actes
                </div>
                <div class="card-body">
                    <canvas id="actesPieChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-list me-1"></i>
                    Dernières Demandes
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Type</th>
                                    <th>Citoyen</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>DEM-000125</td>
                                    <td>Naissance</td>
                                    <td>Jean Dupont</td>
                                    <td><span class="badge bg-success">Prêt</span></td>
                                </tr>
                                <tr>
                                    <td>DEM-000124</td>
                                    <td>Mariage</td>
                                    <td>Marie Lambert</td>
                                    <td><span class="badge bg-warning">En traitement</span></td>
                                </tr>
                                <tr>
                                    <td>DEM-000123</td>
                                    <td>Décès</td>
                                    <td>Pierre Martin</td>
                                    <td><span class="badge bg-primary">En attente</span></td>
                                </tr>
                                <tr>
                                    <td>DEM-000122</td>
                                    <td>Naissance</td>
                                    <td>Sophie Bernard</td>
                                    <td><span class="badge bg-info">En livraison</span></td>
                                </tr>
                                <tr>
                                    <td>DEM-000121</td>
                                    <td>Mariage</td>
                                    <td>Luc Durand</td>
                                    <td><span class="badge bg-success">Livré</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-bell me-1"></i>
                    Dernières Activités
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-alt text-primary me-2"></i>
                                Nouvelle demande #DEM-000125
                                <small class="text-muted d-block">Il y a 5 minutes</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">Naissance</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Demande #DEM-000120 traitée
                                <small class="text-muted d-block">Il y a 1 heure</small>
                            </div>
                            <span class="badge bg-success rounded-pill">Complété</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-truck text-info me-2"></i>
                                Demande #DEM-000118 en livraison
                                <small class="text-muted d-block">Il y a 2 heures</small>
                            </div>
                            <span class="badge bg-info rounded-pill">Livraison</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-exclamation-circle text-danger me-2"></i>
                                Nouvelle réclamation #REC-0032
                                <small class="text-muted d-block">Il y a 3 heures</small>
                            </div>
                            <span class="badge bg-danger rounded-pill">Urgent</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-money-bill-wave text-success me-2"></i>
                                Paiement reçu pour #DEM-000117
                                <small class="text-muted d-block">Il y a 5 heures</small>
                            </div>
                            <span class="badge bg-success rounded-pill">5000 FCFA</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

</main>


<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Line Chart for Demandes
    const demandesCtx = document.getElementById('demandesChart').getContext('2d');
    const demandesChart = new Chart(demandesCtx, {
        type: 'line',
        data: {
            labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30'],
            datasets: [{
                    label: 'Naissance',
                    data: [12, 15, 8, 10, 14, 16, 12, 18, 15, 20, 22, 18, 20, 25, 22, 24, 26, 28, 25, 27, 30, 28, 32, 30, 35, 32, 34, 36, 38, 40],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Mariage',
                    data: [5, 7, 6, 8, 7, 9, 10, 8, 12, 10, 14, 12, 15, 13, 16, 15, 18, 16, 20, 18, 22, 20, 24, 22, 25, 24, 26, 25, 28, 30],
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Décès',
                    data: [3, 4, 5, 6, 5, 7, 6, 8, 7, 9, 8, 10, 9, 11, 10, 12, 11, 13, 12, 14, 13, 15, 14, 16, 15, 17, 16, 18, 17, 20],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Nombre de Demandes'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Jours'
                    }
                }
            }
        }
    });

    // Pie Chart for Actes
    const actesPieCtx = document.getElementById('actesPieChart').getContext('2d');
    const actesPieChart = new Chart(actesPieCtx, {
        type: 'pie',
        data: {
            labels: ['Naissance', 'Mariage', 'Décès'],
            datasets: [{
                data: [65, 25, 10],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(75, 192, 192, 0.7)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw + '%';
                        }
                    }
                }
            }
        }
    });
</script>
</main>


<style>
    

    .card-icon {
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    .stat-card {
        transition: transform 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }
</style>


<?php include 'partials/footer.php'; ?>