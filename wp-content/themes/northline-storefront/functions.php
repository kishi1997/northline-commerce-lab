<?php
/**
 * Northline Storefront setup.
 *
 * @package Northline_Storefront
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Configure theme supports.
 *
 * @return void
 */
function northline_storefront_setup(): void {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
}
add_action( 'after_setup_theme', 'northline_storefront_setup' );

/**
 * Load the public stylesheet.
 *
 * @return void
 */
function northline_storefront_assets(): void {
	wp_enqueue_style(
		'northline-storefront',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'northline_storefront_assets' );

/**
 * Register the theme's pattern category.
 *
 * @return void
 */
function northline_storefront_patterns(): void {
	register_block_pattern_category(
		'northline-commerce',
		array( 'label' => __( 'Northline Commerce', 'northline-storefront' ) )
	);
}
add_action( 'init', 'northline_storefront_patterns' );

/**
 * Surface operational stock states in product loops.
 *
 * @return void
 */
function northline_storefront_loop_stock(): void {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$class = $product->is_in_stock() ? 'nl-stock-status' : 'nl-stock-status nl-stock-status--out';
	$label = $product->is_in_stock()
		? sprintf(
			/* translators: %d: units remaining. */
			_n( '%d in stock', '%d in stock', (int) $product->get_stock_quantity(), 'northline-storefront' ),
			(int) $product->get_stock_quantity()
		)
		: __( 'Out of stock', 'northline-storefront' );

	printf( '<p class="%1$s">%2$s</p>', esc_attr( $class ), esc_html( $label ) );
}
add_action( 'woocommerce_after_shop_loop_item_title', 'northline_storefront_loop_stock', 11 );
