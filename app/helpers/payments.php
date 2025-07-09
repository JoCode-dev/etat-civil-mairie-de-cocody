<?php
require_once __DIR__ . '/../config/constants.php';

class PaymentHelper {
    public static function calculerFrais($typeActe) {
        switch ($typeActe) {
            case 'naissance':
                return TAUX_FRAIS * 0.8; // 20% de réduction pour naissance
            case 'mariage':
                return TAUX_FRAIS * 1.2; // 20% supplémentaire pour mariage
            case 'deces':
                return 0; // Gratuit pour les actes de décès
            default:
                return TAUX_FRAIS;
        }
    }

    public static function initierPaiement($demandeId, $montant, $description) {
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
        
        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $description,
                        ],
                        'unit_amount' => $montant * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => APP_URL . '/compte/paiement/success?session_id={CHECKOUT_SESSION_ID}&demande_id=' . $demandeId,
                'cancel_url' => APP_URL . '/compte/paiement/cancel?demande_id=' . $demandeId,
                'metadata' => [
                    'demande_id' => $demandeId,
                    'type_acte' => $description
                ]
            ]);
            
            return $session->id;
        } catch (Exception $e) {
            error_log("Erreur Stripe: " . $e->getMessage());
            return false;
        }
    }

    public static function verifierPaiement($sessionId) {
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
        
        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            return $session->payment_status === 'paid';
        } catch (Exception $e) {
            error_log("Erreur vérification paiement: " . $e->getMessage());
            return false;
        }
    }
}
?>