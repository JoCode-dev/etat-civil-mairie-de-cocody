  

      <script src="/etatcivil/assets/js/bootstrap.bundle.min.js"></script>
 <script>
        // Toggle sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            
            // Sauvegarder l'état
            localStorage.setItem('sidebarCollapsed', 
                document.querySelector('.sidebar').classList.contains('collapsed'));
        });
        
        // Restaurer l'état au chargement
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            document.querySelector('.sidebar').classList.add('collapsed');
        }
    </script>
</body>
</html>