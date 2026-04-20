<?php
/**
 * Events provider — shows, festivals, venues (events.extrachill.com).
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
 *   - search-events     → delegate to data-machine-events/get-upcoming-counts etc.
 *   - get-event         → event + artists + venue joined
 *   - get-timeline      → upcoming N days
 *   - list-venues       → venue directory
 *   - list-festivals    → festival directory
 *
 * Heavy reuse of existing Data Machine Events abilities.
 */
final class EventsProvider extends AbstractStubProvider {

	public function slug(): string {
		return 'events';
	}

	public function description(): string {
		return __( 'Live music events, shows, festivals, and venues (events.extrachill.com). Optimized for queries like "what shows are happening in Austin this week" or "next 5 dates for this artist".', 'extrachill-mcp' );
	}

	protected function ability_category(): string {
		return AbilityCategories::EVENTS;
	}

	public function tools(): array {
		return array(
			'search-events'  => array(
				'description'  => __( 'Search shows/festivals with filters for date range, location, venue, or artist.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'q'         => array( 'type' => 'string' ),
						'date_from' => array( 'type' => 'string', 'format' => 'date' ),
						'date_to'   => array( 'type' => 'string', 'format' => 'date' ),
						'location'  => array( 'type' => 'string' ),
						'venue'     => array( 'type' => 'string' ),
						'artist'    => array( 'type' => 'string' ),
						'limit'     => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 50, 'default' => 20 ),
					),
				),
			),
			'get-event'      => array(
				'description'  => __( 'Get full event detail including lineup and venue.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'required'   => array( 'id' ),
					'properties' => array(
						'id' => array( 'type' => 'integer' ),
					),
				),
			),
			'get-timeline'   => array(
				'description'  => __( 'Upcoming shows over the next N days.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'days'  => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 365, 'default' => 14 ),
						'limit' => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 25 ),
					),
				),
			),
			'list-venues'    => array(
				'description'  => __( 'Venue directory with upcoming event counts.', 'extrachill-mcp' ),
				'input_schema' => array( 'type' => 'object' ),
			),
			'list-festivals' => array(
				'description'  => __( 'Festival directory with upcoming event counts.', 'extrachill-mcp' ),
				'input_schema' => array( 'type' => 'object' ),
			),
		);
	}
}
