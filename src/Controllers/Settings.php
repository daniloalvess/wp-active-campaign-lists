<?php

namespace ActiveCampaignLists\Controllers;

defined( 'ABSPATH' ) || exit;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Settings {

	public function __construct() {
		add_action( 'carbon_fields_register_fields', array( $this, 'add_menu_page' ) );
	}

	public function add_menu_page() {
		Container::make( 'theme_options', __( ' WP Active Campaign Lists', 'wp-active-campaign-lists' ) )
			->set_page_parent( 'options-general.php' )
			->set_page_file( 'wpacl-settings' )
			->add_tab(
				__( 'Autenticação', 'wp-active-campaign-lists' ),
				array(
					Field::make( 'text', 'wpacl_api_url', __( 'API Url', 'wp-active-campaign-lists' ) )
						->set_required()
						->set_attribute( 'placeholder', 'Ex.: https://exemplodeusuario.api-us1.com' ),
					Field::make( 'text', 'wpacl_api_key', __( 'API Key', 'wp-active-campaign-lists' ) )
						->set_required()
						->set_attribute( 'placeholder', 'Ex.: 89f573ee96ee1a464bdceb56d86dc32b6f80410d208a02ee424832b1c587b861eb921177' )
				)
			)
			->add_tab(
				__( 'Listas', 'wp-active-campaign-lists' ),
				array(
					Field::make( 'complex', 'wpacl_lists', __( 'Configure abaixo, adicionando nome e ID de cada lista:', 'wp-active-campaign-lists' ) )
						->set_layout( 'tabbed-horizontal' )
						->setup_labels([
							'plural_name'   => 'itens',
							'singular_name' => 'item',
						])
						->add_fields([
							Field::make( 'text', 'name', __( 'Nome', 'wp-active-campaign-lists' ) ),
							Field::make( 'number', 'id', __( 'ID', 'wp-active-campaign-lists' ) )
								->set_min(0)
						]),
				)
			);
	}
}
