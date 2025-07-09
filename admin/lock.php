<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Screen Lock</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .lock-container {
            max-width: 400px;
            width: 100%;
            margin: auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .lock-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .lock-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .user-name {
            font-weight: 600;
            margin-bottom: 2rem;
            font-size: 1.2rem;
        }
        
        .form-label {
            font-weight: 500;
        }
        
        .footer {
            margin-top: auto;
            padding: 1rem 0;
            text-align: center;
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .footer-links a {
            color: #6c757d;
            text-decoration: none;
            margin: 0 0.5rem;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="lock-container">
            <div class="lock-header">
                <h1 class="lock-title">Locked</h1>
                <p>Hello Jane, enter your password to unlock the screen!</p>
                <div class="user-name">Jane Doe</div>
            </div>
            
            <form>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" placeholder="Enter password">
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Unlock</button>
                </div>
            </form>
        </div>
    </div>
    
    <footer class="footer">
        <div class="container">
            <div class="footer-links">
                <a href="#">Privacy</a>
                <a href="#">Terms</a>
                <span>© 2022 Sarvadhi</span>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>