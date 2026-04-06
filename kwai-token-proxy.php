<?php
/**
 * Proxy PHP para a API do Kwai Marketing
 * Recebe requisições do frontend e repassa para developers.kwai.com
 * Resolve o problema de CORS do navegador
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

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

if (!$input || empty($input['endpoint']) || empty($input['access_token'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields: endpoint, access_token']);
    exit;
}

$endpoint = $input['endpoint'];
$accessToken = trim($input['access_token']);
$body = isset($input['body']) ? $input['body'] : new stdClass();

// Validar que o endpoint começa com /rest/n/mapi/
if (strpos($endpoint, '/rest/n/mapi/') !== 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid endpoint. Must start with /rest/n/mapi/']);
    exit;
}

$url = 'https://developers.kwai.com' . $endpoint;

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($body),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Access-Token: ' . $accessToken,
    ],
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => true,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    http_response_code(502);
    echo json_encode(['error' => 'Proxy error: ' . $error]);
    exit;
}

http_response_code($httpCode);
echo $response;
