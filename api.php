<?php
// ── CONFIG ──────────────────────────────────────────
define('ADMIN_PASSWORD', 'loralouet2026'); // ← change ce mot de passe !
define('PLACES_FILE', __DIR__ . '/places.json');
// ────────────────────────────────────────────────────

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$method = $_SERVER['REQUEST_METHOD'];

// ── GET : retourner le nombre de places ──
if ($method === 'GET') {
    if (!file_exists(PLACES_FILE)) {
        echo json_encode(['places' => 0]);
        exit;
    }
    echo file_get_contents(PLACES_FILE);
    exit;
}

// ── POST : modifier le nombre de places ──
if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);

    // Vérification mot de passe
    if (!isset($body['password']) || $body['password'] !== ADMIN_PASSWORD) {
        http_response_code(401);
        echo json_encode(['error' => 'Mot de passe incorrect.']);
        exit;
    }

    // Vérification valeur
    if (!isset($body['places']) || !is_numeric($body['places'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Valeur invalide.']);
        exit;
    }

    $places = max(0, min(8, intval($body['places']))); // Entre 0 et 8
    file_put_contents(PLACES_FILE, json_encode(['places' => $places]));
    echo json_encode(['success' => true, 'places' => $places]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Méthode non autorisée.']);
