<?php

namespace ActiveCampaignLists\Controllers;

use ActiveCampaignLists\Helpers\View;
use ActiveCampaignLists\Core;
use ActiveCampaignLists\Services\Api;

defined( 'ABSPATH' ) || exit;

class Notifications {

	public function __construct() {
		add_filter( 'template_include', array( $this, 'include_notifications_template' ) );
		add_filter( 'generate_rewrite_rules', array( $this, 'add_rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );
	}

	public function add_scripts() {
		wp_enqueue_script(
			'wp-active-campaign-lists-scripts',
			Core::plugins_url( 'resources/dist/bundle.js' ),
			array( 'jquery' ),
			Core::filemtime( 'resources/dist/bundle.js' )
		);
	}

	public function add_query_vars( $query_vars ) {
		$query_vars[] = 'wacl_notifications';

		return $query_vars;
	}

	public function add_rewrite_rules( $wp_rewrite ) {
		$wp_rewrite->rules = array_merge(
			['notificacoes/?$' => 'index.php?wacl_notifications=1'],
			$wp_rewrite->rules
		);

		return $wp_rewrite;
	}

	public function include_notifications_template( $template ) {
		if ( intval( get_query_var( 'wacl_notifications' ) ) === 1 ) {

			$user = wp_get_current_user();

			$this->handle_create_contact( $user );

			return View::render(
				'notifications.main',
				[
					'lists'   => carbon_get_theme_option( 'wpacl_lists' ),
					'contact' => $this->get_current_contact( $user )
				]
			);
		}

		return $template;
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

		return $response->contact;
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

	private function get_api() {
		return Api::get_instance();
	}
}
