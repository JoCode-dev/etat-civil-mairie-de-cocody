<?php
class AdminController
{


    public static function isAuth(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['admin_id']) && isset($_SESSION['admin_roleid']);
    }


    public static function  login(PDO $pdo, String $email, String $password):bool
    {
        $sql = "SELECT * FROM administrateurs WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && password_verify($password, $row['password_hash'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $admin = AdminModel::fromArray($row);
            $sql = "UPDATE administrateurs SET last_login = NOW() WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $admin->getId()]);

            $_SESSION['admin_id'] = $admin->getId();
            $_SESSION['admin_name'] = $admin->getNomComplet();
            $_SESSION['admin_email'] = $admin->getEmail();
            $_SESSION['admin_roleid'] = $admin->getRoleId();
            header('Location: /admin/dashboard.php');
            exit;
        }
        return false;
    }

     public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: /admin.php');
        exit();
    }

    public static function requirelogin()
    {
        if (!self::isAuth()) {
            header('Location: /admin/login');
            exit();
        }
    }

    public static function redirectIfAuth()
    {
       if (self::isAuth()) {
            header('Location: /admin/demandes/list.php');
            die();
        }
    }

  public static function checkAndRedirectPermission($menuId, $redirectUrl = '/admin/acces_refuse') {
    $roleId = $_SESSION['admin_roleid'];
    // Tableau des permissions (doit être cohérent avec votre configuration)
    static $permissions = [
        1 => ['dashboard', 'demandes', 'traitement', 'recuperation', 'admins', 'roles', 'citoyens', 'naissances', 'mariages', 'deces', 'type_actes'],
        2 => [ 'demandes', 'traitement', 'recuperation', 'citoyens', 'naissances', 'mariages', 'deces'],
        3 => [ 'demandes', 'traitement', 'recuperation', 'naissances', 'mariages', 'deces', 'type_actes'],
        4 => [ 'demandes', 'traitement', 'naissances', 'mariages', 'deces'],
        5 => [ 'recuperation', 'naissances', 'mariages', 'deces'],
        6 => [ 'demandes'],
        7 => [ 'naissances', 'mariages', 'deces'],
        8 => [ 'deces'],
        9 => [ 'naissances', 'mariages', 'deces'],
        10 => []
    ];

    if (!isset($permissions[$roleId])) {
        $_SESSION['flash_error'] = "Votre rôle n'a pas été reconnu";
        header("Location: ".$redirectUrl);
        exit();
    }

    if (!in_array($menuId, $permissions[$roleId])) {
        $_SESSION['flash_error'] = "Vous n'avez pas l'autorisation d'accéder à cette ressource";
        header("Location: ".$redirectUrl);
        exit();
    }

    return true;
}

}
