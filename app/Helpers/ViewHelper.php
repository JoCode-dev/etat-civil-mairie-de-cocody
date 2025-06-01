<?php
namespace App\Helpers;

class ViewHelper {
    public static function render(string $view, array $data = []): void {
        extract($data);
        require __DIR__ . "/../../views/$view.php";
    }

    public static function json(array $data, int $status = 200): void {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}