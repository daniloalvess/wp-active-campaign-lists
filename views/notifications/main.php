<?php

defined( 'ABSPATH' ) || exit;

use WpActiveCampaignLists\Helpers\{ Utils, View };

get_header();

if ( ! is_user_logged_in() ) {
	printf( '<h3>%s</h3>', __( 'You must be logged in to view the content of this page.', 'wpacl' ) );
	get_footer();
	return;
}

if ( $missing_credentials ) {
	printf( '<h3>%s</h3>', __( 'Could not connect API. Please try again later.', 'wpacl' ) );
	get_footer();
	return;
}

if ( empty( $lists ) ) {
	printf( '<h3>%s</h3>', __( 'No list has been registered.', 'wpacl' ) );
}

if ( empty( $contact ) ) {
	printf( '<h3>%s</h3>', __( 'User not found.', 'wpacl' ) );
}

if ( $lists && $contact ) :

?>
<section data-contact="<?php echo $contact->id ?>" data-component="lists" class="wp-active-campaign-lists">

	<?php View::render( 'notifications.loader' ); ?>

	<header>
		<div class="col"><?php esc_html_e( 'Your Subscriptions', 'wpacl' ); ?></div>
		<div class="col"><?php esc_html_e( 'Notifications', 'wpacl' ); ?></div>
	</header>

	<?php foreach ( $lists as $list ) : ?>

	<div class="row">
		<?php $list_id = intval( $list['id'] ); ?>
		<div class="col"><?php echo esc_html( $list['name'] ); ?></div>
		<div class="col">
			<input class="input"
				data-element="checkbox"
				data-list="<?php echo $list_id; ?>"
				type="checkbox"
				id="item-<?php echo $list_id; ?>"
				style="display:none"
				<?php Utils::checked( $list_id, $contact->contactLists ); ?>
			/>
			<label for="item-<?php echo $list_id; ?>" class="toggle"><span></span></label>
		</div>
	</div>

	<?php
	endforeach;
	wp_nonce_field( 'wacl_update_list' );
	?>

</section>
<?php

endif;

get_footer();
