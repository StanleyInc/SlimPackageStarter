<?php

session_cache_limiter(false);
session_start();

define('ROOT_PATH'  , __DIR__.'/../../');
define('VENDOR_PATH', __DIR__.'/../../vendor/');
define('APP_PATH'   , __DIR__.'/../../app/');
define('MODULE_PATH', __DIR__.'/../../app/modules/');
define('PUBLIC_PATH', __DIR__.'/../../');

require VENDOR_PATH.'autoload.php';
use SlimServices\ServiceManager;

/**
 * Load the configuration
 */
$config = array(
    'path.root'     => ROOT_PATH,
    'path.public'   => PUBLIC_PATH,
    'path.app'      => APP_PATH,
    'path.module'   => MODULE_PATH
);

foreach (glob(APP_PATH.'config/*.php') as $configFile) {
    require $configFile;
}

/** looping all custom use class in vendor/use folder */
foreach (glob(VENDOR_PATH.'custom_use/*.php') as $configFile) {
    require $configFile;
}
//global use phpmailer class for email sending
require VENDOR_PATH.'phpmailer/PHPMailerAutoload.php';

/** Merge cookies config to slim config */
if(isset($config['cookies'])){
    foreach($config['cookies'] as $configKey => $configVal){
        $config['slim']['cookies.'.$configKey] = $configVal;
    }
}

/**
 * Initialize Slim and SlimStarter application
 */
$app        = new \Slim\Slim($config['slim']);

//register service from service providers
$services = new ServiceManager($app);
$services->registerServices(array(
    'Illuminate\Filesystem\FilesystemServiceProvider',
    'Illuminate\Translation\TranslationServiceProvider',
    'Illuminate\Validation\ValidationServiceProvider'
));

$starter    = new \SlimStarter\Bootstrap($app);
$app->notFound(function () use ($app) {
    $app->render('404.html');
});

$starter->setConfig($config);

/**
 * if called from the install script, disable all hooks, middlewares, and database init
 */
if(!defined('INSTALL')){
    /** boot up SlimStarter */
    $starter->boot();

    /** Setting up Slim hooks and middleware */
    require APP_PATH.'bootstrap/app.php';

    /** registering modules */
    foreach (glob(APP_PATH.'modules/*') as $module) {
        $className = basename($module);
        $moduleBootstrap = "\\$className\\Initialize";

        $app->module->register(new $moduleBootstrap);
    }

    $app->module->boot();

    /** Start the route */
    require APP_PATH.'routes.php';
}else{
    /** disregard sentry configuration on install */
    $config['aliases']['Sentry'] = 'Cartalyst\Sentry\Facades\Native\Sentry';

    $starter->bootFacade($config['aliases']);
}

return $starter;