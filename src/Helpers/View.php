<?php

namespace ActiveCampaignLists\Helpers;

use ActiveCampaignLists\Core;

defined( 'ABSPATH' ) || exit;

class View {

	public static function render( $relative_path, array $args = [] ) {
		$path = self::path_join( $relative_path );

		if ( ! file_exists( $path )) {
			return;
		}

		foreach ( $args as $key => $value ) {
			$$key = $value;
		}

		unset( $relative_path, $args );

		include $path;
	}

	public static function path_join( $path ) {
		return sprintf( '%s/%s.php', Core::plugin_dir_path( 'views' ), self::path_normalize( $path ) );
	}

	public static function path_normalize( $path ) {
		return str_replace( '.', '/', $path );
	}
}
