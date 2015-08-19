<?php
/**
 * Forked from https://github.com/alexschwarz89/browserstack
 *
 * Build out our Response
 */

namespace Linchpin\Browserstack\Screenshots\Response;

/**
 * Class ScreenshotsResponse
 * @package Linchpin\Browserstack\Screenshots\Response
 */
class ScreenshotsResponse extends Base {

	const ERROR_LIMIT_REACHED = 'Parallel limit reached';
	const ERROR_INVALID_REQUEST = 'Invalid Request';
	const ERROR_VALIDATION_FAILED = 'Validation failed';
	const ERROR_AUTHENTICATION_FAILED = 'Authentication failed. Please check your login details and retry.';

	/**
	 * Contains an Array of finished screenshots
	 *
	 * @var
	 */
	public $finished_screenshots;

	/**
	 * Contains an Array of pending screenshots
	 *
	 * @var
	 */
	public $pending_screenshots;

	/**
	 * Contains an Array of failed screenshots
	 *
	 * @var
	 */
	public $failed_screenshots;

	/**
	 * Indicates if the request was successful
	 * Should be used first
	 *
	 * A not finished Request is also a successful.
	 *
	 * @var bool
	 */
	public $is_successful = false;

	/**
	 * If the request was successful contains the Browserstack Job ID
	 * needed to query the JOB status
	 *
	 * @var bool
	 */
	public $job_ID = false;

	/**
	 * If this is true, the parallel limit was reached and the request has not been made
	 *
	 * @var bool
	 */
	public $is_throttled = false;

	/**
	 * If provided, contains the Error Message from Browserstack
	 *
	 * @var bool
	 */
	public $error_message = false;

	/**
	 * If provided, contains the fields that caused an error at Browserstack
	 * @var array
	 */
	public $error_fields = array();

	/**
	 * Constructor
	 *
	 * @param string $api_response
	 */
	public function __construct( $api_response ) {
		parent::set_api_response( $api_response );
		$this->parse();
	}

	/**
	 * Parse the JSON response from Browserstack
	 *
	 * @return bool
	 */
	public function parse() {
		if ( is_string( $this->response ) && $this->response === self::ERROR_AUTHENTICATION_FAILED ) {
			$this->error_message = self::ERROR_AUTHENTICATION_FAILED;
		}

		$this->response = json_decode( $this->response );

		if ( ! $this->response ) {
			return false;
		}

		$this->finished_screenshots = array();
		$this->pending_screenshots  = array();

		if ( isset( $this->response->message ) ) {
			if ( $this->response->message === 'Parallel limit reached' ) {
				$this->is_throttled = true;
			}

			$this->error_message = $this->response->message;
			if ( isset( $this->response->errors ) ) {
				$this->error_fields = $this->response->errors;
			}
		}
		if ( isset( $this->response->screenshots ) && count( $this->response->screenshots ) > 0 ) {
			$this->is_successful = true;

			foreach ( $this->response->screenshots as $screenshot ) {
				if ( 'dome' === $screenshot->state ) {
					$this->finished_screenshots[] = $screenshot;
				} elseif ( 'pending' === $screenshot->state || 'processing' === $screenshot->state ) {
					$this->pending_screenshots[] = $screenshot;
				} elseif ( 'timed-out' === $screenshot->state || 'failed' === $screenshot->state ) {
					$this->failed_screenshots[] = $screenshot;
				}
			}
		}
		if ( isset( $this->response->job_id ) ) {
			$this->job_ID = $this->response->job_id;
		} elseif ( isset( $this->response->id ) ) {
			$this->job_ID = $this->response->id;
		} else {
			$this->is_successful = false;

			return false;
		}

		return $this->job_ID;

	}

	/**
	 * If there are no pending screenshots the request is considered as finished
	 *
	 * @return bool
	 */
	public function is_finished() {
		if ( isset( $this->response ) && count( $this->pending_screenshots ) === 0 ) {
			return true;
		}

		return false;
	}

}