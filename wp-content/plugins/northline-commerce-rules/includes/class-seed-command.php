<?php
/**
 * Deterministic WP-CLI demo seeding.
 *
 * @package Northline_Commerce_Rules
 */

declare(strict_types=1);

namespace Northline\CommerceRules;

use WC_Coupon;
use WC_Product;
use WC_Product_Attribute;
use WC_Product_Simple;
use WC_Product_Variable;
use WC_Product_Variation;
use WC_Shipping_Zones;
use WP_CLI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generates the complete fictional store without a database dump.
 */
final class Seed_Command {

	/**
	 * Seed the demo store.
	 *
	 * ## OPTIONS
	 *
	 * [--yes]
	 * : Confirm that demo settings and Northline-managed content may be updated.
	 *
	 * @param array<int, string>   $args Positional arguments.
	 * @param array<string, mixed> $assoc_args Named arguments.
	 * @return void
	 */
	public function seed( array $args, array $assoc_args ): void {
		unset( $args );

		if ( empty( $assoc_args['yes'] ) ) {
			WP_CLI::confirm( 'Update Northline demo content and WooCommerce settings?' );
		}

		// A seed must never attempt real email delivery from a disposable environment.
		add_filter( 'pre_wp_mail', '__return_true' );

		$this->configure_site();
		$category_ids = $this->create_categories();
		$asset_ids    = $this->create_product_images();
		$this->assign_category_images( $category_ids, $asset_ids );
		$this->create_products( $category_ids, $asset_ids );
		$this->create_pages();
		$this->create_coupon();
		$this->configure_shipping();
		$this->create_demo_users();

		remove_filter( 'pre_wp_mail', '__return_true' );

		flush_rewrite_rules();
		WP_CLI::success( 'Northline Commerce Lab seeded successfully.' );
	}

	/**
	 * Configure site and WooCommerce defaults.
	 *
	 * @return void
	 */
	private function configure_site(): void {
		update_option( 'blogname', 'Northline Commerce Lab' );
		update_option( 'blogdescription', 'Engineered equipment. Considered choices. Technical demo only.' );
		update_option( 'timezone_string', 'America/Vancouver' );
		update_option( 'permalink_structure', '/%postname%/' );
		update_option( 'woocommerce_currency', 'CAD' );
		update_option( 'woocommerce_default_country', 'CA:BC' );
		update_option( 'woocommerce_calc_taxes', 'yes' );
		update_option( 'woocommerce_prices_include_tax', 'no' );
		update_option( 'woocommerce_enable_guest_checkout', 'yes' );
		update_option( 'woocommerce_enable_checkout_login_reminder', 'no' );
		update_option( 'woocommerce_manage_stock', 'yes' );
		update_option( 'woocommerce_notify_low_stock_amount', '3' );
		update_option( 'woocommerce_notify_no_stock_amount', '0' );
		update_option( 'woocommerce_coming_soon', 'no' );
		update_option( 'woocommerce_store_pages_only', 'no' );
		update_option( 'woocommerce_allow_tracking', 'no' );
		update_option(
			'woocommerce_onboarding_profile',
			array(
				'completed' => true,
				'skipped'   => true,
			)
		);
		update_option( 'woocommerce_task_list_hidden_lists', array( 'setup', 'extended' ) );
		update_option( 'woocommerce_task_list_completed_lists', array( 'setup', 'extended' ) );
		delete_option( 'woocommerce_onboarding_profile_progress' );
		delete_transient( '_wc_activation_redirect' );

		$cod = (array) get_option( 'woocommerce_cod_settings', array() );
		update_option(
			'woocommerce_cod_settings',
			array_merge(
				$cod,
				array(
					'enabled'     => 'yes',
					'title'       => 'Test order — no payment',
					'description' => 'Technical demo only. No charge is made and nothing is shipped.',
				)
			)
		);

		switch_theme( 'northline-storefront' );
	}

