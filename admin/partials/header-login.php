<?php
require_once __DIR__ . '/../../app/config/constants.php';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - <?php echo COMMUNE_NAME; ?></title>
    <meta content="" name="description">
    <link href="/etatcivil/assets/img/favicon.png" rel="icon">
    <!-- Bootstrap core CSS -->
    <link href="/etatcivil/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/etatcivil/assets/css/admin.css" rel="stylesheet">
     <link href="/etatcivil/assets/css/bootstrap-icons.css" rel="stylesheet">    
   
    <link href="/etatcivil/assets/css/fontawesome.all.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .login-container {
            max-width: 450px;
            width: 100%;
            margin: auto;
            padding: 2.5rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .login-subtitle {
            color: #6c757d;
            margin-bottom: 2rem;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .form-control {
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
        }
        .divider {
            margin: 1.5rem 0;
            border-top: 1px solid #dee2e6;
        }
        .login-btn {
            width: 100%;
            padding: 0.75rem;
            font-weight: 500;
        }
        .forgot-password {
            text-align: center;
            margin-top: 1rem;
        }
        .footer {
            margin-top: auto;
            padding: 1.5rem 0;
            text-align: center;
            font-size: 0.9rem;
            color: #6c757d;
        }
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .footer-links a {
            color: #6c757d;
            text-decoration: none;
        }
        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>