<?php
/**
 * Title: Northline editorial commerce homepage
 * Slug: northline-storefront/homepage
 * Categories: northline-commerce, featured
 * Inserter: true
 *
 * @package Northline_Storefront
 */

?>
<!-- wp:html -->
	<section class="nl-hero" aria-labelledby="northline-hero-title">
	<div class="nl-hero__index" aria-hidden="true">NL / 001</div>
	<div class="nl-hero__copy">
		<h1 class="nl-display-title" id="northline-hero-title">Equipment,<br>clearly<br>considered.</h1>
		<div class="nl-hero__specs">
		<p>Engineered for control<br>Materials documented<br>Demo warranty only</p>
		<p>Designed in-house<br>Fictional catalogue<br>No real transactions</p>
		</div>
		<div class="wp-block-button nl-button"><a class="wp-block-button__link wp-element-button" href="/shop/">Explore the catalogue →</a></div>
	</div>
	<div class="nl-hero__visual" aria-hidden="true"></div>
	<div class="nl-hero__vertical-note" aria-hidden="true">Precision / Simplicity</div>
	</section>
<!-- /wp:html -->

<!-- wp:group {"align":"full","className":"nl-catalogue","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull nl-catalogue"><!-- wp:shortcode -->
[product_categories number="4" columns="4" hide_empty="0" parent="0" orderby="id" order="ASC"]
<!-- /wp:shortcode --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"nl-featured","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull nl-featured"><!-- wp:html -->
<div class="nl-featured__intro"><p>Featured / 01—03</p><h2>Selected<br>equipment</h2><p>Fictional pieces selected to demonstrate product, stock, and variation workflows.</p><p><a href="/shop/">Browse all devices →</a></p></div>
<!-- /wp:html -->
<!-- wp:shortcode -->
[products limit="3" columns="3" visibility="featured" orderby="menu_order"]
<!-- /wp:shortcode --></div>
<!-- /wp:group -->

<!-- wp:html -->
	<section class="nl-responsibility" aria-labelledby="responsibility-title">
	<div class="nl-responsibility__title"><h2 id="responsibility-title">Responsible<br>retail demo</h2><p>Technical portfolio only. No real transaction or identity verification occurs.</p></div>
	<div class="nl-responsibility__item"><h3>Shipping simulation</h3><p>Rates and free-delivery thresholds demonstrate WooCommerce configuration.</p><a href="/shipping-returns/">Learn more →</a></div>
	<div class="nl-responsibility__item"><h3>Restricted regions</h3><p>Checkout is stopped by server-side product and destination rules.</p><a href="/faq/">View rules →</a></div>
	<div class="nl-responsibility__item"><h3>Test checkout</h3><p>Use fictional details only. Payment is disabled and no order is fulfilled.</p><a href="/checkout/">View test info →</a></div>
	</section>
	<section class="nl-editorial-banner" aria-labelledby="function-first-title">
	<div class="nl-editorial-banner__copy"><p>Built to perform</p><h2 id="function-first-title">Function first.<br>Every time.</h2><p>Durable patterns. Precise rules. Minimal dependencies.</p><div class="wp-block-button nl-button"><a class="wp-block-button__link wp-element-button" href="/shop/">Explore the catalogue →</a></div></div>
	<div class="nl-editorial-banner__object" aria-hidden="true"></div>
	</section>
<!-- /wp:html -->
