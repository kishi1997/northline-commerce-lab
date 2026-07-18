<?php
/**
 * Destination validation for classic and Store API checkouts.
 *
 * @package Northline_Commerce_Rules
 */

declare(strict_types=1);

namespace Northline\CommerceRules;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use WC_Order;
use WC_Product;
use WP_Error;
use WP_REST_Request;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rejects disallowed shipping destinations on the server.
 */
final class Checkout_Validator {

	/**
	 * Register validation hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'woocommerce_after_checkout_validation', array( $this, 'validate_classic_checkout' ), 10, 2 );
		add_action( 'woocommerce_check_cart_items', array( $this, 'validate_known_cart_destination' ) );
		add_action( 'woocommerce_store_api_checkout_update_order_from_request', array( $this, 'validate_store_api_checkout' ), 20, 2 );
	}

	/**
	 * Validate classic checkout data.
	 *
	 * @param array<string, mixed> $data Checkout data.
	 * @param WP_Error             $errors Checkout errors.
	 * @return void
	 */
	public function validate_classic_checkout( array $data, WP_Error $errors ): void {
		$ship_to_different = ! empty( $data['ship_to_different_address'] );
		$region_key        = $ship_to_different ? 'shipping_state' : 'billing_state';
		$region            = isset( $data[ $region_key ] ) ? strtoupper( sanitize_key( (string) $data[ $region_key ] ) ) : '';
		$validation        = $this->validate_region( $region );

		if ( is_wp_error( $validation ) ) {
			$errors->add( $validation->get_error_code(), $validation->get_error_message() );
		}
	}

	/**
	 * Revalidate cart when WooCommerce already knows the customer's destination.
	 *
	 * An empty region is ignored here because checkout owns the required-field error.
	 *
	 * @return void
	 */
	public function validate_known_cart_destination(): void {
		if ( ! WC()->customer ) {
			return;
		}

		$region = strtoupper( sanitize_key( (string) WC()->customer->get_shipping_state() ) );

		if ( '' === $region ) {
			return;
		}

		$validation = $this->validate_region( $region );
		if ( is_wp_error( $validation ) ) {
			wc_add_notice( $validation->get_error_message(), 'error' );
		}
	}

	/**
	 * Validate the Store API order used by Checkout Block.
	 *
	 * @param WC_Order        $order Order assembled by Store API.
	 * @param WP_REST_Request $request Store API request.
	 * @return void
	 * @throws RouteException When the destination is not allowed.
	 */
	public function validate_store_api_checkout( WC_Order $order, WP_REST_Request $request ): void {
		unset( $request );

		$region = $order->get_shipping_state();
		if ( '' === $region ) {
			$region = $order->get_billing_state();
		}

		$validation = $this->validate_region( strtoupper( sanitize_key( $region ) ) );

		if ( is_wp_error( $validation ) ) {
			throw new RouteException(
				sanitize_key( $validation->get_error_code() ),
				esc_html( $validation->get_error_message() ),
				400
			);
		}
	}

	/**
	 * Validate every restricted cart item for a destination.
	 *
	 * @param string $region Province/territory code.
	 * @return true|WP_Error
	 */
	public function validate_region( string $region ): true|WP_Error {
		if ( ! WC()->cart ) {
			return true;
		}

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$product = isset( $cart_item['data'] ) && $cart_item['data'] instanceof WC_Product ? $cart_item['data'] : null;

			if ( ! $product instanceof WC_Product || ! Product_Rules::is_restricted( $product ) ) {
				continue;
			}

			$allowed = Product_Rules::get_regions( $product );

			if ( '' === $region || ! in_array( $region, $allowed, true ) ) {
				return new WP_Error(
					'northline_restricted_region',
					sprintf(
						/* translators: %s: product name. */
						__( '“%s” cannot be ordered for the selected province or territory. Remove it or choose an allowed destination.', 'northline-commerce-rules' ),
						$product->get_name()
					)
				);
			}
		}

		return true;
	}
}
