<?php
require_once __DIR__ . '/../app/config/database.php';

echo "<h2>Test de connexion à la base de données et vérification des comptes admin</h2>";

try {
    // Test de connexion à la base
    echo "<p>✅ Connexion à la base de données réussie</p>";
    
    // Vérification des administrateurs
    $sql = "SELECT id, nom, prenom, email, role_id, statut FROM administrateurs WHERE statut = 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($admins) > 0) {
        echo "<h3>✅ Comptes administrateurs trouvés :</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Role ID</th></tr>";
        foreach ($admins as $admin) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($admin['id']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['nom']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['prenom']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['role_id']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p><strong>💡 Utilisez ces identifiants pour vous connecter avec le mot de passe : admin123</strong></p>";
    } else {
        echo "<p>❌ Aucun compte administrateur trouvé. Vous devez exécuter le fichier table_init.sql</p>";
        
        // Vérification si les tables existent
        $tables = ['administrateurs', 'role', 'citoyens'];
        foreach ($tables as $table) {
            $sql = "SHOW TABLES LIKE '$table'";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $exists = $stmt->fetch();
            if ($exists) {
                echo "<p>✅ Table '$table' existe</p>";
            } else {
                echo "<p>❌ Table '$table' n'existe pas</p>";
            }
        }
    }
    
    // Test du hash du mot de passe
    echo "<h3>Test du hash du mot de passe 'admin123' :</h3>";
    $test_password = 'admin123';
    $stored_hash = '$2y$10$elbV6WSAflrfFVgQ1QU4JOV4af0tALOtidDEhElV1yawNR8B5wuA.';
    
    if (password_verify($test_password, $stored_hash)) {
        echo "<p>✅ Le hash du mot de passe fonctionne correctement</p>";
    } else {
        echo "<p>❌ Problème avec le hash du mot de passe</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { margin: 10px 0; }
    th, td { padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style> 