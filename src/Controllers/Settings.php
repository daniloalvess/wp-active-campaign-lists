<?php

namespace WpActiveCampaignLists\Controllers;

defined( 'ABSPATH' ) || exit;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Settings {

	public function __construct() {
		add_action( 'carbon_fields_register_fields', array( $this, 'add_menu_page' ) );
	}

	public function add_menu_page() {
		Container::make( 'theme_options', __( ' WP Active Campaign Lists', 'wpacl' ) )
			->set_page_parent( 'options-general.php' )
			->set_page_file( 'wpacl-settings' )
			->add_tab(
				__( 'Authentication', 'wpacl' ),
				array(
					Field::make( 'text', 'wpacl_api_url', __( 'API Url', 'wpacl' ) )
						->set_required()
						->set_attribute( 'placeholder', 'Ex.: https://exemplodeusuario.api-us1.com' ),
					Field::make( 'text', 'wpacl_api_key', __( 'API Key', 'wpacl' ) )
						->set_required()
						->set_attribute( 'placeholder', 'Ex.: 89f573ee96ee1a464bdceb56d86dc32b6f80410d208a02ee424832b1c587b861eb921177' )
				)
			)
			->add_tab(
				__( 'Lists', 'wpacl' ),
				array(
					Field::make( 'complex', 'wpacl_lists', __( 'Configure below, adding the NAME and ID of each list:', 'wpacl' ) )
						->set_layout( 'tabbed-horizontal' )
						->setup_labels([
							'plural_name'   => 'items',
							'singular_name' => 'item',
						])
						->add_fields([
							Field::make( 'text', 'name', __( 'Name', 'wpacl' ) ),
							Field::make( 'text', 'id', __( 'ID (Active Campaign)', 'wpacl' ) )
						]),
				)
			);
	}
}
