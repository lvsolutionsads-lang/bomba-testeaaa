<?php
/**
 * Proxy para trocar o authorization code por token no Kwai.
 * Coloque este arquivo na RAIZ do seu domínio (ex: public_html/kwai-token-proxy.php).
 *
 * O frontend fará POST para /kwai-token-proxy.php com JSON:
 * { "code": "...", "client_id": "...", "client_secret": "...", "redirect_uri": "..." }
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['code']) || empty($input['client_id']) || empty($input['client_secret']) || empty($input['redirect_uri'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields: code, client_id, client_secret, redirect_uri']);
    exit;
}

$postData = http_build_query([
    'grant_type'    => 'authorization_code',
    'code'          => trim($input['code']),
    'client_id'     => trim($input['client_id']),
    'client_secret' => trim($input['client_secret']),
    'redirect_uri'  => trim($input['redirect_uri']),
]);

$ch = curl_init('https://business.kwai.com/oauth/token');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $postData,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json',
    ],
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_SSL_VERIFYPEER => true,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error    = curl_error($ch);
curl_close($ch);

if ($error) {
    http_response_code(502);
    echo json_encode(['error' => 'Proxy error: ' . $error]);
    exit;
}

http_response_code($httpCode);
echo $response;
