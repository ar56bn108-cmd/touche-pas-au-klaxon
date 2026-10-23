<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = require __DIR__ . '/../config/database.php';

require_once __DIR__ . '/../app/Models/Agence.php';
require_once __DIR__ . '/../app/Models/Trajet.php';
require_once __DIR__ . '/../app/Models/Reservation.php';

require_once __DIR__ . '/../app/Controllers/HomeController.php';
require_once __DIR__ . '/../app/Controllers/TrajetController.php';
require_once __DIR__ . '/../app/Controllers/TrajetDetailController.php';
require_once __DIR__ . '/../app/Controllers/ReservationController.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/TrajetCreationController.php';
require_once __DIR__ . '/../app/Controllers/MesTrajetsController.php';
require_once __DIR__ . '/../app/Controllers/SupprimerTrajetController.php';
require_once __DIR__ . '/../app/Controllers/ModifierTrajetController.php';
require_once __DIR__ . '/../app/Controllers/AdminController.php';
require_once __DIR__ . '/../app/Controllers/AgenceController.php';

$routes = require __DIR__ . '/../routes/web.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/') ?: '/';

if (in_array($path, ['/reservation', '/annuler-reservation', '/mes-reservations'], true)) {
    $controller = new \App\Controllers\ReservationController($pdo);
    if ($path === '/reservation') {
        $controller->creer();
    } elseif ($path === '/annuler-reservation') {
        $controller->annuler();
    } else {
        $controller->mesReservations();
    }
    exit;
}

if (isset($routes[$path])) {
    [$controllerClass, $method] = $routes[$path];

    $controller = new $controllerClass($pdo);
    $controller->$method();
} else {
    http_response_code(404);
    echo 'Page introuvable';
}
