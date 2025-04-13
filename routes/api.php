<?php
require_once __DIR__ . '/AuthRoutes.php';
require_once __DIR__ . '/UserRoutes.php';
require_once __DIR__ . '/RoleRoutes.php';
require_once __DIR__ . '/BookRoutes.php';
require_once __DIR__ . '/FavoriteRoutes.php';
require_once __DIR__ . '/SavedRoutes.php';
require_once __DIR__ . '/TagRoutes.php';

function registerAllRoutes($router) {
    registerAuthRoutes($router);
    registerUserRoutes($router);
    registerRoleRoutes($router);
    registerBookRoutes($router);
    registerFavoriteRoutes($router);
    registerSavedRoutes($router);
    registerTagRoutes($router);
}