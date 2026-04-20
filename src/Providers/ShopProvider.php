<?php
/**
 * Shop provider — shop.extrachill.com.
 *
 * @package ExtraChillMcp
 */

declare( strict_types = 1 );

namespace ExtraChillMcp\Providers;

use ExtraChillMcp\AbilityCategories;

defined( 'ABSPATH' ) || exit;

/**
 * STUB — Phase 1.
 *
 * Phase 1 implementation plan:
 *   - search-products   → WooCommerce product search via REST
 *   - get-product       → product + variants + stock
 *   - get-timeline      → recently added products
 *   - list-categories   → WooCommerce product categories
 */
final class ShopProvider extends AbstractStubProvider {

	public function slug(): string {
		return 'shop';
	}

	public function description(): string {
		return __( 'Extra Chill Shop merchandise and music (shop.extrachill.com). WooCommerce-backed catalog with Stripe Connect marketplace artists.', 'extrachill-mcp' );
	}

	protected function ability_category(): string {
		return AbilityCategories::SHOP;
	}

	public function tools(): array {
		return array(
			'search-products'  => array(
				'description'  => __( 'Search shop products by keyword, category, or artist.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'q'        => array( 'type' => 'string' ),
						'category' => array( 'type' => 'string' ),
						'artist'   => array( 'type' => 'string' ),
						'limit'    => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 50, 'default' => 10 ),
					),
				),
			),
			'get-product'      => array(
				'description'  => __( 'Fetch a product with variants and stock info.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'slug' => array( 'type' => 'string' ),
						'id'   => array( 'type' => 'integer' ),
					),
				),
			),
			'get-timeline'     => array(
				'description'  => __( 'Recently added products.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'limit' => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 50, 'default' => 20 ),
					),
				),
			),
			'list-categories'  => array(
				'description'  => __( 'Product categories.', 'extrachill-mcp' ),
				'input_schema' => array( 'type' => 'object' ),
			),
		);
	}
}
