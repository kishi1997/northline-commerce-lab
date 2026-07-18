<?php
/**
 * Plugin Name: Northline Commerce Rules
 * Plugin URI: https://example.invalid/northline-commerce-lab
 * Description: Demonstrates auditable product notices and destination rules for a fictional WooCommerce store.
 * Version: 0.1.0
 * Requires at least: 7.0
 * Requires PHP: 8.3
 * WC requires at least: 10.9
 * WC tested up to: 10.9
 * Author: Northline Commerce Lab
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: northline-commerce-rules
 *
 * @package Northline_Commerce_Rules
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NORTHLINE_RULES_VERSION', '0.1.0' );
define( 'NORTHLINE_RULES_FILE', __FILE__ );
define( 'NORTHLINE_RULES_PATH', plugin_dir_path( __FILE__ ) );

require_once NORTHLINE_RULES_PATH . 'includes/class-product-rules.php';
require_once NORTHLINE_RULES_PATH . 'includes/class-checkout-validator.php';
require_once NORTHLINE_RULES_PATH . 'includes/class-order-snapshot.php';
require_once NORTHLINE_RULES_PATH . 'includes/class-plugin.php';

/**
 * Declare compatibility before WooCommerce initializes.
 *
 * @return void
 */
function northline_rules_declare_compatibility(): void {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'custom_order_tables',
			NORTHLINE_RULES_FILE,
			true
		);
	}
}
add_action( 'before_woocommerce_init', 'northline_rules_declare_compatibility' );

/**
 * Start the plugin after all dependencies are available.
 *
 * @return void
 */
function northline_rules_boot(): void {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action(
			'admin_notices',
			static function (): void {
				if ( current_user_can( 'activate_plugins' ) ) {
					echo '<div class="notice notice-error"><p>' . esc_html__( 'Northline Commerce Rules requires WooCommerce.', 'northline-commerce-rules' ) . '</p></div>';
				}
			}
		);
		return;
	}

	Northline\CommerceRules\Plugin::instance()->register();

	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		require_once NORTHLINE_RULES_PATH . 'includes/class-seed-command.php';
		WP_CLI::add_command( 'northline', 'Northline\CommerceRules\Seed_Command' );
	}
}
add_action( 'plugins_loaded', 'northline_rules_boot', 20 );
