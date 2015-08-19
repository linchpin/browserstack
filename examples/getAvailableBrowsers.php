<?php

require __DIR__ . '/../vendor/autoload.php';

/*
 * This a simple example how to get a list of browsers currently supported by Browserstack
 * The given credentials are actually working for that method.
 *
 */

use Linchpin\Browserstack\Screenshots\ScreenshotsAPI;

$api            = new ScreenshotsAPI( '', '' );
$browserList    = $api->get_browsers();

var_dump( $browserList );