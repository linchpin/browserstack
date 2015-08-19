<?php

require __DIR__ . '/../vendor/autoload.php';

/*
 * This an example of the more advanced way of building requests
 * 
 */

use Linchpin\Browserstack\Screenshots\ScreenshotsAPI;
use Linchpin\Browserstack\Screenshots\Request;

const BROWSERSTACK_ACCOUNT   = '';
const BROWSERSTACK_PASSWORD  = '';

$api    = new ScreenshotsAPI( BROWSERSTACK_ACCOUNT, BROWSERSTACK_PASSWORD );

$request               = new Request();
$request->url          = 'http://www.example.org';
$request->mac_res      = '1920x1080';
$request->win_res      = '1920x1080';
$request->quality      = 'Original';
$request->wait_time    = 10;
$request->orientation  = 'landscape';

$request->add_browser( 'ios', '8.0', 'Mobile Safari', NULL, 'iPhone 6' );
$request->add_browser( 'ios', '8.0', 'Mobile Safari', NULL, 'iPhone 6 Plus' );
$request->add_browser( 'Windows', 'XP', 'ie', '7.0' );

// Send the request
$api->send_request( $request );

// Output
var_dump($request);