	/**
	 * Create top-level product categories.
	 *
	 * @return array<string, int>
	 */
	private function create_categories(): array {
		$categories = array(
			'devices'           => 'Devices',
			'accessories'       => 'Accessories',
			'replacement-parts' => 'Replacement Parts',
			'care'              => 'Care',
		);
		$ids        = array();

		foreach ( $categories as $slug => $name ) {
			$existing = term_exists( $slug, 'product_cat' );
			if ( ! $existing ) {
				$existing = wp_insert_term( $name, 'product_cat', array( 'slug' => $slug ) );
			}

			if ( is_wp_error( $existing ) ) {
				WP_CLI::error( $existing->get_error_message() );
			}

			$ids[ $slug ] = (int) ( is_array( $existing ) ? $existing['term_id'] : $existing );
		}

		update_option( 'default_product_cat', $ids['devices'] );
		$uncategorized = get_term_by( 'slug', 'uncategorized', 'product_cat' );
		if ( $uncategorized instanceof \WP_Term && (int) $uncategorized->term_id !== $ids['devices'] ) {
			wp_delete_term( $uncategorized->term_id, 'product_cat' );
		}

		return $ids;
	}

	/**
	 * Create or update twelve fictional products.
	 *
	 * @param array<string, int> $category_ids Category IDs.
	 * @param array<string, int> $asset_ids Product image IDs keyed by SKU.
	 * @return void
	 */
	private function create_products( array $category_ids, array $asset_ids ): void {
		$products = array(
			array(
				'sku'        => 'NL-CORE-20',
				'name'       => 'NL Core 2.0 Precision Device',
				'category'   => 'devices',
				'price'      => '249.00',
				'stock'      => 16,
				'featured'   => true,
				'restricted' => true,
			),
			array(
				'sku'        => 'NL-FIELD-11',
				'name'       => 'Field Module 11',
				'category'   => 'devices',
				'price'      => '189.00',
				'sale'       => '169.00',
				'stock'      => 8,
				'featured'   => false,
				'restricted' => true,
			),
			array(
				'sku'        => 'NL-STUDIO-V',
				'name'       => 'Studio Variable System',
				'category'   => 'devices',
				'price'      => '219.00',
				'stock'      => 12,
				'featured'   => true,
				'restricted' => true,
				'variable'   => true,
			),
			array(
				'sku'        => 'NL-POCKET-04',
				'name'       => 'Pocket Control 04',
				'category'   => 'devices',
				'price'      => '129.00',
				'stock'      => 0,
				'featured'   => false,
				'restricted' => true,
			),
			array(
				'sku'        => 'NL-GLASS-01',
				'name'       => 'Precision Glass Attachment',
				'category'   => 'accessories',
				'price'      => '89.00',
				'stock'      => 9,
				'featured'   => true,
				'restricted' => false,
			),
			array(
				'sku'        => 'NL-CASE-02',
				'name'       => 'Travel Carry Solution',
				'category'   => 'accessories',
				'price'      => '59.00',
				'stock'      => 2,
				'featured'   => false,
				'restricted' => false,
			),
			array(
				'sku'        => 'NL-STAND-03',
				'name'       => 'Mineral Display Stand',
				'category'   => 'accessories',
				'price'      => '44.00',
				'stock'      => 18,
				'featured'   => false,
				'restricted' => false,
			),
			array(
				'sku'        => 'NL-RING-10',
				'name'       => 'Service Ring Set',
				'category'   => 'replacement-parts',
				'price'      => '18.00',
				'stock'      => 30,
				'featured'   => false,
				'restricted' => false,
			),
			array(
				'sku'        => 'NL-SCREEN-20',
				'name'       => 'Precision Screen Pack',
				'category'   => 'replacement-parts',
				'price'      => '22.00',
				'stock'      => 25,
				'featured'   => false,
				'restricted' => false,
			),
			array(
				'sku'        => 'NL-ADAPTER-08',
				'name'       => 'Universal Adapter 08',
				'category'   => 'replacement-parts',
				'price'      => '34.00',
				'stock'      => 7,
				'featured'   => false,
				'restricted' => false,
			),
			array(
				'sku'        => 'NL-CARE-01',
				'name'       => 'Technical Care Kit',
				'category'   => 'care',
				'price'      => '29.00',
				'stock'      => 20,
				'featured'   => false,
				'restricted' => false,
			),
			array(
				'sku'        => 'NL-BRUSH-02',
				'name'       => 'Detail Brush Pair',
				'category'   => 'care',
				'price'      => '16.00',
				'stock'      => 14,
				'featured'   => false,
				'restricted' => false,
			),
		);

		foreach ( $products as $index => $data ) {
			$this->upsert_product( $data, $category_ids[ $data['category'] ], $asset_ids, $index );
		}
	}

