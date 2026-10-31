<?php

declare(strict_types=1);

/**
 * Etiqueta corta para clientes.metodo_pago a partir del PaymentIntent de Stripe.
 */
function gp_pago_etiqueta_desde_intent(object $intent): string
{
    $pm = $intent->payment_method ?? null;
    if (!is_object($pm)) {
        return gp_pago_etiqueta_metodo('');
    }

    return gp_pago_etiqueta_desde_metodo($pm);
}

function gp_pago_etiqueta_desde_metodo(object $pm): string
{
    $type = '';
    $brand = null;
    $wallet = null;

    if (is_object($pm)) {
        $type = strtolower(trim((string) ($pm->type ?? '')));
        if ($type === 'card' && isset($pm->card)) {
            $brand = isset($pm->card->brand) ? (string) $pm->card->brand : null;
            $wallet = isset($pm->card->wallet->type) ? (string) $pm->card->wallet->type : null;
        }
    }

    return gp_pago_etiqueta_metodo($type, $brand, $wallet);
}

function gp_pago_etiqueta_metodo(string $type, ?string $brand = null, ?string $wallet = null): string
{
    $type = strtolower(trim($type));
    $wallet = strtolower(trim((string) $wallet));
    $brand = strtolower(trim((string) $brand));

    if ($wallet === 'apple_pay') {
        return 'Apple Pay';
    }
    if ($wallet === 'google_pay') {
        return 'Google Pay';
    }

    $nombres = [
        'card' => 'Tarjeta',
        'sepa_debit' => 'SEPA',
        'paypal' => 'PayPal',
        'link' => 'Link',
        'klarna' => 'Klarna',
        'bizum' => 'Bizum',
        'bancontact' => 'Bancontact',
        'ideal' => 'iDEAL',
        'giropay' => 'Giropay',
        'sofort' => 'Sofort',
        'eps' => 'EPS',
        'p24' => 'Przelewy24',
        'customer_balance' => 'Transferencia',
    ];

    if ($type === 'card') {
        $marcas = [
            'visa' => 'Visa',
            'mastercard' => 'Mastercard',
            'amex' => 'American Express',
            'discover' => 'Discover',
            'diners' => 'Diners',
            'jcb' => 'JCB',
            'unionpay' => 'UnionPay',
        ];
        if (isset($marcas[$brand])) {
            return 'Tarjeta ' . $marcas[$brand];
        }

        return 'Tarjeta';
    }

    if (isset($nombres[$type])) {
        return $nombres[$type];
    }

    if ($type === '') {
        return 'Tarjeta';
    }

    $legible = ucfirst(str_replace('_', ' ', $type));

    return substr($legible, 0, 50);
}
