<?php

class FormItemHelper
{
    /**
     * Génère un champ input HTML.
     */
    public static function input(string $key, string $label, bool $required = true, string $value = '', string $type = 'text', string $error = '', bool $disabled = false): string
    {
        $requiredHtml = $required ? 'required' : '';
        $disabledHtml = $disabled ? 'disabled' : '';
        $errorFeedback = self::getErrorFeedback($error);
        return <<<HTML
        <div class="form-group mb-2">
            <label for="field{$key}">{$label}</label>
            <input type="{$type}" id="field{$key}" class="form-control" name="{$key}" value="{$value}" {$requiredHtml} {$disabledHtml} />
            {$errorFeedback}
        </div>
HTML;
    }

    /**
     * Génère un champ textarea HTML.
     */
    public static function textarea(string $key, string $label, bool $required = true, string $value = '', string $error = '', int $rows = 3): string
    {
        $requiredHtml = $required ? 'required' : '';
        $errorFeedback = self::getErrorFeedback($error);
        return <<<HTML
        <div class="form-group mb-2">
            <label for="field{$key}">{$label}</label>
            <textarea id="field{$key}" class="form-control" name="{$key}" rows="{$rows}" {$requiredHtml}>{$value}</textarea>
            {$errorFeedback}
        </div>
HTML;
    }

    /**
     * Génère un champ select HTML.
     */
    public static function select(string $key, string $label, array $options = [], bool $required = true, string $value = '', string $error = ''): string
    {
        $optionsHtml = '';
        foreach ($options as $optionValue => $optionLabel) {
            $selected = $value == $optionValue ? 'selected' : '';
            $optionsHtml .= "<option value=\"{$optionValue}\" {$selected}>{$optionLabel}</option>";
        }
        $requiredHtml = $required ? 'required' : '';
        $errorFeedback = self::getErrorFeedback($error);
        return <<<HTML
        <div class="form-group mb-2">
            <label for="field{$key}">{$label}</label>
            <select id="field{$key}" class="form-control" name="{$key}" {$requiredHtml}>
                {$optionsHtml}
            </select>
            {$errorFeedback}
        </div>
HTML;
    }

    /**
     * Génère un champ input de type fichier HTML.
     */
    public static function inputFile(string $key, string $label, bool $required = true, string $error = ''): string
    {
        $requiredHtml = $required ? 'required' : '';
        $errorFeedback = self::getErrorFeedback($error);
        return <<<HTML
        <div class="form-group">
            <label for="field{$key}">{$label}</label>
            <input type="file" id="field{$key}" class="form-control" name="{$key}" accept="image/*" {$requiredHtml} />
            {$errorFeedback}
        </div>
HTML;
    }

    /**
     * Génère le feedback d'erreur pour un champ.
     */
    public static function getErrorFeedback(string $error): string
    {
        if (!empty($error)) {
            return '<div class="invalid-feedback">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</div>';
        }
        return '';
    }
}