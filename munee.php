<?php

define('ROOT_PATH'  , __DIR__.'/');
define('VENDOR_PATH', __DIR__.'/vendor/');
define('APP_PATH'   , __DIR__.'/app/');


//for minify use
define('WEBROOT', __DIR__.'/');
define('MUNEE_CACHE', APP_PATH.'storage/minify');


require VENDOR_PATH.'autoload.php';

echo \Munee\Dispatcher::run(new \Munee\Request(
    array('css' => array('lessifyAllCss' => true),
        'image' => array('checkReferrer' => false),
        'javascript' => array('packer' => array('fastDecode' => false)
            ))), array('setHeaders' => true));