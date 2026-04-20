<?php
/**
 * Artists provider — artist profiles + link pages (artist.extrachill.com).
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
 *   - search-artists  → extrachill CLI wraps this, route-affinity to artist.extrachill.com
 *   - get-artist      → profile + link page + recent editorial coverage joined
 *   - get-timeline    → recently updated artist profiles
 *   - list-genres     → genre taxonomy
 *
 * Reference existing abilities:
 *   - wp extrachill artists search ...
 *   - Artist platform REST under /extrachill/v1/artists/
 */
final class ArtistsProvider extends AbstractStubProvider {

	public function slug(): string {
		return 'artists';
	}

	public function description(): string {
		return __( 'Artist profiles and link pages on artist.extrachill.com. Returns profile metadata, link page stats, and joined editorial coverage from extrachill.com.', 'extrachill-mcp' );
	}

	protected function ability_category(): string {
		return AbilityCategories::ARTISTS;
	}

	public function tools(): array {
		return array(
			'search-artists' => array(
				'description'  => __( 'Search artist profiles by name, genre, or location.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'q'        => array( 'type' => 'string' ),
						'genre'    => array( 'type' => 'string' ),
						'location' => array( 'type' => 'string' ),
						'limit'    => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 50, 'default' => 10 ),
					),
				),
			),
			'get-artist'     => array(
				'description'  => __( 'Get a single artist profile with link page and recent editorial coverage.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'slug' => array( 'type' => 'string' ),
						'id'   => array( 'type' => 'integer' ),
					),
				),
			),
			'get-timeline'   => array(
				'description'  => __( 'Recently updated artist profiles.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'days'  => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 90, 'default' => 30 ),
						'limit' => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 50, 'default' => 20 ),
					),
				),
			),
			'list-genres'    => array(
				'description'  => __( 'Genres represented on the artist platform.', 'extrachill-mcp' ),
				'input_schema' => array( 'type' => 'object' ),
			),
		);
	}
}
