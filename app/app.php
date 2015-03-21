<?php

require_once __DIR__.'/../bootstrap.php';

use Landing\Controller\IndexController;
use Symfony\Component\HttpFoundation\Request;

/**
*
*    Routes
*
**/

// Landing Page Homepage
$app->get(
    '/',
    'Landing\Controller\IndexController::indexAction'
)->bind('index');

return $app;