	/**
	 * Create or update one product through WooCommerce CRUD APIs.
	 *
	 * @param array<string, mixed> $data Product fixture.
	 * @param int                  $category_id Product category ID.
	 * @param array<string, int>   $asset_ids Product image IDs keyed by SKU.
	 * @param int                  $menu_order Stable catalogue order.
	 * @return void
	 */
	private function upsert_product( array $data, int $category_id, array $asset_ids, int $menu_order ): void {
		$id       = wc_get_product_id_by_sku( (string) $data['sku'] );
		$variable = ! empty( $data['variable'] );
		$product  = $id ? wc_get_product( $id ) : ( $variable ? new WC_Product_Variable() : new WC_Product_Simple() );

		if ( ! $product instanceof WC_Product ) {
			WP_CLI::warning( 'Could not load product ' . $data['sku'] );
			return;
		}

		$product->set_name( (string) $data['name'] );
		$product->set_status( 'publish' );
		$product->set_catalog_visibility( 'visible' );
		$product->set_sku( (string) $data['sku'] );
		$product->set_description( 'A fictional, unbranded technical product created only to demonstrate WooCommerce catalogue and order workflows.' );
		$product->set_short_description( 'Documented materials, precise controls, and a no-sales technical-demo notice.' );
		$product->set_regular_price( (string) $data['price'] );
		$product->set_sale_price( isset( $data['sale'] ) ? (string) $data['sale'] : '' );
		$product->set_manage_stock( true );
		$product->set_stock_quantity( (int) $data['stock'] );
		$product->set_stock_status( (int) $data['stock'] > 0 ? 'instock' : 'outofstock' );
		$product->set_category_ids( array( $category_id ) );
		$product->set_featured( (bool) $data['featured'] );
		$product->set_menu_order( $menu_order );
		$product->set_weight( '0.6' );
		$product->set_length( '14' );
		$product->set_width( '7' );
		$product->set_height( '5' );
		if ( isset( $asset_ids[ $data['sku'] ] ) ) {
			$product->set_image_id( $asset_ids[ $data['sku'] ] );
		}
		if ( 'NL-CORE-20' === $data['sku'] ) {
			$gallery_ids = array_filter(
				array(
					$asset_ids['NL-FIELD-11'] ?? 0,
					$asset_ids['NL-STUDIO-V'] ?? 0,
				)
			);
			$product->set_gallery_image_ids( array_values( $gallery_ids ) );
		}
		$product->update_meta_data( Product_Rules::META_RESTRICTED, ! empty( $data['restricted'] ) ? 'yes' : 'no' );
		$product->update_meta_data( Product_Rules::META_NOTICE, ! empty( $data['restricted'] ) ? 'Restricted-product workflow demo. Destination availability is checked before the order can be placed.' : '' );
		$product->update_meta_data( Product_Rules::META_REGIONS, ! empty( $data['restricted'] ) ? 'BC,AB,ON' : '' );

		if ( $variable && $product instanceof WC_Product_Variable ) {
			$attribute = new WC_Product_Attribute();
			$attribute->set_name( 'Finish' );
			$attribute->set_options( array( 'Onyx', 'Stone' ) );
			$attribute->set_visible( true );
			$attribute->set_variation( true );
			$product->set_attributes( array( $attribute ) );
		}

		$product_id = $product->save();

		if ( $variable && $product instanceof WC_Product_Variable ) {
			$this->upsert_variations( $product_id, (string) $data['sku'] );
		}

		WP_CLI::log( sprintf( 'Product ready: %s', $data['sku'] ) );
	}

