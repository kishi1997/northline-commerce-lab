<?php
/**
 * Minimal isolated bootstrap for pure metadata-policy tests.
 */

declare(strict_types=1);

define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );

if ( ! function_exists( 'sanitize_key' ) ) {
    function sanitize_key( string $key ): string {
        return strtolower( preg_replace( '/[^a-zA-Z0-9_-]/', '', $key ) ?? '' );
    }
}

if ( ! function_exists( 'apply_filters' ) ) {
    function apply_filters( string $hook_name, mixed $value, mixed ...$args ): mixed {
        unset( $hook_name, $args );
        return $value;
    }
}

require_once dirname( __DIR__, 2 ) . '/wp-content/plugins/northline-commerce-rules/includes/class-product-rules.php';

