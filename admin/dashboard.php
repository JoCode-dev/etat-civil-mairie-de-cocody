<?php
require_once __DIR__ . '/../app/config/constants.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/alert_helper.php';
require_once __DIR__ . '/../app/controllers/admin_controller.php';
require_once __DIR__ . '/../app/repositories/admin_repository.php';
require_once __DIR__ . '/../app/repositories/demande_repository.php';

AdminController::requirelogin();
$activeMenu = "dashbord"; 


$repository = new DemandeRepository($db);
$stat = [];
$list = [];
$statType = [];
$error = null;

try {
    $resultRequete = $repository->search('', 1, 5);
    $list = $resultRequete['data'];
    $stat = $repository->statistiques();
    $statType = $repository->statparTypeActe();
} catch (Exception $e) {
    $error = $e->getMessage();
   
}


function translateStatut($statut)
{
    static $translations = [
        'en_attente' => 'En attente',
        'en_traitement' => 'En traitement',
        'pret' => 'Prêt',
        'recupere' => 'Récupéré',
        'annule' => 'Annulé',
        'en_livraison' => 'En livraison',
        'livre' => 'Livré'
    ];
    return $translations[$statut] ?? $statut;
}


function getBadgeClass($statut)
{
    $classes = [
        'en_attente' => 'bg-primary',
        'en_traitement' => 'bg-warning',
        'pret' => 'bg-success',
        'recupere' => 'bg-secondary',
        'annule' => 'bg-danger',
        'en_livraison' => 'bg-info',
        'livre' => 'bg-success'
    ];
    return $classes[$statut] ?? 'bg-light text-dark';
}

include 'partials/header.php';
?>

<main class="main-content">
    <!-- Section Titre -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Tableau de Bord</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </div>

    <!-- Cartes de Statistiques -->
    <div class="row mb-4 g-3">
        <?php 
        // Configuration des cartes stats
        $statsCards = [
            [
                'title' => 'Demandes Total',
                'value' => $stat['total'] ?? 0,
                'daily' => $stat['du_jour']['total'] ?? 0,
                'icon' => 'fas fa-file-alt',
                'bg' => 'primary'
            ],
            [
                'title' => 'Demandes Traitées',
                'value' => $stat['pret'] ?? 0,
                'daily' => $stat['du_jour']['pret'] ?? 0,
                'icon' => 'fas fa-check-circle',
                'bg' => 'success'
            ],
            [
                'title' => 'En traitement',
                'value' => $stat['en_traitement'] ?? 0,
                'daily' => $stat['du_jour']['en_traitement'] ?? 0,
                'icon' => 'fas fa-clock',
                'bg' => 'warning'
            ],
            [
                'title' => 'Demandes Annulées',
                'value' => $stat['annule'] ?? 0,
                'daily' => $stat['du_jour']['annule'] ?? 0,
                'icon' => 'fas fa-exclamation-triangle',
                'bg' => 'danger'
            ]
        ];
        
        foreach ($statsCards as $card): ?>
        <div class="col-md-3">
            <div class="card text-white bg-<?= $card['bg'] ?> stat-card h-100">
                <div class="card-body d-flex align-items-center py-2 px-3">
                    <div class="me-2">
                        <i class="<?= $card['icon'] ?>" style="font-size: 2.2rem;" aria-hidden="true"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1"><?= $card['title'] ?></h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-text mb-1"><?= $card['value'] ?></h5>
                            <small class="card-text text-white-50">
                                <?= $card['daily'] ? '+' . $card['daily'] . '% aujourd\'hui' : 'N/A' ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Graphiques et Tableaux -->
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-list me-2" aria-hidden="true"></i>
                    Dernières Demandes
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Référence</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($list as $demande): ?>
                                <tr>
                                    <td><?= htmlspecialchars($demande->getReference()) ?></td>
                                    <td><?= htmlspecialchars($demande->getActeLibelle()) ?></td>
                                    <td>
                                        <span class="badge <?= getBadgeClass($demande->getStatut()) ?>">
                                            <?= translateStatut($demande->getStatut()) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4 h-100">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-chart-pie me-2" aria-hidden="true"></i>
                    Répartition des Types d'Actes
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="flex-grow-1">
                        <canvas id="actesPieChart" height="300" style="max-height: 500px;" aria-label="Répartition des types d'actes" role="img"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- JS Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Script du Graphique -->
<script>
    // Fonction de génération de couleurs avec contraste vérifié
    function generateColors(count) {
        const colors = [];
        const hueStep = 360 / Math.max(count, 1);
        const saturation = 70;
        const lightness = 60;

        for (let i = 0; i < count; i++) {
            const hue = (i * hueStep) % 360;
            colors.push(`hsla(${hue}, ${saturation}%, ${lightness}%, 0.7)`);
        }

        return colors;
    }

    // Initialisation du graphique
    function initPieChart() {
        const ctx = document.getElementById('actesPieChart');
        if (!ctx) return;

        const statTypeData = <?= json_encode($statType) ?>;
        const canvas = ctx.getContext('2d');
        
        if (!statTypeData?.length) {
            canvas.font = '16px Arial';
            canvas.textAlign = 'center';
            canvas.fillText('Aucune donnée disponible', ctx.width / 2, ctx.height / 2);
            return;
        }

        new Chart(canvas, {
            type: 'pie',
            data: {
                labels: statTypeData.map(item => item.libelle),
                datasets: [{
                    data: statTypeData.map(item => item.nombre_demandes),
                    backgroundColor: generateColors(statTypeData.length),
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percent = Math.round((context.raw / total) * 100);
                                return `${context.label}: ${context.raw} (${percent}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Lancement lorsque le DOM est prêt
    if (document.readyState === 'complete') {
        initPieChart();
    } else {
        window.addEventListener('load', initPieChart);
    }
</script>

<style>
    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .table-responsive {
        overflow-x: auto;
    }
    
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }
</style>

<?php include 'partials/footer.php'; ?>