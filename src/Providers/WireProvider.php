<?php
/**
 * Wire provider — news wire aggregation (wire.extrachill.com).
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
 *   - search-wire-items   → cross-site REST to wire.extrachill.com /wp/v2/posts
 *   - get-wire-item       → ec_cross_site_rest_request() to wire subsite
 *   - get-timeline        → recent wire items, date-filtered
 *   - list-festivals      → festival taxonomy on wire CPT
 */
final class WireProvider extends AbstractStubProvider {

	public function slug(): string {
		return 'wire';
	}

	public function description(): string {
		return __( 'News wire aggregation from wire.extrachill.com. Fast-moving music industry news items with festival tagging.', 'extrachill-mcp' );
	}

	protected function ability_category(): string {
		return AbilityCategories::WIRE;
	}

	public function tools(): array {
		return array(
			'search-wire-items' => array(
				'description'   => __( 'Search news wire items by keyword, festival, or date range.', 'extrachill-mcp' ),
				'input_schema'  => array(
					'type'       => 'object',
					'properties' => array(
						'q'        => array( 'type' => 'string' ),
						'festival' => array( 'type' => 'string' ),
						'days'     => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 90, 'default' => 30 ),
						'limit'    => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 50, 'default' => 10 ),
					),
				),
			),
			'get-wire-item'     => array(
				'description'   => __( 'Get a single news wire item by ID.', 'extrachill-mcp' ),
				'input_schema'  => array(
					'type'       => 'object',
					'required'   => array( 'id' ),
					'properties' => array(
						'id' => array( 'type' => 'integer' ),
					),
				),
			),
			'get-timeline'      => array(
				'description'   => __( 'Recent wire items from the last N days.', 'extrachill-mcp' ),
				'input_schema'  => array(
					'type'       => 'object',
					'properties' => array(
						'days'  => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 30, 'default' => 7 ),
						'limit' => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 50, 'default' => 20 ),
					),
				),
			),
			'list-festivals'    => array(
				'description'   => __( 'List festivals tracked by the wire.', 'extrachill-mcp' ),
				'input_schema'  => array( 'type' => 'object' ),
			),
		);
	}
}
