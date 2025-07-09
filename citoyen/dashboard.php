/** 
 * resources/views/citoyen/dashboard.php
 * Tableau de bord du citoyen
 */
$pageTitle = "Tableau de bord";
$activePage = "dashboard";
include '../partials/header-citoyen.php';
?>

<div class="dashboard-container">
    <div class="welcome-banner">
        <h1>Bonjour, <?= htmlspecialchars($user['prenom']) ?> <?= htmlspecialchars($user['nom']) ?></h1>
        <p>Votre espace personnel pour gérer vos démarches d'état civil</p>
    </div>

        <!-- Section Statistiques -->
        <section class="stats-section">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="icon-file-text"></i>
                </div>
                <div class="stat-content">
                    <h3>Demandes en cours</h3>
                    <span class="stat-value"><?= $stats['en_cours'] ?></span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="icon-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Demandes traitées</h3>
                    <span class="stat-value"><?= $stats['terminees'] ?></span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="icon-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>En attente</h3>
                    <span class="stat-value"><?= $stats['en_attente'] ?></span>
                </div>
            </div>
        </section>

        <!-- Actions rapides -->
        <section class="quick-actions">
            <h2>Actions rapides</h2>
            <div class="action-grid">
                <a href="/demandes/nouvelle" class="action-card">
                    <i class="icon-plus"></i>
                    <span>Nouvelle demande</span>
                </a>
                <a href="/profile" class="action-card">
                    <i class="icon-user"></i>
                    <span>Mon profil</span>
                </a>
                <a href="/documents" class="action-card">
                    <i class="icon-folder"></i>
                    <span>Mes documents</span>
                </a>
            </div>
        </section>

        <!-- Dernières demandes -->
        <section class="recent-requests">
            <div class="section-header">
                <h2>Vos dernières demandes</h2>
                <a href="/demandes" class="view-all">Voir tout</a>
            </div>
            
            <div class="requests-table">
                <table>
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lastRequests as $demande): ?>
                        <tr>
                            <td>DEM-<?= str_pad($demande['id'], 6, '0', STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars($demande['type_acte']) ?></td>
                            <td><?= date('d/m/Y', strtotime($demande['created_at'])) ?></td>
                            <td>
                                <span class="status-badge <?= str_replace('_', '-', $demande['statut']) ?>">
                                    <?= $this->getStatusLabel($demande['statut']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="/demandes/<?= $demande['id'] ?>" class="btn-detail">
                                    <i class="icon-eye"></i> Détails
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Statistiques graphiques -->
        <section class="charts-section">
            <div class="chart-container">
                <canvas id="requestsChart"></canvas>
            </div>
        </section>
</div>
<script>
        // Graphique des demandes
        const ctx = document.getElementById('requestsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Naissance', 'Mariage', 'Décès', 'Autre'],
                datasets: [{
                    label: 'Vos demandes par type',
                    data: [
                        <?= $stats['by_type']['naissance'] ?? 0 ?>,
                        <?= $stats['by_type']['mariage'] ?? 0 ?>,
                        <?= $stats['by_type']['deces'] ?? 0 ?>,
                        <?= $stats['by_type']['autre'] ?? 0 ?>
                    ],
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a',
                        '#e74a3b',
                        '#f6c23e'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>

<?php include '../partials/footer-citoyen.php'; ?>