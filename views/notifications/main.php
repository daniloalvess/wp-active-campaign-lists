<?php

defined( 'ABSPATH' ) || exit;

use ActiveCampaignLists\Helpers\Utils;

get_header();

if ( empty( $lists ) ) {
	printf( '<h3>%s</h3>', __( 'Nenhuma lista foi cadastrada.', 'wp-active-campaign-lists' ) );
}

if ( empty( $contact ) ) {
	printf( '<h3>%s</h3>', __( 'Contato não encontrado.', 'wp-active-campaign-lists' ) );
}

if ( $lists && $contact ) :

?>
<section class="wp-active-campaign-lists">
	<header>
		<div class="col"><?php esc_html_e( 'Suas Assinaturas', 'wp-active-campaign-lists' ); ?></div>
		<div class="col"><?php esc_html_e( 'Notificações', 'wp-active-campaign-lists' ); ?></div>
	</header>
	<?php foreach ( $lists as $list ) : ?>
	<div class="row">
		<div class="col"><?php echo esc_html( $list['name'] ); ?></div>
		<div class="col">
			<input class="input"
				<?php Utils::checked( $list['id'], $contact->contactLists ); ?>
				type="checkbox"
				id="item-<?php echo intval( $list['id'] ); ?>"
				style="display:none"
			/>
			<label for="item-<?php echo intval( $list['id'] ); ?>" class="toggle"><span></span></label>
		</div>
	</div>
	<?php endforeach; ?>
</section>
<?php

endif;

get_footer();
