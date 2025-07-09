<?php
require_once 'app/config/constants.php';
require_once 'app/config/database.php';
require_once 'app/helpers/alert_helper.php';
require_once 'app/helpers/alert_helper.php';
require_once 'app/Controllers/citoyen_controller.php';
require_once 'app/repositories/citoyen_repository.php';

$entity = 'demande';
$id = 1;
$pdo = $db;


switch ($entity) {
    case 'naissance':
        $title = "Détails de l'acte de naissance";
        $stmt = $pdo->prepare("SELECT * FROM actes_naissance WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $fields = [
            'numero_acte' => 'Numéro d\'acte',
            'nom' => 'Nom',
            'prenom' => 'Prénom',
            'date_naissance' => 'Date de naissance',
            'lieu_naissance' => 'Lieu de naissance',
            'nom_pere' => 'Nom du père',
            'nom_mere' => 'Nom de la mère',
            'date_enregistrement' => 'Date d\'enregistrement'
        ];
        break;
        
    case 'mariage':
        $title = "Détails de l'acte de mariage";
        $stmt = $pdo->prepare("SELECT * FROM actes_mariage WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $fields = [
            'numero_acte' => 'Numéro d\'acte',
            'nom_epoux' => 'Nom époux',
            'prenom_epoux' => 'Prénom époux',
            'nom_epouse' => 'Nom épouse',
            'prenom_epouse' => 'Prénom épouse',
            'date_mariage' => 'Date de mariage',
            'lieu_mariage' => 'Lieu de mariage',
            'temoin1' => 'Témoin 1',
            'temoin2' => 'Témoin 2'
        ];
        break;
        
    case 'citoyen':
        $title = "Détails du compte citoyen";
        $stmt = $pdo->prepare("SELECT * FROM citoyens WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $fields = [
            'nom' => 'Nom',
            'prenom' => 'Prénom',
            'email' => 'Email',
            'telephone' => 'Téléphone',
            'adresse' => 'Adresse',
            'code_postal' => 'Code postal',
            'ville' => 'Ville',
            'date_inscription' => 'Date d\'inscription'
        ];
        break;
        
    case 'admin':
        $title = "Détails de l'administrateur";
        $stmt = $pdo->prepare("SELECT id, nom, email, role, date_creation FROM administrateurs WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $fields = [
            'nom' => 'Nom complet',
            'email' => 'Email',
            'role' => 'Rôle',
            'date_creation' => 'Date de création'
        ];
        break;
        
    case 'demande':
        $title = "Détails de la demande";
        $stmt = $pdo->prepare("SELECT d.*, c.nom, c.prenom FROM demandes d JOIN citoyens c ON d.citoyen_id = c.id WHERE d.id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $fields = [
            'numero_demande' => 'Numéro de demande',
            'type' => 'Type',
            'nom' => 'Nom du demandeur',
            'prenom' => 'Prénom du demandeur',
            'date_demande' => 'Date de demande',
            'statut' => 'Statut',
            'commentaires' => 'Commentaires'
        ];
        break;
}



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'citoyen/includes/header.php'; ?>
    
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0"><?= htmlspecialchars($title) ?></h3>
                        <div>
                            <a href="edit.php?entity=<?= $entity ?>&id=<?= $id ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i> Modifier
                            </a>
                            <?php if ($entity === 'naissance' || $entity === 'mariage'): ?>
                                <a href="pdf.php?entity=<?= $entity ?>&id=<?= $id ?>" class="btn btn-success btn-sm ms-2">
                                    <i class="fas fa-file-pdf me-1"></i> PDF
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success m-3"><?= htmlspecialchars($success_message) ?></div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <dl class="row">
                            <?php foreach ($fields as $field => $label): ?>
                                <?php if (array_key_exists($field, $data)): ?>
                                    <dt class="col-sm-4"><?= htmlspecialchars($label) ?></dt>
                                    <dd class="col-sm-8">
                                        <?php 
                                            // Formatage spécial pour certains champs
                                            if (strpos($field, 'date') !== false && !empty($data[$field])) {
                                                echo htmlspecialchars(date('d/m/Y', strtotime($data[$field])));
                                            } elseif ($field === 'role') {
                                                $roles = [
                                                    'superadmin' => 'Super Admin',
                                                    'admin' => 'Admin',
                                                    'agent' => 'Agent'
                                                ];
                                                echo htmlspecialchars($roles[$data[$field]] ?? $data[$field]);
                                            } else {
                                                echo !empty($data[$field]) ? htmlspecialchars($data[$field]) : '<span class="text-muted">Non renseigné</span>';
                                            }
                                        ?>
                                    </dd>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </dl>
                        
                        <?php if ($entity === 'demande'): ?>
                            <div class="mt-4">
                                <h5>Changer le statut</h5>
                                <div class="btn-group">
                                    <form method="post" action="update_status.php" class="me-2">
                                        <input type="hidden" name="demande_id" value="<?= $id ?>">
                                        <input type="hidden" name="new_status" value="approuve">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check me-1"></i> Approuver
                                        </button>
                                    </form>
                                    
                                    <form method="post" action="update_status.php">
                                        <input type="hidden" name="demande_id" value="<?= $id ?>">
                                        <input type="hidden" name="new_status" value="rejete">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-times me-1"></i> Rejeter
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer text-end">
                        <a href="admin.php#<?= $entity ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <?php include 'citoyen/includes/footer.php'; ?>