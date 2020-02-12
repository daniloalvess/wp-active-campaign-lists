<?php

defined( 'ABSPATH' ) || exit;

use ActiveCampaignLists\Helpers\{ Utils, View };

get_header();

if ( ! is_user_logged_in() ) {
	printf( '<h3>%s</h3>', __( 'É preciso estar logado para visualizar o conteúdo dessa página.', 'wp-active-campaign-lists' ) );
	get_footer();
	return;
}

if ( $missing_credentials ) {
	printf( '<h3>%s</h3>', __( 'Não foi possível conectar a API. Por favor, verifique as credenciais na administração.', 'wp-active-campaign-lists' ) );
	get_footer();
	return;
}

if ( empty( $lists ) ) {
	printf( '<h3>%s</h3>', __( 'Nenhuma lista foi cadastrada.', 'wp-active-campaign-lists' ) );
}

if ( empty( $contact ) ) {
	printf( '<h3>%s</h3>', __( 'Usuário não encontrado.', 'wp-active-campaign-lists' ) );
}

if ( $lists && $contact ) :

?>
<section data-contact="<?php echo $contact->id ?>" data-component="lists" class="wp-active-campaign-lists">

	<?php View::render( 'notifications.loader' ); ?>

	<header>
		<div class="col"><?php esc_html_e( 'Suas Assinaturas', 'wp-active-campaign-lists' ); ?></div>
		<div class="col"><?php esc_html_e( 'Notificações', 'wp-active-campaign-lists' ); ?></div>
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
