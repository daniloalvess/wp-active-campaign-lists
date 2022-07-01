<?php

namespace ActiveCampaignLists\Controllers;

use ActiveCampaignLists\Helpers\{Utils, View};
use ActiveCampaignLists\Core;
use ActiveCampaignLists\Services\Api;

defined( 'ABSPATH' ) || exit;

class Notifications {

	public function __construct() {
		add_filter( 'template_include', array( $this, 'include_notifications_template' ) );
		add_filter( 'generate_rewrite_rules', array( $this, 'add_rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );
		add_action( 'wp_ajax_e36f520fa', array( $this, 'handle_ajax_request' ) );
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

		$this->handle_create_contact( $user );

		return View::render(
			'notifications.main',
			[
				'lists'               => carbon_get_theme_option( 'wpacl_lists' ),
				'contact'             => $this->get_current_contact( $user ),
				'missing_credentials' => $this->get_api()->check_credentials(),
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

		$response = $this->get_api()->update_contact_list([
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

	private function get_current_contact( $user ) {
		$contact_id = get_user_meta( $user->ID, 'wacl_contact_id', true );

		if ( empty( $contact_id ) ) {
			return false;
		}

		$response = $this->get_api()->find_contact_by_id( $contact_id );

		if ( ! isset( $response->contact ) ) {
			return false;
		}

		$contact = $response->contact;
		$contact->contactLists = $this->prepare_contact_lists( $response->contactLists );

		return $contact;
	}

	private function prepare_contact_lists( $contact_lists ) {
		$data = [];

		foreach ( $contact_lists as $item ) {
			if ( intval( $item->status ) === 1 ) {
				array_push( $data, intval( $item->list ) );
			}
		}

		return $data;
	}

	private function handle_create_contact( $user ) {
		$is_created = get_user_meta( $user->ID, 'wacl_contact_id' );

		if ( $is_created ) {
			return;
		}

		$user_exists = $this->get_api()->find_contact_by_email( $user->user_email );

		if ( isset( $user_exists->contacts ) && ! empty( $user_exists->contacts ) ) {
			$contact = current( $user_exists->contacts );
			update_user_meta( $user->ID, 'wacl_contact_id', intval( $contact->id ) );
			return;
		}

		$response = $this->get_api()->create_contact([
			'contact' => [
				'email'     => $user->user_email,
				'firstName' => $user->first_name,
				'lastName'  => $user->last_name,
			]
		]);

		if ( isset( $response->contact ) ) {
			update_user_meta( $user->ID, 'wacl_contact_id', intval( $response->contact->id ) );
		}
	}

	private function is_notifications_page() {
		return intval( get_query_var( 'wpacl_notifications' ) ) === 1;
	}

	private function get_api() {
		return Api::get_instance();
	}
}
