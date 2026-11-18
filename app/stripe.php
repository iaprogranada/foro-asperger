<?php
/** Cliente mínimo de Stripe (sin librerías): Checkout, portal de facturación y firma de webhooks. */

function stripe_ready(): bool { return (string) cfg('stripe.secret_key', '') !== ''; }

function stripe_price(string $plan): string { return (string) cfg('stripe.prices.' . $plan, ''); }

function stripe_api(string $method, string $path, array $params = []): array
{
    $ch = curl_init('https://api.stripe.com/v1/' . $path);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD => cfg('stripe.secret_key') . ':',
        CURLOPT_TIMEOUT => 25,
        CURLOPT_CUSTOMREQUEST => $method,
    ]);
    if ($method === 'POST') curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    $res = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    $data = json_decode((string) $res, true);
    if ($res === false || !is_array($data) || $code >= 400) {
        throw new RuntimeException('Stripe: ' . ($data['error']['message'] ?? ($err ?: 'respuesta no válida (' . $code . ')')));
    }
    return $data;
}

/** Comprueba la cabecera Stripe-Signature de un webhook. */
function stripe_verify(string $payload, string $header, string $secret, int $tolerance = 300): bool
{
    $t = null; $sigs = [];
    foreach (explode(',', $header) as $part) {
        [$k, $v] = array_pad(explode('=', trim($part), 2), 2, '');
        if ($k === 't') $t = $v;
        if ($k === 'v1') $sigs[] = $v;
    }
    if ($secret === '' || !$t || !$sigs || abs(time() - (int) $t) > $tolerance) return false;
    $expected = hash_hmac('sha256', $t . '.' . $payload, $secret);
    foreach ($sigs as $s) if (hash_equals($expected, $s)) return true;
    return false;
}

/** Fin del periodo pagado de una suscripción (el campo cambió de sitio entre versiones de la API). */
function stripe_period_end(array $sub): ?string
{
    $ts = $sub['current_period_end'] ?? ($sub['items']['data'][0]['current_period_end'] ?? null);
    return $ts ? date('Y-m-d H:i:s', (int) $ts) : null;
}
