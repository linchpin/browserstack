<?php

namespace Alexschwarz89\Browserstack\Screenshots\Response;

/**
 * Class Base
 * @package Alexschwarz89\Browserstack\Screenshots\Response
 */
class Base {

	/**
	 * @var
	 */
	public $response;

	/**
	 * @param $api_response
	 */
	public function set_api_response( $api_response ) {
		$this->response = $api_response;
	}

	/**
	 * @return mixed
	 */
	public function get_response() {
		return $this->response;
	}
}
