<?php

require __DIR__ . '/../vendor/autoload.php';

/*
 * This a simple example for a complete process-implementation
 *  generating a screenshot
 *  querying the information
 *  receive a list of generated screenshots
 *
 */

use Alexschwarz89\Browserstack\Screenshots\Api;
use Alexschwarz89\Browserstack\Screenshots\Request;

const BROWSERSTACK_ACCOUNT   = '';
const BROWSERSTACK_PASSWORD  = '';

$api    = new ScreenshotsAPI( BROWSERSTACK_ACCOUNT, BROWSERSTACK_PASSWORD );

// Short-hand Notation
$request    = Request::build_request( 'http://www.example.org', 'Windows', '8.1', 'ie', '11.0' );

// Send the request
$response   = $api->send_request( $request );

// Query information about the newly created request
if ( $response->is_successful ) {
    // Wait until the request is finished
    do {
        // Query Job Status
        $status = $api->get_job_status( $response->job_ID );
        if ( $status->is_finished() ) {
            // When it's finished, print out the image URLs
            foreach ( $status->finished_screenshots as $screenshot ) {
                print $screenshot->image_url . "\n";
            }
            break;
        }
        // Wait five seconds
        sleep(5);

    } while (true);

} else {
    print 'Job creation failed. Reason: ' . $response->error_message . "\n";
}