	/**
	 * Import the deterministic, project-authored WebP catalogue images.
	 *
	 * @return array<string, int>
	 */
	private function create_product_images(): array {
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$fixtures = array(
			'NL-CORE-20'    => array(
				'file'  => 'nl-core-20.webp',
				'title' => 'NL Core 2.0 product study',
			),
			'NL-FIELD-11'   => array(
				'file'  => 'nl-field-11.webp',
				'title' => 'Field Module 11 product study',
			),
			'NL-STUDIO-V'   => array(
				'file'  => 'nl-studio-v.webp',
				'title' => 'Studio Variable System product study',
			),
			'NL-POCKET-04'  => array(
				'file'  => 'nl-pocket-04.webp',
				'title' => 'Pocket Control 04 product study',
			),
			'NL-GLASS-01'   => array(
				'file'  => 'nl-glass-01.webp',
				'title' => 'Precision Glass Attachment product study',
			),
			'NL-CASE-02'    => array(
				'file'  => 'nl-case-02.webp',
				'title' => 'Travel Carry Solution product study',
			),
			'NL-STAND-03'   => array(
				'file'  => 'nl-stand-03.webp',
				'title' => 'Mineral Display Stand product study',
			),
			'NL-RING-10'    => array(
				'file'  => 'nl-ring-10.webp',
				'title' => 'Service Ring Set product study',
			),
			'NL-SCREEN-20'  => array(
				'file'  => 'nl-screen-20.webp',
				'title' => 'Precision Screen Pack product study',
			),
			'NL-ADAPTER-08' => array(
				'file'  => 'nl-adapter-08.webp',
				'title' => 'Universal Adapter product study',
			),
			'NL-CARE-01'    => array(
				'file'  => 'nl-care-01.webp',
				'title' => 'Technical Care Kit product study',
			),
			'NL-BRUSH-02'   => array(
				'file'  => 'nl-brush-02.webp',
				'title' => 'Detail Brush Pair product study',
			),
		);
		$ids      = array();

		foreach ( $fixtures as $sku => $fixture ) {
			// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_key, WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Bounded lookup across twelve seed-owned attachments.
			$existing = get_posts(
				array(
					'post_type'      => 'attachment',
					'post_status'    => 'inherit',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'meta_key'       => '_northline_asset_key',
					'meta_value'     => $sku,
				)
			);
			// phpcs:enable WordPress.DB.SlowDBQuery.slow_db_query_meta_key, WordPress.DB.SlowDBQuery.slow_db_query_meta_value

			if ( ! empty( $existing ) ) {
				$ids[ $sku ] = (int) $existing[0];
				continue;
			}

			$source = NORTHLINE_RULES_PATH . 'assets/demo-products/' . $fixture['file'];
			if ( ! is_readable( $source ) ) {
				WP_CLI::warning( 'Missing generated image: ' . $fixture['file'] );
				continue;
			}

			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reads a local project-generated image, not a remote URL.
			$contents = file_get_contents( $source );
			if ( false === $contents ) {
				WP_CLI::warning( 'Could not read generated image: ' . $fixture['file'] );
				continue;
			}

			$upload = wp_upload_bits( $fixture['file'], null, $contents );
			if ( ! empty( $upload['error'] ) ) {
				WP_CLI::warning( (string) $upload['error'] );
				continue;
			}

			$attachment_id = wp_insert_attachment(
				array(
					'post_title'     => $fixture['title'],
					'post_content'   => '',
					'post_status'    => 'inherit',
					'post_mime_type' => 'image/webp',
				),
				$upload['file']
			);

			if ( is_wp_error( $attachment_id ) ) {
				WP_CLI::warning( $attachment_id->get_error_message() );
				continue;
			}

			$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
			wp_update_attachment_metadata( $attachment_id, $metadata );
			update_post_meta( $attachment_id, '_northline_asset_key', $sku );
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', $fixture['title'] );
			$ids[ $sku ] = (int) $attachment_id;
		}

		return $ids;
	}

