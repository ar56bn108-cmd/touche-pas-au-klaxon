<?php
declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\TrajetController;
use App\Controllers\TrajetDetailController;
use App\Controllers\AuthController;
use App\Controllers\TrajetCreationController;
use App\Controllers\MesTrajetsController;
use App\Controllers\ModifierTrajetController;

return [
    '/admin' => [\App\Controllers\AdminController::class, 'index'],
    '/admin/agences' => [\App\Controllers\AgenceController::class, 'index'],
    '/supprimer-trajet' => [\App\Controllers\SupprimerTrajetController::class, 'supprimer'],
    '/modifier-trajet' => [ModifierTrajetController::class, 'index'],
    '/mes-trajets' => [MesTrajetsController::class, 'index'],
    '/mes-reservations' => [\App\Controllers\ReservationController::class, 'mesReservations'],
    '/annuler-reservation' => [\App\Controllers\ReservationController::class, 'annuler'],
    '/' => [HomeController::class, 'index'],
    '/trajets' => [TrajetController::class, 'index'],
    '/trajet' => [TrajetDetailController::class, 'show'],
    '/proposer-trajet' => [TrajetCreationController::class, 'creer'],

    '/inscription' => [AuthController::class, 'inscription'],
    '/connexion' => [AuthController::class, 'connexion'],
    '/deconnexion' => [AuthController::class, 'deconnexion'],
];
