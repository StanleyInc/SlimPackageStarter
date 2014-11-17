<?php
if (version_compare(phpversion(), '5.4', '<')) {
    // php version isn't high enough
    echo 'Please upgrade php version to at least 5.4 version';
    exit();
}
$app = require 'app/bootstrap/start.php';
$app->run();