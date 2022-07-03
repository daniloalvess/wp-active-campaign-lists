<?php

namespace WpActiveCampaignLists\Helpers;

defined( 'ABSPATH' ) || exit;

class Utils {

	public static function checked( $current, $list = [], $echo = true ) {
		$value = '';

		if ( in_array( $current, $list ) ) {
			$value = 'checked="checked"';
		}

		if ( $echo ) {
			echo $value;
		}

		return $value;
	}

	public static function request( $type, $name, $default, $sanitize = 'rm_tags' ) {
		$request = filter_input_array( $type, FILTER_SANITIZE_SPECIAL_CHARS );

		if ( ! isset( $request[ $name ] ) || empty( $request[ $name ] ) ) {
			return $default;
		}

		return self::sanitize( $request[ $name ], $sanitize );
	}

	public static function post( $name, $default = '', $sanitize = 'rm_tags' ) {
		return self::request( INPUT_POST, $name, $default, $sanitize );
	}

	public static function get( $name, $default = '', $sanitize = 'rm_tags' ) {
		return self::request( INPUT_GET, $name, $default, $sanitize );
	}

	public static function sanitize( $value, $sanitize ) {
		if ( ! is_callable( $sanitize ) ) {
			return ( false === $sanitize ) ? $value : self::rm_tags( $value );
		}

		if ( is_array( $value ) ) {
			return array_map( $sanitize, $value );
		}

		return call_user_func( $sanitize, $value );
	}

	public static function rm_tags( $value, $remove_breaks = false ) {
		if ( empty( $value ) || is_object( $value ) ) {
			return $value;
		}

		if ( is_array( $value ) ) {
			return array_map( __METHOD__, $value );
		}

		return wp_strip_all_tags( $value, $remove_breaks );
	}
}
