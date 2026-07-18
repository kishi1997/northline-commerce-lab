<?php
/**
 * Plugin composition root.
 *
 * @package Northline_Commerce_Rules
 */

declare(strict_types=1);

namespace Northline\CommerceRules;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the plugin services.
 */
final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return Plugin
	 */
	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register all services.
	 *
	 * @return void
	 */
	public function register(): void {
		( new Product_Rules() )->register();
		( new Checkout_Validator() )->register();
		( new Order_Snapshot() )->register();
	}
}
