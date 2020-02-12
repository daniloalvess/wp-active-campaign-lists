<?php

namespace ActiveCampaignLists\Helpers;

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
}
