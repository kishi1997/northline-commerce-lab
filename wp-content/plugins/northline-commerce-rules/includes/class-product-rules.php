<?php
/**
 * Product rule editing and display.
 *
 * @package Northline_Commerce_Rules
 */

declare(strict_types=1);

namespace Northline\CommerceRules;

use WC_Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns restricted-product metadata.
 */
final class Product_Rules {

	public const META_RESTRICTED = '_northline_restricted';
	public const META_NOTICE     = '_northline_notice';
	public const META_REGIONS    = '_northline_allowed_regions';
	public const NONCE_ACTION    = 'northline_save_product_rules';
	public const NONCE_NAME      = 'northline_product_rules_nonce';
	private const NOTICE_LIMIT   = 240;

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'render_fields' ) );
		add_action( 'woocommerce_admin_process_product_object', array( $this, 'save_fields' ) );
		add_action( 'woocommerce_single_product_summary', array( $this, 'render_product_notice' ), 24 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );
	}

	/**
	 * Render product editor fields.
	 *
	 * @return void
	 */
	public function render_fields(): void {
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );

		echo '<div class="options_group">';

		woocommerce_wp_checkbox(
			array(
				'id'          => self::META_RESTRICTED,
				'label'       => __( 'Restricted product', 'northline-commerce-rules' ),
				'description' => __( 'Enables the technical notice and destination checks. This is not identity verification.', 'northline-commerce-rules' ),
			)
		);

		woocommerce_wp_textarea_input(
			array(
				'id'                => self::META_NOTICE,
				'label'             => __( 'Display notice', 'northline-commerce-rules' ),
				'description'       => __( 'Plain text shown on the product, cart, order, customer details, and email.', 'northline-commerce-rules' ),
				'desc_tip'          => true,
				'custom_attributes' => array( 'maxlength' => (string) self::NOTICE_LIMIT ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => self::META_REGIONS,
				'label'       => __( 'Allowed Canadian regions', 'northline-commerce-rules' ),
				'placeholder' => 'BC, AB, ON',
				'description' => __( 'Comma-separated province/territory codes. Leave empty to deny all destinations for a restricted product.', 'northline-commerce-rules' ),
				'desc_tip'    => true,
			)
		);

		echo '</div>';
	}

	/**
	 * Save rule fields after nonce and capability validation.
	 *
	 * @param WC_Product $product Product being saved.
	 * @return void
	 */
	public function save_fields( WC_Product $product ): void {
		$nonce = isset( $_POST[ self::NONCE_NAME ] ) ? sanitize_text_field( wp_unslash( $_POST[ self::NONCE_NAME ] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $product->get_id() ) ) {
			return;
		}

		$restricted = isset( $_POST[ self::META_RESTRICTED ] ) ? 'yes' : 'no';
		$notice     = isset( $_POST[ self::META_NOTICE ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ self::META_NOTICE ] ) ) : '';
		$regions    = isset( $_POST[ self::META_REGIONS ] ) ? self::sanitize_regions( sanitize_text_field( wp_unslash( $_POST[ self::META_REGIONS ] ) ) ) : array();

		if ( function_exists( 'mb_substr' ) ) {
			$notice = mb_substr( $notice, 0, self::NOTICE_LIMIT );
		} else {
			$notice = substr( $notice, 0, self::NOTICE_LIMIT );
		}

		$product->update_meta_data( self::META_RESTRICTED, $restricted );
		$product->update_meta_data( self::META_NOTICE, $notice );
		$product->update_meta_data( self::META_REGIONS, implode( ',', $regions ) );
	}

	/**
	 * Render the product-page notice.
	 *
	 * @return void
	 */
	public function render_product_notice(): void {
		$product = wc_get_product( get_the_ID() );

		if ( ! $product instanceof WC_Product || ! self::is_restricted( $product ) ) {
			return;
		}

		$notice = self::get_notice( $product );
		?>
		<aside class="northline-rule-notice" role="note" aria-label="<?php esc_attr_e( 'Purchase notice', 'northline-commerce-rules' ); ?>">
			<strong><?php esc_html_e( 'Technical restriction demo', 'northline-commerce-rules' ); ?></strong>
			<?php if ( '' !== $notice ) : ?>
				<p><?php echo esc_html( $notice ); ?></p>
			<?php endif; ?>
			<p><?php esc_html_e( 'No age or identity verification is performed by this demonstration.', 'northline-commerce-rules' ); ?></p>
		</aside>
		<?php
	}

	/**
	 * Add public rule information to cart/checkout item data.
	 *
	 * @param array<string, mixed> $item_data Existing item data.
	 * @param array<string, mixed> $cart_item Cart item.
	 * @return array<string, mixed>
	 */
	public function add_cart_item_data( array $item_data, array $cart_item ): array {
		$product = isset( $cart_item['data'] ) && $cart_item['data'] instanceof WC_Product ? $cart_item['data'] : null;

		if ( ! $product instanceof WC_Product || ! self::is_restricted( $product ) ) {
			return $item_data;
		}

		$notice = self::get_notice( $product );

		if ( '' !== $notice ) {
			$item_data[] = array(
				'key'   => __( 'Purchase notice', 'northline-commerce-rules' ),
				'value' => esc_html( $notice ),
			);
		}

		$item_data[] = array(
			'key'   => __( 'Identity status', 'northline-commerce-rules' ),
			'value' => esc_html__( 'Not verified — technical demo', 'northline-commerce-rules' ),
		);

		return $item_data;
	}

	/**
	 * Determine whether a product is restricted, inheriting from its parent.
	 *
	 * @param WC_Product $product Product or variation.
	 * @return bool
	 */
	public static function is_restricted( WC_Product $product ): bool {
		return 'yes' === self::get_inherited_meta( $product, self::META_RESTRICTED );
	}

	/**
	 * Get the public notice.
	 *
	 * @param WC_Product $product Product or variation.
	 * @return string
	 */
	public static function get_notice( WC_Product $product ): string {
		return sanitize_textarea_field( (string) self::get_inherited_meta( $product, self::META_NOTICE ) );
	}

	/**
	 * Get allowed region codes.
	 *
	 * @param WC_Product $product Product or variation.
	 * @return array<int, string>
	 */
	public static function get_regions( WC_Product $product ): array {
		return self::sanitize_regions( (string) self::get_inherited_meta( $product, self::META_REGIONS ) );
	}

	/**
	 * Sanitize comma-separated region codes using a fixed allow list.
	 *
	 * @param mixed $raw Raw value.
	 * @return array<int, string>
	 */
	public static function sanitize_regions( mixed $raw ): array {
		$parts   = is_array( $raw ) ? $raw : explode( ',', (string) $raw );
		$allowed = array_keys( self::region_options() );
		$clean   = array();

		foreach ( $parts as $part ) {
			$code = strtoupper( sanitize_key( trim( (string) $part ) ) );

			if ( in_array( $code, $allowed, true ) ) {
				$clean[] = $code;
			}
		}

		return array_values( array_unique( $clean ) );
	}

	/**
	 * Canadian region allow list.
	 *
	 * @return array<string, string>
	 */
	public static function region_options(): array {
		$regions = array(
			'AB' => 'Alberta',
			'BC' => 'British Columbia',
			'MB' => 'Manitoba',
			'NB' => 'New Brunswick',
			'NL' => 'Newfoundland and Labrador',
			'NS' => 'Nova Scotia',
			'NT' => 'Northwest Territories',
			'NU' => 'Nunavut',
			'ON' => 'Ontario',
			'PE' => 'Prince Edward Island',
			'QC' => 'Quebec',
			'SK' => 'Saskatchewan',
			'YT' => 'Yukon',
		);

		/**
		 * Filter valid region codes used by the demo rules.
		 *
		 * @param array<string, string> $regions Code-to-label map.
		 */
		return (array) apply_filters( 'northline_allowed_region_options', $regions );
	}

	/**
	 * Read metadata from a variation or its parent product.
	 *
	 * @param WC_Product $product Product or variation.
	 * @param string     $key Metadata key.
	 * @return mixed
	 */
	private static function get_inherited_meta( WC_Product $product, string $key ): mixed {
		$value = $product->get_meta( $key, true );

		if ( '' === $value && $product->is_type( 'variation' ) ) {
			$parent = wc_get_product( $product->get_parent_id() );
			if ( $parent instanceof WC_Product ) {
				$value = $parent->get_meta( $key, true );
			}
		}

		return $value;
	}
}
