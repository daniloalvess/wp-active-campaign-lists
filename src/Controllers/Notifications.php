<?php

namespace WpActiveCampaignLists\Controllers;

use WpActiveCampaignLists\Helpers\{Utils, View};
use WpActiveCampaignLists\Core;
use WpActiveCampaignLists\Services\{ApiClient, Handler};

defined( 'ABSPATH' ) || exit;

class Notifications {

	protected $handler;

	public function __construct() {
		$this->handler = $this->get_handler();

		add_filter( 'template_include', array( $this, 'include_notifications_template' ) );
		add_filter( 'generate_rewrite_rules', array( $this, 'add_rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );
		add_action( 'wp_ajax_e36f520fa', array( $this, 'handle_ajax_request' ) );
	}

	public function get_handler() {
		return new Handler(
			new ApiClient(
				get_option( '_wpacl_api_url' ),
				get_option( '_wpacl_api_key' )
			)
		);
	}

	public function add_scripts() {
		if ( ! $this->is_notifications_page() ) {
			return;
		}

		wp_enqueue_script(
			'wp-active-campaign-lists-scripts',
			Core::plugins_url( 'resources/dist/bundle.js' ),
			array( 'jquery' ),
			Core::filemtime( 'resources/dist/bundle.js' )
		);

		wp_localize_script(
			'wp-active-campaign-lists-scripts',
			'waclVars',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' )
			)
		);
	}

	public function add_query_vars( $query_vars ) {
		$query_vars[] = 'wpacl_notifications';

		return $query_vars;
	}

	public function add_rewrite_rules( $wp_rewrite ) {
		$wp_rewrite->rules = array_merge(
			['wpacl-notifications/?$' => 'index.php?wpacl_notifications=1'],
			$wp_rewrite->rules
		);

		return $wp_rewrite;
	}

	public function include_notifications_template( $template ) {
		if ( ! $this->is_notifications_page() ) {
			return $template;
		}

		$user = wp_get_current_user();

		$this->handler->save_contact( $user );

		return View::render(
			'notifications.main',
			[
				'lists'               => carbon_get_theme_option( 'wpacl_lists' ),
				'contact'             => $this->handler->get_current_contact( $user ),
				'missing_credentials' => $this->handler->api->is_invalid_credencials(),
			]
		);
	}

	public function handle_ajax_request() {
		if ( ! wp_verify_nonce( Utils::post('nonce'), 'wacl_update_list' ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid request.', 'wpacl' ) ] );
		}

		$contact_id = Utils::post( 'contact_id', false, 'intval' );
		$list_id    = Utils::post( 'list_id', false, 'intval' );
		$status     = Utils::post( 'status', false, 'intval' );

		if ( ! $contact_id ) {
			wp_send_json_error( [ 'message' => __( 'Please, fill the contact ID.', 'wpacl' ) ] );
		}

		if ( ! $list_id ) {
			wp_send_json_error( [ 'message' => __( 'Please, fill the list ID.', 'wpacl' ) ] );
		}

		if ( ! $status ) {
			wp_send_json_error( [ 'message' => __( 'Please, inform a valid status.', 'wpacl' ) ] );
		}

		$response = $this->handler->api->update_contact_list([
			'contactList' => array(
				'list'    => $list_id,
				'contact' => $contact_id,
				'status'  => $status,
			)
		]);

		if ( ! isset( $response->contactList ) || $response->contactList->status !== $status ) {
			wp_send_json_error( [ 'message' => __( 'Unable to disable notifications.', 'wpacl' ) ] );
		}

		wp_send_json_success( [ 'message' => __( 'Saved successfully!', 'wpacl' ) ] );
	}

	private function is_notifications_page() {
		return intval( get_query_var( 'wpacl_notifications' ) ) === 1;
	}
}
