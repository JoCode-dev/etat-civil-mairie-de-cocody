<?php
require_once __DIR__ . '/../config/constants.php';

/**
 * Envoie un email de vérification de compte
 * 
 * @param string $email Email du destinataire
 * @param string $nom Nom du destinataire
 * @param string $verificationLink Lien de vérification
 * @return bool True si l'email a été envoyé avec succès
 */
function envoyerEmailVerification($email, $nom, $verificationLink) {
    $sujet = "Vérification de votre email - Mairie de [Votre Ville]";
    
    $contenu = <<<HTML
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #FF6B35; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { padding: 20px; background-color: #f9f9f9; border-left: 1px solid #ddd; border-right: 1px solid #ddd; }
            .footer { padding: 15px; text-align: center; font-size: 0.9em; color: #777; border-radius: 0 0 8px 8px; border: 1px solid #ddd; border-top: none; }
            .btn { display: inline-block; background-color: #4CB944; color: white; padding: 12px 25px; text-decoration: none; border-radius: 50px; font-weight: bold; margin: 15px 0; }
            .signature { margin-top: 20px; font-style: italic; }
        </style>
    </head>
    <body>
        <div class="header">
            <h2>Mairie de [Votre Ville]</h2>
            <p>Service d'État Civil</p>
        </div>
        
        <div class="content">
            <p>Bonjour $nom,</p>
            <p>Merci de vous être inscrit sur le portail de la mairie de [Votre Ville]. Pour activer votre compte, veuillez cliquer sur le bouton ci-dessous :</p>
            
            <p style="text-align: center;">
                <a href="$verificationLink" class="btn">Vérifier mon email</a>
            </p>
            
            <p>Si vous n'avez pas créé de compte sur notre site, veuillez ignorer cet email.</p>
            
            <div class="signature">
                <p>Cordialement,</p>
                <p>Le service d'État Civil<br>Mairie de [Votre Ville]</p>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; <?= date('Y') ?> Mairie de [Votre Ville] - Tous droits réservés</p>
            <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
        </div>
    </body>
    </html>
HTML;

    return envoyerEmail($email, $sujet, $contenu);
}

/**
 * Envoie un email de notification de demande traitée
 * 
 * @param string $email Email du destinataire
 * @param string $nom Nom du destinataire
 * @param string $typeActe Type d'acte (naissance, mariage, décès)
 * @param string $lienTelechargement Lien pour télécharger l'acte
 * @return bool True si l'email a été envoyé avec succès
 */
function envoyerNotificationDemandeTraitee($email, $nom, $typeActe, $lienTelechargement) {
    $typesActes = [
        'naissance' => 'd\'un acte de naissance',
        'mariage' => 'd\'un acte de mariage',
        'deces' => 'd\'un acte de décès'
    ];
    
    $libelleActe = $typesActes[$typeActe] ?? 'd\'un acte d\'état civil';
    
    $sujet = "Votre demande $libelleActe est prête - Mairie de [Votre Ville]";
    
    $contenu = <<<HTML
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #FF6B35; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { padding: 20px; background-color: #f9f9f9; border-left: 1px solid #ddd; border-right: 1px solid #ddd; }
            .footer { padding: 15px; text-align: center; font-size: 0.9em; color: #777; border-radius: 0 0 8px 8px; border: 1px solid #ddd; border-top: none; }
            .btn { display: inline-block; background-color: #4CB944; color: white; padding: 12px 25px; text-decoration: none; border-radius: 50px; font-weight: bold; margin: 15px 0; }
            .signature { margin-top: 20px; font-style: italic; }
            .info-box { background-color: #e8f4f8; padding: 15px; border-left: 4px solid #3498db; margin: 15px 0; }
        </style>
    </head>
    <body>
        <div class="header">
            <h2>Mairie de [Votre Ville]</h2>
            <p>Service d'État Civil</p>
        </div>
        
        <div class="content">
            <p>Bonjour $nom,</p>
            <p>Votre demande $libelleActe a été traitée avec succès par nos services.</p>
            
            <div class="info-box">
                <p>Vous pouvez maintenant télécharger votre document en cliquant sur le bouton ci-dessous :</p>
                <p style="text-align: center;">
                    <a href="$lienTelechargement" class="btn">Télécharger mon acte</a>
                </p>
                <p><small>Ce lien est valable pendant 30 jours. Après cette date, vous devrez faire une nouvelle demande.</small></p>
            </div>
            
            <p>Si vous rencontrez des difficultés ou si vous avez des questions, n'hésitez pas à répondre à cet email.</p>
            
            <div class="signature">
                <p>Cordialement,</p>
                <p>Le service d'État Civil<br>Mairie de [Votre Ville]</p>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; <?= date('Y') ?> Mairie de [Votre Ville] - Tous droits réservés</p>
        </div>
    </body>
    </html>
HTML;

    return envoyerEmail($email, $sujet, $contenu);
}

/**
 * Fonction générique pour envoyer des emails
 * 
 * @param string $destinataire Email du destinataire
 * @param string $sujet Sujet de l'email
 * @param string $contenu Contenu HTML de l'email
 * @return bool True si l'email a été envoyé avec succès
 */
function envoyerEmail($destinataire, $sujet, $contenu) {
    $headers = "From: " . EMAIL_FROM . "\r\n";
    $headers .= "Reply-To: " . EMAIL_ADMIN . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // En production, utiliser une vraie solution d'envoi d'emails
    if (APP_ENV === 'production') {
        // Configuration pour un vrai serveur SMTP
        // return envoyerEmailSMTP($destinataire, $sujet, $contenu);
        return mail($destinataire, $sujet, $contenu, $headers);
    } else {
        // En développement, logger l'email au lieu de l'envoyer
        $logMessage = sprintf(
            "[EMAIL SIMULE] Dest: %s | Sujet: %s\nContenu: %s\n\n",
            $destinataire,
            $sujet,
            strip_tags($contenu)
        );
        
        file_put_contents(__DIR__ . '/../logs/emails.log', $logMessage, FILE_APPEND);
        return true;
    }
}

/**
 * Envoi d'email via SMTP (exemple avec PHPMailer)
 * 
 * @param string $destinataire Email du destinataire
 * @param string $sujet Sujet de l'email
 * @param string $contenu Contenu HTML de l'email
 * @return bool True si l'email a été envoyé avec succès
 */
function envoyerEmailSMTP($destinataire, $sujet, $contenu) {
    require_once __DIR__ . '/../vendor/autoload.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Configuration SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.votreserveur.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'votre@email.com';
        $mail->Password = 'votremotdepasse';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Destinataires
        $mail->setFrom(EMAIL_FROM, 'Mairie de [Votre Ville]');
        $mail->addAddress($destinataire);
        $mail->addReplyTo(EMAIL_ADMIN, 'Service État Civil');
        
        // Contenu
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body = $contenu;
        $mail->AltBody = strip_tags($contenu);
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erreur d'envoi d'email: " . $mail->ErrorInfo);
        return false;
    }
}