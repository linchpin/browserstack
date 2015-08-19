<?php
/**
 * Forked from https://github.com/alexschwarz89/browserstack
 */

namespace Linchpin\Browserstack\Screenshots;

/**
 * Class Response
 *
 * Base Class to build a JSON response
 *
 * @package Linchpin\Browserstack\Screenshots
 */
class Response {

	/**
	 * @var
	 */
	private $_response;

	/**
	 * Sets the response in JSON format
	 *
	 * @param string $api_response JSON.
	 */
	public function set_api_response( $api_response ) {
		$this->_response = json_decode( $api_response, true );
	}

	/**
	 * Get the API response as an associative array
	 *
	 * @return Array
	 */
	public function get_response() {
		return $this->_response;
	}

}