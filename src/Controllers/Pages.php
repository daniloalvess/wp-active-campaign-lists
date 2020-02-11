<?php

namespace ActiveCampaignLists\Controllers;

use ActiveCampaignLists\Views\Pages as View;
use ActiveCampaignLists\Core;

defined( 'ABSPATH' ) || exit;

class Pages {

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
			return View::render_notifications();
		}

		return $template;
	}
}
