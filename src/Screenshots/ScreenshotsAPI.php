<?php
/**
 * Forked from https://github.com/alexschwarz89/browserstack
 */

namespace Linchpin\Browserstack\Screenshots;

use Linchpin\Browserstack\Screenshots\Response\Base;
use Linchpin\Browserstack\Screenshots\Response\ScreenshotsResponse;
use tzfrs\Util\Curl;

/**
 * Class ScreenshotsAPI
 * @package Linchpin\Browserstack\Screenshots
 */
class ScreenshotsAPI {

	/**
	 * The BASE URL to the Browserstack Screenshots API where all endpoints are appended
	 */
	const API_BASE_URL = 'http://www.browserstack.com/screenshots';

	/**
	 * SimpleCurl Instance
	 *
	 * @var SimpleCurl
	 */
	protected $curl;

	/**
	 * The HTTP Headers used for the requests
	 *
	 * @var array
	 */
	protected $headers = array(
		'Content-type: application/json',
		'Accept: application/json',
	);

	/**
	 * If set to true, will output additional debug information
	 * to read in the CLI
	 *
	 * @var bool
	 */
	protected $debug = false;

	/**
	 * Browserstack Username
	 * @var string
	 */
	private $username;

	/**
	 * Browserstack Password
	 * @var string
	 */
	private $password;

	/**
	 * Constructor with Browserstack Username and Password
	 *
	 * @param string $username Browserstack Username.
	 * @param string $password Browserstack Password.
	 */
	public function __construct( $username, $password ) {
		$this->username = $username;
		$this->password = $password;

		$this->init();
	}

	/**
	 * This is actually needed to reset the cURL instance used inside SimpleCurl
	 */
	protected function init() {
		$this->curl = new Curl();
		$this->set_credentials( $this->username, $this->password );
	}

	/**
	 * Directly sets HTTP Authentication in cURL instance
	 *
	 * @param string $username Username.
	 * @param string $password Password.
	 */
	protected function set_credentials( $username, $password ) {
		$this->curl->setUserPwd( $username, $password );
	}

	/**
	 * Returns an Array of valid browser/OS combinations that can be used
	 * for requests. This request actually doesn't need Authorization.
	 *
	 * @return mixed
	 */
	public function get_browsers() {
		$res = new Response();
		$res->set_api_response( $this->_request( 'browsers.json' ) );

		return $res->get_api_response();
	}

	/**
	 * Queries the Status of the jobID provided
	 * Gets and parses the <jobid>.JSON file
	 *
	 * @param int $job_ID
	 *
	 * @return ScreenshotsResponse
	 */
	public function get_job_status( $job_ID ) {
		$response             = $this->_request( $job_ID . '.json' );
		$screenshots_response = new ScreenshotsResponse( $response );

		return $screenshots_response;
	}

	/**
	 * Actually sends the request to Browserstack
	 *
	 * @param Request $request
	 *
	 * @return ScreenshotsResponse|bool
	 */
	public function send_request( Request $request ) {
		$params = array();

		foreach ( $request as $k => $v ) {
			if ( ( is_string( $v ) && strlen( $v ) > 0 ) || ( is_array( $v ) && count( $v ) > 0 ) ) {
				$params[ $k ] = $v;
			}
		}

		$response = $this->_request( null, $params, 'POST' );

		if ( $this->debug ) {
			var_dump( $response );
		}

		if ( $response ) {
			$screenshots_response = new ScreenshotsResponse( $response );

			return $screenshots_response;
		}

		return false;
	}

	/**
	 * Builds SimpleCurl Requests
	 * Internal wrapper function for SimpleCurl
	 *
	 * @param null   $endpoint
	 * @param array  $params
	 * @param string $method
	 *
	 * @return mixed Returns the content of the request or false if any error occurred
	 */
	private function _request( $endpoint = null, $params = array(), $method = 'GET' ) {

		// Init cURL instance.
		$this->init();

		// Set default options.
		$this->curl->setHTTPHeader( $this->headers );

		$url = self::API_BASE_URL;
		if ( null !== $endpoint ) {
			$url .= '/' . $endpoint;
		}
		if ( is_array( $params ) && count( $params ) > 0 ) {
			$query_string = json_encode( $params );
		}

		if ( $this->debug ) {
			print "Request-Url: $url<br>";

			if ( isset( $query_string ) ) {
				print "QueryString: $query_string<br>";
			}
		}

		if ( 'GET' === $method ) {
			if ( isset( $query_string ) ) {
				$url .= '?' . $query_string;
			}
			$contents = $this->curl->get( $url );

		} elseif ( 'POST' === $method ) {
			$contents = $this->curl->post( $url, $query_string );
		}

		if ( $this->debug ) {
			var_dump( $contents );
		}

		return ( $contents ) ? $contents['response'] : false;
	}

	/**
	 * Checks if the Browserstack Screenshots Service is online and accessible
	 * Can be used prior any requests to ensure that the API is working
	 *
	 * If anything is fine, returns an Array with index 'success'
	 * If any error occured contains an index 'errors' with an error message
	 *
	 * @return Array
	 */
	public function is_browserstack_accessible() {
		$message  = null;
		$headers  = get_headers( self::API_BASE_URL );
		$response = new Base();

		if ( ! empty( $headers ) && isset( $headers[0] ) ) {
			$http_code = $headers[0];

			preg_match( '#HTTP\/1\.1\s+(.*?)\s+#', $http_code, $matches );

			if ( ! empty( $matches ) && isset( $matches[1] ) ) {

				$http_code = $matches[1];
				http_response_code( $http_code );

				switch ( substr( $http_code, 0, 1 ) ) {
					case 2 :
						$response->set_api_response( [ 'success' => [ 'Service is available' ] ] );

						return $response->get_api_response();
					case 4 :
						if ( 404 === $http_code ) {
							$message = 'API Endpoint not found';
						}
						if ( 401 === $http_code ) {
							$message = 'User is not authorized';
						}
						break;
					case 5 :
						$message = 'Bad request: ' . $http_code;
						break;
				}
			}
		} else {
			$message = 'Could not establish connection to Browserstack';
		}

		$response->set_api_response( [ 'errors' => [ $message ] ] );

		return $response->get_api_response();
	}
}
