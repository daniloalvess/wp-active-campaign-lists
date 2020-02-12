<?php

namespace ActiveCampaignLists\Services;

defined( 'ABSPATH' ) || exit;

class Api {

	protected $base_url;

	protected $token;

	protected $version = 'api/3';

	private static $instance = null;

	private function __construct()
	{
		$this->set_config();
	}

	protected function set_config() {
		$this->base_url = carbon_get_theme_option( 'wpacl_api_url' );
		$this->token    = carbon_get_theme_option( 'wpacl_api_key' );
	}

	protected function get_headers( $token = false ) {
		if ( empty( $token ) ) {
			$token = $this->token;
		}

		return [
			'Api-Token'    => $token,
			'Content-Type' => 'application/json'
		];
	}

	protected function request( $url, $data = [], $headers = [], $method = 'POST' ) {
		$params = [
			'method'  => $method,
			'timeout' => 60,
		];

		if ( 'POST' === $method && ! empty( $data ) ) {
			$params['body'] = json_encode($data);
		}

		$params['headers'] = empty( $headers ) ? $this->get_headers() : $headers;

		return $this->get_body( wp_safe_remote_request( $url, $params ) );
	}

	protected function get_url( $resource ) {
		return rtrim( $this->base_url, '/' ) . "/{$this->version}/{$resource}";
	}

	public function create_contact( $data ) {
		try {
			return $this->request(
				$this->get_url( 'contacts' ),
				$data
			);
		} catch ( Exception $e ) {
			return $e->getMessage();
		}
	}

	public function find_contact_by_email( $email ) {
		try {
			return $this->request(
				$this->get_url( 'contacts/?email=' . sanitize_email( $email ) ),
				array(),
				array(),
				'GET'
			);
		} catch ( Exception $e ) {
			return $e->getMessage();
		}
	}

	public function find_all_lists() {
		try {
			return $this->request(
				$this->get_url( 'lists' ),
				array(),
				array(),
				'GET'
			);
		} catch ( Exception $e ) {
			return $e->getMessage();
		}
	}

	public function find_contact_by_id( $contact_id ) {
		try {
			return $this->request(
				$this->get_url( 'contacts/' . intval( $contact_id ) ),
				array(),
				array(),
				'GET'
			);
		} catch ( Exception $e ) {
			return $e->getMessage();
		}
	}

	/**
	 * Get body and normalize the response type.
	 *
	 * @param WP_Error|stdClass $response wp_safe_remote_request response.
	 *
	 * @return stdClass
	 */
	protected function get_body( $response ) {
		$body          = new \stdClass();
		$body->success = false;

		if ( is_wp_error( $response ) ) {
			$body->error = $response->get_error_message();
			return $body;
		}

		if ( ! isset( $response['body'] ) ) {
			$body->error = false;
			return $body;
		}

		return json_decode( $response['body'] );
	}

	public static function get_instance()
	{
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
