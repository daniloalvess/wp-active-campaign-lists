<?php

namespace ActiveCampaignLists;

defined( 'ABSPATH' ) || exit;

class Core  {

	public function __construct() {
		$this->load_controllers([
			'Settings',
			'Notifications',
		]);
	}

	public function load_controllers( $controllers ) {
		$namespace = $this->get_namespace();

		foreach ( $controllers as $name ) {
			$this->handle_instance( sprintf( "{$namespace}\Controllers\%s", $name ) );
		}
	}

	public function get_namespace() {
		return ( new \ReflectionClass( $this ) )->getNamespaceName();
	}

	private function handle_instance( $class ) {
		return new $class();
	}

	public static function plugins_url( $path ) {
		return esc_url( plugins_url( $path, dirname( __FILE__ ) ) );
	}

	public static function plugin_dir_path( $path = '' ) {
		return plugin_dir_path( dirname( __FILE__ ) ) . $path;
	}

	public static function filemtime( $path ) {
		$file = self::plugin_dir_path( $path );

		return file_exists( $file ) ? filemtime( $file ) : '1.0.0';
	}
}
