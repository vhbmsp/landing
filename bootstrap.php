<?php

require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();

/**
    CONFIGS
**/

$app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__.'/configs/config.yml')); // base config

$app['env'] = false;

// get Environment based on config environments and HTTP_HOST
foreach ($app['config']['environments'] as $envKey => $envHosts) {

    if (in_array($_SERVER['HTTP_HOST'], $envHosts)) {
        $app['env'] = $envKey;
    }
}

if (false === $app['env']) {
    die ('Unable to determinate HOST environment.');
}

// Load Environment Config if exists
if (true === file_exists(__DIR__.'/configs/config_'.$app['env'].'.yml')) {
    $app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__.'/configs/config_'.$app['env'].'.yml'));
}

/**
    END CONFIGS
**/

// set debug mode
$app['debug'] = $app['config']['debug'];

$app->register(new Silex\Provider\SessionServiceProvider());

// If not development reset session.storage.handler and use php.ini config (memcached)
if ($app['env'] != 'development') {
    $app['session.storage.handler'] = null;
}

/**
    URL generation from routes
**/
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

/**
    MYSQL
**/
$app->register(
    new Silex\Provider\DoctrineServiceProvider(),
    [
        'db.options' => [
            'driver' => 'pdo_mysql',
            'charset' => 'utf8',
            'host' => $app['config']['mysql']['host'],
            'dbname' => $app['config']['mysql']['dbname'],
            'user' => $app['config']['mysql']['user'],
            'password' => $app['config']['mysql']['password'],
        ],
    ]
);

/**
    TWIG
**/
$twigConfig = [
        'twig.path' => [
                        __DIR__.'/src/Landing/Views',
                    ],
        'twig.options' => [
                    'cache' => __DIR__.'/cache',
                    'debug' => false,
                    'auto_reload' => false,
                ]
];


switch ($app['env']) {
    case 'develop':
    case 'staging':
        $twigConfig['twig.options']['cache'] = false; // cache is a path; comment all line to use cache
        $twigConfig['twig.options']['debug'] = true;
        $twigConfig['twig.options']['auto_reload'] = true;
        break;
}

$app->register(
    new Silex\Provider\TwigServiceProvider(),
    $twigConfig
);


// Variables Passed to Twig:
$app['twig']->addGlobal('session', $app['session']);
$app['twig']->addGlobal('env', $app['env']);
$app['twig']->addGlobal('base_url', $app['config']['base_url']);

/**
    LEGACY SESSION DATA
**/
session_start();
$app['twig']->addGlobal('_SESSION', $_SESSION);

// Create Twig Filter
$concat123 = new \Twig_Filter_Function(
    function ($string) {

        return $string.'123';
    }
);

$app['twig']->addFilter('concat123', $concat123);

/**
    Memcache Configuration
**/
$app->register(new KuiKui\MemcacheServiceProvider\ServiceProvider());
$app['memcache.class'] = '\Memcached';
$app['memcache.default_duration'] = 300; // default TTL
$app['memcache.connections'] = array(
    array('127.0.0.1', 11211),
);
