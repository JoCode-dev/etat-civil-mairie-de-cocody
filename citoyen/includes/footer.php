 <?php
require_once __DIR__ . '/../../app/config/database.php';

// Récupérer la baseUrl si elle n'est pas déjà définie
if (!isset($baseUrl)) {
    $baseUrl = '/etatcivil/';
}

?>
 <footer>
        <div class="footer-grid">
            <div class="footer-col">
                <h3>Mairie de Cocody</h3>
                <p>Place de la Mairie<br>
                
                Tél. : 01 23 45 67 89<br>
                Email : contact@mairie-cocody.fr</p>
            </div>
            
            <div class="footer-col">
                <h3>Horaires d'ouverture</h3>
                <ul>
                    <li>Lundi : 9h-12h / 14h-17h</li>
                    <li>Mardi : 9h-12h / 14h-17h</li>
                    <li>Mercredi : 9h-12h</li>
                    <li>Jeudi : 9h-12h / 14h-17h</li>
                    <li>Vendredi : 9h-12h / 14h-16h</li>
                </ul>
            </div>
            
           
            
            <div class="footer-col">
                <h3>Réseaux sociaux</h3>
                <ul>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">Instagram</a></li>
                    <li><a href="#">YouTube</a></li>
                </ul>
            </div>
        </div>
        
        <div class="copyright">
            <p>&copy; <?php echo date('Y'); ?> Mairie de Cocody - Tous droits réservés</p>
        </div>
    </footer>

     <script src="<?= $baseUrl ?>assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>