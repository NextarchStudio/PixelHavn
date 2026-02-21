<?php

declare(strict_types=1);

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'AuthController::loginForm');

$routes->get('login', 'AuthController::loginForm');
$routes->post('login', 'AuthController::login');
$routes->get('register', 'AuthController::registerForm');
$routes->post('register', 'AuthController::register');
$routes->get('logout', 'AuthController::logout', ['filter' => 'auth']);
$routes->get('auth/change-password', 'AuthController::changePasswordForm', ['filter' => 'auth']);
$routes->post('auth/change-password', 'AuthController::changePassword', ['filter' => 'auth']);

$routes->get('auth/passkey-login', 'AuthController::passkeyLoginForm');
$routes->post('auth/passkey/options', 'AuthController::passkeyAssertionOptions');
$routes->post('auth/passkey/verify', 'AuthController::passkeyVerifyLogin');

$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes): void {
    $routes->get('dashboard', 'DashboardController::index');

    $routes->get('passkeys', 'PasskeyController::index');
    $routes->post('passkeys/options', 'PasskeyController::registrationOptions');
    $routes->post('passkeys/register', 'PasskeyController::registerCredential');
    $routes->post('passkeys/(:num)/delete', 'PasskeyController::delete/$1');

    $routes->get('rooms', 'RoomController::index');
    $routes->get('rooms/(:num)', 'RoomController::view/$1');

    $routes->get('inventory', 'InventoryController::index');
    $routes->post('inventory/place', 'InventoryController::place');
});

$routes->group('admin', ['filter' => 'auth,role:Developer,Chief,Accounting,Office'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Admin\\AdminController::index');
    $routes->post('settings', 'Admin\\AdminController::updateSettings', ['filter' => 'role:Developer,Chief']);

    $routes->get('users', 'Admin\\AdminController::users');
    $routes->post('users/(:num)/role', 'Admin\\AdminController::assignRole/$1', ['filter' => 'role:Developer,Chief']);
    $routes->post('users/(:num)/ban', 'Admin\\AdminController::toggleBan/$1', ['filter' => 'role:Developer,Chief']);

    $routes->get('rooms', 'Admin\\AdminController::rooms', ['filter' => 'role:Developer,Chief']);
    $routes->post('rooms', 'Admin\\AdminController::createRoom', ['filter' => 'role:Developer,Chief']);

    $routes->get('items', 'Admin\\AdminController::items', ['filter' => 'role:Developer,Chief']);
    $routes->post('items', 'Admin\\AdminController::createItem', ['filter' => 'role:Developer,Chief']);
});

$routes->group('api', ['filter' => 'auth'], static function (RouteCollection $routes): void {
    $routes->post('realtime/auth', 'Api\\RealtimeController::authToken');
    $routes->get('rooms/(:num)', 'Api\\RoomApiController::show/$1');
    $routes->get('rooms/(:num)/placed-items', 'Api\\RoomApiController::placedItems/$1');
    $routes->post('rooms/(:num)/place-item', 'Api\\RoomApiController::placeItem/$1');
});

$routes->post('api/realtime/event', 'Api\\RealtimeController::ingestEvent', ['filter' => 'realtime-ingest']);