	/**
	 * Assign representative generated images to product categories.
	 *
	 * @param array<string, int> $category_ids Category IDs keyed by slug.
	 * @param array<string, int> $asset_ids Attachment IDs keyed by SKU.
	 * @return void
	 */
	private function assign_category_images( array $category_ids, array $asset_ids ): void {
		$assignments = array(
			'devices'           => 'NL-CORE-20',
			'accessories'       => 'NL-GLASS-01',
			'replacement-parts' => 'NL-RING-10',
			'care'              => 'NL-CARE-01',
		);

		foreach ( $assignments as $category => $sku ) {
			if ( isset( $category_ids[ $category ], $asset_ids[ $sku ] ) ) {
				update_term_meta( $category_ids[ $category ], 'thumbnail_id', $asset_ids[ $sku ] );
			}
		}
	}

	/**
	 * Create stable variations for the variable demo product.
	 *
	 * @param int    $parent_id Parent product ID.
	 * @param string $parent_sku Parent SKU.
	 * @return void
	 */
	private function upsert_variations( int $parent_id, string $parent_sku ): void {
		$fixtures = array(
			array(
				'suffix' => 'ONX',
				'finish' => 'Onyx',
				'price'  => '219.00',
				'stock'  => 7,
			),
			array(
				'suffix' => 'STN',
				'finish' => 'Stone',
				'price'  => '229.00',
				'stock'  => 5,
			),
		);

		foreach ( $fixtures as $fixture ) {
			$sku       = $parent_sku . '-' . $fixture['suffix'];
			$id        = wc_get_product_id_by_sku( $sku );
			$variation = $id ? wc_get_product( $id ) : new WC_Product_Variation();

			if ( ! $variation instanceof WC_Product_Variation ) {
				continue;
			}

			$variation->set_parent_id( $parent_id );
			$variation->set_sku( $sku );
			$variation->set_attributes( array( 'finish' => $fixture['finish'] ) );
			$variation->set_regular_price( $fixture['price'] );
			$variation->set_manage_stock( true );
			$variation->set_stock_quantity( $fixture['stock'] );
			$variation->set_status( 'publish' );
			$variation->save();
		}

		WC_Product_Variable::sync( $parent_id );
	}

