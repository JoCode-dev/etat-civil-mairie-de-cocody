<?php
class CitoyenController
{


    public static function isAuth(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['citoyen_id']);
    }

    public static function name():String{
       return self::isAuth()?$_SESSION['citoyen_name']:'';
    }
    public static function email():String{
       return self::isAuth()?$_SESSION['citoyen_email']:'';
    }

    public static function  login(PDO $pdo, String $email, String $password): string
    {
        $sql = "SELECT * FROM citoyens WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return "Aucun compte trouvé avec cette adresse email.";
        } else  if ($row && password_verify($password, $row['password_hash'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $citoyen = CitoyenModel::fromArray($row);
            $sql = "UPDATE citoyens SET last_login = NOW() WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $citoyen->getId()]);

            $_SESSION['citoyen_id'] = $citoyen->getId();
            $_SESSION['citoyen_name'] = $citoyen->getNomComplet();
            $_SESSION['citoyen_email'] = $citoyen->getEmail();
            $_SESSION['citoyen_adresse'] = $citoyen->getAdresse();
            
            header('Location: acte_demande.php');
            exit;
        } else {
            return "Mot de passe incorrect.";
        }
    }

    public static function register(PDO $db, CitoyenModel $citoyen, String $password)
    {

        $citoyen->setPasswordHash(password_hash($password, PASSWORD_BCRYPT));
        $sql = "
            INSERT INTO citoyens (
                nom, prenom, email, mobile, adresse, password_hash, statut, created_at, last_login
            ) VALUES (
                :nom, :prenom, :email, :mobile, :adresse, :password_hash, :statut, NOW(), NOW()
            )
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':nom' => $citoyen->getNom(),
            ':prenom' => $citoyen->getPrenom(),
            ':email' => $citoyen->getEmail(),
            ':mobile' => $citoyen->getMobile(),
            ':adresse' => $citoyen->getAdresse(),
            ':password_hash' => $citoyen->getPasswordHash(),
            ':statut' => true
        ]);
        $lastInserted = $db->lastInsertId();
        if ($lastInserted) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['citoyen_id'] = $lastInserted;
            $_SESSION['citoyen_name'] = $citoyen->getNomComplet();
            $_SESSION['citoyen_email'] = $citoyen->getEmail();
             $_SESSION['citoyen_adresse'] = $citoyen->getAdresse();
            header('Location: acte_demande.php');
            exit;
        }
    }

    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit();
    }

    public static function requirelogin()
    {
        if (!CitoyenController::isAuth()) {
            header('Location: login.php');
            exit();
        }
    }

    public static function redirectIfAuth()
    {
       if (CitoyenController::isAuth()) {
            header('Location: acte_demande.php');
            die();
        }
    }


    public static function authHtml()
    {
        $userName = self::name();
        $userEmail = self::email();
$userName = strlen($userName) > 8 ? substr($userName, 0, 8).'...' : $userName;
    
        return self::isAuth() ? <<<HTML
     <div class="user-profile dropdown">
         <div data-bs-toggle="dropdown" aria-expanded="false">
                <img src="/etatcivil/assets/img/user.png"  width="32" height="32" class="rounded-circle" alt="Profile">
                <span class="user-name me-2" style="color:white">{$userName}</span>
                
                <i class="bi bi-chevron-down" style="color:white"></i>
            </div>
         <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="/etatcivil/citoyen/demandes.php"><i class="bi bi-person me-2"></i>Profil</a></li>
                <li><a class="dropdown-item" href="/etatcivil/citoyen/demandes.php"><i class="bi bi-gear me-2"></i>Mes demandes</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="/etatcivil/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a></li>
            </ul>
        
    </div>

HTML : <<<HTML
     <a class="btn btn-outline-light" href="/etatcivil/login.php">Se connecter</a>
HTML;
    }
}
