<?php

class AlertHelper
{
    /**
     * Génère une alerte de succès.
     *
     * @param string $message Le message de succès.
     * @param string $hrefFermer Lien pour fermer l'alerte.
     * @return string Le HTML de l'alerte de succès.
     */
    public static function success(string $message, string $hrefFermer): string
    {
        return <<<HTML
        <div class="alert alert-success" role="alert">
            {$message} <a href="{$hrefFermer}" class="alert-link">Fermer</a>
        </div>
HTML;
    }

    /**
     * Génère une alerte d'erreur.
     *
     * @param string $message Le message d'erreur.
     * @return string Le HTML de l'alerte d'erreur.
     */
    public static function error(string $message): string
    {
        return <<<HTML
        <div class="alert alert-danger" role="alert">
            {$message}
        </div>
HTML;
    }

    /**
     * Génère une alerte pour une exception.
     *
     * @param string $message Le message d'exception.
     * @return string Le HTML de l'alerte d'exception.
     */
    public static function exception(string $message): string
    {
        return <<<HTML
        <div class="alert alert-warning" role="alert">
            {$message}
        </div>
HTML;
    }
}