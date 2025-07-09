<?php
session_start();

// Vérification de l'authentification
/* if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
} */

// Connexion à la base de données (à adapter)
require_once 'config/db.php';

// Récupération du type d'entité et de l'ID
$entity = $_GET['entity'] ?? '';
$id = $_GET['id'] ?? 0;

// Vérification des paramètres
if (empty($entity) || !in_array($entity, ['naissance', 'mariage', 'citoyen', 'admin']) || $id <= 0) {
    header('Location: admin.php');
    exit;
}

// Récupération des données selon l'entité
$data = [];
$title = '';
$fields = [];

switch ($entity) {
    case 'naissance':
        $title = "Modifier un acte de naissance";
        $stmt = $pdo->prepare("SELECT * FROM actes_naissance WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $fields = [
            'numero_acte' => ['label' => 'Numéro d\'acte', 'type' => 'text', 'required' => true],
            'nom' => ['label' => 'Nom', 'type' => 'text', 'required' => true],
            'prenom' => ['label' => 'Prénom', 'type' => 'text', 'required' => true],
            'date_naissance' => ['label' => 'Date de naissance', 'type' => 'date', 'required' => true],
            'lieu_naissance' => ['label' => 'Lieu de naissance', 'type' => 'text', 'required' => true],
            'nom_pere' => ['label' => 'Nom du père', 'type' => 'text', 'required' => false],
            'nom_mere' => ['label' => 'Nom de la mère', 'type' => 'text', 'required' => false],
            'date_enregistrement' => ['label' => 'Date d\'enregistrement', 'type' => 'date', 'required' => true]
        ];
        break;
        
    case 'mariage':
        $title = "Modifier un acte de mariage";
        $stmt = $pdo->prepare("SELECT * FROM actes_mariage WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $fields = [
            'numero_acte' => ['label' => 'Numéro d\'acte', 'type' => 'text', 'required' => true],
            'nom_epoux' => ['label' => 'Nom époux', 'type' => 'text', 'required' => true],
            'prenom_epoux' => ['label' => 'Prénom époux', 'type' => 'text', 'required' => true],
            'nom_epouse' => ['label' => 'Nom épouse', 'type' => 'text', 'required' => true],
            'prenom_epouse' => ['label' => 'Prénom épouse', 'type' => 'text', 'required' => true],
            'date_mariage' => ['label' => 'Date de mariage', 'type' => 'date', 'required' => true],
            'lieu_mariage' => ['label' => 'Lieu de mariage', 'type' => 'text', 'required' => true],
            'temoin1' => ['label' => 'Témoin 1', 'type' => 'text', 'required' => false],
            'temoin2' => ['label' => 'Témoin 2', 'type' => 'text', 'required' => false]
        ];
        break;
        
    case 'citoyen':
        $title = "Modifier un compte citoyen";
        $stmt = $pdo->prepare("SELECT * FROM citoyens WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $fields = [
            'nom' => ['label' => 'Nom', 'type' => 'text', 'required' => true],
            'prenom' => ['label' => 'Prénom', 'type' => 'text', 'required' => true],
            'email' => ['label' => 'Email', 'type' => 'email', 'required' => true],
            'telephone' => ['label' => 'Téléphone', 'type' => 'tel', 'required' => false],
            'adresse' => ['label' => 'Adresse', 'type' => 'text', 'required' => false],
            'code_postal' => ['label' => 'Code postal', 'type' => 'text', 'required' => false],
            'ville' => ['label' => 'Ville', 'type' => 'text', 'required' => false]
        ];
        break;
        
    case 'admin':
        $title = "Modifier un administrateur";
        $stmt = $pdo->prepare("SELECT id, nom, email, role FROM administrateurs WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $fields = [
            'nom' => ['label' => 'Nom complet', 'type' => 'text', 'required' => true],
            'email' => ['label' => 'Email', 'type' => 'email', 'required' => true],
            'role' => [
                'label' => 'Rôle', 
                'type' => 'select', 
                'required' => true,
                'options' => [
                    'superadmin' => 'Super Admin',
                    'admin' => 'Admin',
                    'agent' => 'Agent'
                ]
            ]
        ];
        break;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updateData = [];
    
    foreach ($fields as $field => $config) {
        if (isset($_POST[$field])) {
            $updateData[$field] = htmlspecialchars(trim($_POST[$field]));
        }
    }
    
    try {
        // Construction de la requête SQL dynamique
        $setParts = [];
        $params = [];
        
        foreach ($updateData as $field => $value) {
            $setParts[] = "$field = ?";
            $params[] = $value;
        }
        
        $params[] = $id; // Pour le WHERE
        
        $table = match($entity) {
            'naissance' => 'actes_naissance',
            'mariage' => 'actes_mariage',
            'citoyen' => 'citoyens',
            'admin' => 'administrateurs'
        };
        
        $sql = "UPDATE $table SET " . implode(', ', $setParts) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $_SESSION['success_message'] = "Modification enregistrée avec succès";
        header("Location: details.php?entity=$entity&id=$id");
        exit;
        
    } catch (PDOException $e) {
        $error = "Erreur lors de la mise à jour : " . $e->getMessage();
    }
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
    <?php include 'partials/header.php'; ?>
    
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title"><?= htmlspecialchars($title) ?></h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <form method="post">
                            <?php foreach ($fields as $field => $config): ?>
                                <div class="mb-3">
                                    <label for="<?= $field ?>" class="form-label"><?= htmlspecialchars($config['label']) ?></label>
                                    
                                    <?php if ($config['type'] === 'select'): ?>
                                        <select class="form-select" id="<?= $field ?>" name="<?= $field ?>" <?= $config['required'] ? 'required' : '' ?>>
                                            <?php foreach ($config['options'] as $value => $label): ?>
                                                <option value="<?= htmlspecialchars($value) ?>" <?= ($data[$field] ?? '') === $value ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($label) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <input type="<?= $config['type'] ?>" class="form-control" id="<?= $field ?>" name="<?= $field ?>" 
                                               value="<?= htmlspecialchars($data[$field] ?? '') ?>" <?= $config['required'] ? 'required' : '' ?>>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="d-flex justify-content-between">
                                <a href="details.php?entity=<?= $entity ?>&id=<?= $id ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>