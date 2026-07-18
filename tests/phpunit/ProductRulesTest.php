<?php
/**
 * Product rule unit tests.
 */

declare(strict_types=1);

use Northline\CommerceRules\Product_Rules;
use PHPUnit\Framework\TestCase;

final class ProductRulesTest extends TestCase {

    public function test_region_codes_are_normalized_and_deduplicated(): void {
        self::assertSame(
            array( 'BC', 'AB', 'ON' ),
            Product_Rules::sanitize_regions( 'bc, AB, invalid, ON, bc' )
        );
    }

    public function test_unknown_region_codes_are_rejected(): void {
        self::assertSame( array(), Product_Rules::sanitize_regions( 'WA, CA, XX' ) );
    }

    public function test_array_input_is_supported_for_future_integrations(): void {
        self::assertSame(
            array( 'QC', 'NU' ),
            Product_Rules::sanitize_regions( array( 'qc', 'nu' ) )
        );
    }
}

