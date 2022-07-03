<?php

namespace WpActiveCampaignLists\Services;

class Handler {

	public $api;

	public function __construct( ApiClient $api ) {
		$this->api = $api;
	}

	public function save_contact( $user ): void {
		$is_created = get_user_meta( $user->ID, 'wacl_contact_id' );

		if ( $is_created ) {
			return;
		}

		$user_exists = $this->api->find_contact_by_email( $user->user_email );

		if ( isset( $user_exists->contacts ) && ! empty( $user_exists->contacts ) ) {
			$contact = current( $user_exists->contacts );
			update_user_meta( $user->ID, 'wacl_contact_id', intval( $contact->id ) );
			return;
		}

		$response = $this->api->create_contact([
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

	public function get_current_contact( $user ) {
		$contact_id = get_user_meta( $user->ID, 'wacl_contact_id', true );

		if ( empty( $contact_id ) ) {
			return false;
		}

		$response = $this->api->find_contact_by_id( $contact_id );

		if ( ! isset( $response->contact ) ) {
			return false;
		}

		$contact = $response->contact;
		$contact->contactLists = $this->prepare_contact_lists( $response->contactLists );

		return $contact;
	}

	private function prepare_contact_lists( $contact_lists ): array {
		$data = [];

		foreach ( $contact_lists as $item ) {
			if ( intval( $item->status ) === 1 ) {
				array_push( $data, intval( $item->list ) );
			}
		}

		return $data;
	}
}