	/**
	 * Create operational and policy pages.
	 *
	 * @return void
	 */
	private function create_pages(): void {
		if ( class_exists( 'WC_Install' ) ) {
			\WC_Install::create_pages();
		}

		$pages = array(
			'home'                  => array(
				'title'   => 'Home',
				'content' => '<!-- wp:pattern {"slug":"northline-storefront/homepage"} /-->',
			),
			'faq'                   => array(
				'title'   => 'FAQ',
				'content' => '<!-- wp:heading --><h2>Is this a real store?</h2><!-- /wp:heading --><!-- wp:paragraph --><p>No. It is a fictional technical portfolio. Do not enter real personal or payment information.</p><!-- /wp:paragraph --><!-- wp:heading --><h2>What do the regional rules prove?</h2><!-- /wp:heading --><!-- wp:paragraph --><p>They demonstrate product metadata, server-side checkout validation, and auditable order snapshots. They do not establish legal compliance.</p><!-- /wp:paragraph -->',
			),
			'shipping-returns'      => array(
				'title'   => 'Shipping & Returns',
				'content' => '<!-- wp:paragraph --><p>All rates, delivery windows, cancellations, and refunds are fictional workflow examples. No item is shipped and no money is collected.</p><!-- /wp:paragraph -->',
			),
			'privacy-policy'        => array(
				'title'   => 'Privacy Policy',
				'content' => '<!-- wp:paragraph --><p>This disposable demo should only receive fictional test details. Repository fixtures use reserved invalid email domains and contain no real customer records.</p><!-- /wp:paragraph -->',
			),
			'classic-checkout-test' => array(
				'title'   => 'Classic Checkout Test',
				'content' => '<!-- wp:paragraph --><p><strong>Compatibility fixture:</strong> this page proves the regional rule on WooCommerce Classic Checkout as well as Checkout Block. Use fictional details only; no payment is collected.</p><!-- /wp:paragraph --><!-- wp:shortcode -->[woocommerce_checkout]<!-- /wp:shortcode -->',
			),
		);

		foreach ( $pages as $slug => $data ) {
			$page = get_page_by_path( $slug );
			$id   = wp_insert_post(
				array(
					'ID'           => $page ? $page->ID : 0,
					'post_title'   => $data['title'],
					'post_name'    => $slug,
					'post_content' => $data['content'],
					'post_status'  => 'publish',
					'post_type'    => 'page',
				),
				true
			);

			if ( is_wp_error( $id ) ) {
				WP_CLI::warning( $id->get_error_message() );
				continue;
			}

			if ( 'home' === $slug ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $id );
			}

			if ( 'privacy-policy' === $slug ) {
				update_option( 'wp_page_for_privacy_policy', $id );
			}
		}
	}

	/**
	 * Create a reusable percentage coupon.
	 *
	 * @return void
	 */
	private function create_coupon(): void {
		$coupon_id = wc_get_coupon_id_by_code( 'NORTHLINE10' );
		$coupon    = new WC_Coupon( $coupon_id > 0 ? $coupon_id : 0 );
		$coupon->set_code( 'NORTHLINE10' );
		$coupon->set_description( 'Fictional portfolio coupon.' );
		$coupon->set_discount_type( 'percent' );
		$coupon->set_amount( 10 );
		$coupon->set_individual_use( true );
		$coupon->set_usage_limit( 100 );
		$coupon->save();
	}

	/**
	 * Add flat-rate, free-shipping, and local-pickup examples.
	 *
	 * @return void
	 */
	private function configure_shipping(): void {
		$zone    = WC_Shipping_Zones::get_zone( 0 );
		$methods = $zone->get_shipping_methods( true );
		$present = array();

		foreach ( $methods as $method ) {
			$present[ $method->id ] = true;
		}

		if ( empty( $present['flat_rate'] ) ) {
			$instance = $zone->add_shipping_method( 'flat_rate' );
			update_option(
				'woocommerce_flat_rate_' . $instance . '_settings',
				array(
					'title'      => 'Standard demo shipping',
					'tax_status' => 'taxable',
					'cost'       => '12.00',
				)
			);
		}

		if ( empty( $present['free_shipping'] ) ) {
			$instance = $zone->add_shipping_method( 'free_shipping' );
			update_option(
				'woocommerce_free_shipping_' . $instance . '_settings',
				array(
					'title'      => 'Free demo shipping',
					'requires'   => 'min_amount',
					'min_amount' => '150',
				)
			);
		}

		if ( empty( $present['local_pickup'] ) ) {
			$instance = $zone->add_shipping_method( 'local_pickup' );
			update_option(
				'woocommerce_local_pickup_' . $instance . '_settings',
				array(
					'title'      => 'Northline lab pickup',
					'tax_status' => 'taxable',
					'cost'       => '0',
				)
			);
		}
	}

	/**
	 * Create fictional operational accounts for disposable environments.
	 *
	 * @return void
	 */
	private function create_demo_users(): void {
		$users = array(
			array(
				'login' => 'store-manager',
				'email' => 'store-manager@northline.invalid',
				'role'  => 'shop_manager',
			),
			array(
				'login' => 'demo-customer',
				'email' => 'customer@northline.invalid',
				'role'  => 'customer',
			),
		);

		foreach ( $users as $fixture ) {
			$user = get_user_by( 'login', $fixture['login'] );
			if ( ! $user ) {
				$id = wp_create_user( $fixture['login'], 'northline-demo', $fixture['email'] );
				if ( is_wp_error( $id ) ) {
					WP_CLI::warning( $id->get_error_message() );
					continue;
				}
				$user = get_user_by( 'id', $id );
			}

			if ( $user ) {
				$user->set_role( $fixture['role'] );
			}
		}
	}
}
