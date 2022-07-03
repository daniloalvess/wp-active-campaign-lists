<?php

namespace ActiveCampaignLists\Services;

defined( 'ABSPATH' ) || exit;

class ApiClient {

	protected $base_url;

	protected $token;

	protected $version = 'api/3';

	public function __construct( $base_url, $token ) {
		$this->base_url = $base_url;
		$this->token    = $token;
	}

	/**
	 * Return the default headers used by all requests.
	 *
	 * @param boolean $token
	 *
	 * @return array
	 */
	protected function get_headers( $token = false ) {
		if ( empty( $token ) ) {
			$token = $this->token;
		}

		return [
			'Api-Token'    => $token,
			'Content-Type' => 'application/json'
		];
	}

	/**
	 * Abstraction for all requests.
	 *
	 * @param string $url
	 * @param array $data
	 * @param array $headers
	 * @param string $method
	 *
	 * @return stdClass
	 */
	protected function build_request( $url, $data = [], $headers = [], $method = 'POST' ) {
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

	/**
	 * Handle all POST requests.
	 *
	 * @param string $url
	 *
	 * @return stdClass
	 */
	protected function post( $url, $data = [] ) {
		try {
			return $this->build_request( $url, $data );
		} catch ( Exception $e ) {
			return $e->getMessage();
		}
	}

	/**
	 * Handle all GET requests.
	 *
	 * @param string $url
	 *
	 * @return stdClass
	 */
	protected function get( $url ) {
		try {
			return $this->build_request(
				$url,
				array(),
				array(),
				'GET'
			);
		} catch ( Exception $e ) {
			return $e->getMessage();
		}
	}

	protected function get_url( $resource ) {
		return rtrim( $this->base_url, '/' ) . "/{$this->version}/{$resource}";
	}

	/**
	 * Create a contact
	 *
	 * Reference: https://developers.activecampaign.com/reference#create-contact
	 *
	 * @param array $data
	 *
	 * @return stdClass
	 */
	public function create_contact( $data ) {
		return $this->post( $this->get_url( 'contacts' ), $data );
	}

	/**
	 * Retrieve a contact by user email.
	 *
	 * @param string $email
	 *
	 * @return stdClass
	 */
	public function find_contact_by_email( $email ) {
		return $this->get( $this->get_url( 'contacts/?email=' . sanitize_email( $email ) ) );
	}

	/**
	 * Retrieve all lists.
	 *
	 * Reference: https://developers.activecampaign.com/reference#retrieve-all-lists
	 *
	 * @return stdClass
	 */
	public function find_all_lists() {
		return $this->get( $this->get_url( 'lists' ) );
	}

	/**
	 * Retrieve a contact by ID.
	 *
	 * Reference: https://developers.activecampaign.com/reference#get-contact
	 *
	 * @param integer $contact_id
	 *
	 * @return stdClass
	 */
	public function find_contact_by_id( $contact_id ) {
		return $this->get( $this->get_url( 'contacts/' . intval( $contact_id ) ) );
	}

	/**
	 * Update the list status for a contact.
	 *
	 * Reference: https://developers.activecampaign.com/reference#update-list-status-for-contact
	 *
	 * @param array $data
	 *
	 * @return stdClass
	 */
	public function update_contact_list( $data ) {
		return $this->post( $this->get_url( 'contactLists' ), $data );
	}

	/**
	 * Verify if the user filled API credentials.
	 *
	 * @return boolean
	 */
	public function check_credentials() {
		return empty( $this->base_url ) || empty( $this->token );
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
}
