<?php

/**
 * Plugin Name: WP Active Campaign Lists
 * Version: 1.0.0
 * Author: Danilo Alves
 * Author URI: https://github.com/daniloalvess
 * Description: Gerencia listas do Active Campaign e usuários inscritos.
 * Text Domain: wp-active-campaign-lists
 * Domain Path: /languages
 * Requires PHP: 7.0
*/

defined( 'ABSPATH' ) || exit;

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

add_action( 'after_setup_theme', function() {
	if ( class_exists( 'Carbon_Fields\Carbon_Fields' ) ) {
		Carbon_Fields\Carbon_Fields::boot();
	}
} );

define( 'WACL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'WACL_PLUGIN_VERSION', '1.0.0' );

$core = new ActiveCampaignLists\Core();

register_activation_hook( __FILE__, 'flush_rewrite_rules' );
