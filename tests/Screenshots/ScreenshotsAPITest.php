<?php

use \Alexschwarz89\Browserstack\Screenshots\ScreenshotsAPI;
use \Alexschwarz89\Browserstack\Screenshots\Request;

class ScreenshotsAPITest extends PHPUnit_Framework_TestCase
{

    /**
     * @var browserstack_API
     */
    protected static $browserstack_API;

    /**
     *
     */
    public static function setUpBeforeClass() {
        self::$browserstack_API = new ScreenshotsAPI( 'phpunit', 'phpunit' );
    }

    /**
     *
     */
    public function test_construct() {
        $this->assertInstanceOf( '\Alexschwarz89\Browserstack\Screenshots\Api', self::$browserstack_API );
    }

    /**
     *
     */
    public function test_get_browsers() {
        $response = self::$browserstack_API->getBrowsers();

        // Check if we have a valid JSON response.
        $this->assertInternalType( 'array', $response );
    }

    /**
     *
     */
    public function test_get_job_status() {
        $this->assertInstanceOf('Alexschwarz89\Browserstack\Screenshots\Response\ScreenshotsResponse', self::$browserstack_API->getJobStatus( '123' ) );
    }

    /**
     * @return bool
     */
    public function test_send_request() {
        $request = Request::build_request(
            'http://www.example.com',
            'Windows',
            '8.1',
            'ie',
            '11.0'
        );

        $this->assertInstanceOf( 'Alexschwarz89\Browserstack\Screenshots\Request', $request );

        $response = self::$browserstack_API->sendRequest( $request );

        $this->assertInstanceOf( 'Alexschwarz89\Browserstack\Screenshots\Response\ScreenshotsResponse', $response );

        return false;
    }

    /**
     *
     */
    public function test_is_browserstack_accessible() {
        $response = self::$browserstack_API->is_browserstack_accessible();

        $result = false;

        if ( is_array( $response ) && ( isset( $response['success'] ) || isset( $response['errors'] ) ) ) {
            $result = true;
        }

        $this->assertTrue( $result );
    }


}