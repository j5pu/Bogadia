<?php
/**
 * Single Product tabs
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter tabs and allow third parties to add their own
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! empty( $tabs ) ) : ?>
	<div class="envio_dev_free" style="">ENVÍO Y DEVOLUCIÓN <span>GRATUITOS</span></div>
	<div id="accordion-woo" class="panel-group panel-kleo icons-to-right" data-active-tab="1">
		<?php 
		$count_tab = 0;
		foreach ( $tabs as $key => $tab ) : $count_tab++; ?>
			<div class="panel">
				<div class="panel-heading">
					<div class="panel-title">					
							<?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ?>								
						</a>
					</div>
				</div>
				<div id="<?php echo $key ?>_tab" class="panel-collapse">
					<div class="panel-body"><?php call_user_func( $tab['callback'], $key, $tab ) ?></div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

<?php endif; ?>
