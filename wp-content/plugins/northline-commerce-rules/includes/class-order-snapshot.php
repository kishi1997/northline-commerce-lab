<?php
/**
 * Immutable order-item rule snapshots.
 *
 * @package Northline_Commerce_Rules
 */

declare(strict_types=1);

namespace Northline\CommerceRules;

use WC_Order;
use WC_Order_Item_Product;
use WC_Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stores the rule that applied at checkout on each order line.
 */
final class Order_Snapshot {

	/**
	 * Register checkout hook.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'snapshot' ), 10, 4 );
	}

	/**
	 * Add public item metadata. WooCommerce displays it in admin, customer details, and emails.
	 *
	 * @param WC_Order_Item_Product $item Order line item.
	 * @param string                $cart_item_key Cart item key.
	 * @param array<string, mixed>  $values Cart values.
	 * @param WC_Order              $order Order object.
	 * @return void
	 */
	public function snapshot( WC_Order_Item_Product $item, string $cart_item_key, array $values, WC_Order $order ): void {
		unset( $cart_item_key );

		$product = isset( $values['data'] ) && $values['data'] instanceof WC_Product ? $values['data'] : null;

		if ( ! $product instanceof WC_Product || ! Product_Rules::is_restricted( $product ) ) {
			return;
		}

		$context = array(
			'order'   => $order,
			'product' => $product,
		);

		/**
		 * Filter a future external identity-verification result.
		 *
		 * The plugin deliberately defaults to false and makes no verification claim.
		 *
		 * @param bool                 $verified Verification state.
		 * @param array<string, mixed> $context Order and product context.
		 */
		$verified = (bool) apply_filters( 'northline_customer_age_verified', false, $context );

		$item->add_meta_data(
			__( 'Purchase notice', 'northline-commerce-rules' ),
			Product_Rules::get_notice( $product ),
			true
		);
		$item->add_meta_data(
			__( 'Allowed regions at purchase', 'northline-commerce-rules' ),
			implode( ', ', Product_Rules::get_regions( $product ) ),
			true
		);
		$item->add_meta_data(
			__( 'Identity status', 'northline-commerce-rules' ),
			$verified
				? __( 'Verified by configured integration', 'northline-commerce-rules' )
				: __( 'Not verified — technical demo', 'northline-commerce-rules' ),
			true
		);
	}
}
