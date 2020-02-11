<?php

namespace ActiveCampaignLists\Views;

defined( 'ABSPATH' ) || exit;

class Pages {

	public static function render_notifications() {
		get_header();

		?>
		<section class="wp-active-campaign-lists">
			<header>
				<div class="col">Suas Assinaturas</div>
				<div class="col">Notificações</div>
			</header>
			<div class="row">
				<div class="col">Suno Dividendos</div>
				<div class="col">
					<input class="input" type="checkbox" id="item-1" style="display:none"/>
  					<label for="item-1" class="toggle"><span></span></label>
				</div>
			</div>
			<div class="row">
				<div class="col">Suno Dividendos</div>
				<div class="col">2</div>
			</div>
			</section>
		<?php

		get_footer();
	}
}
