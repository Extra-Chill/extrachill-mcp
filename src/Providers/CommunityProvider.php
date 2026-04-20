<?php
/**
 * Community provider — forums (community.extrachill.com).
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
 *   - search-threads  → existing community search endpoint
 *   - get-thread      → topic + replies
 *   - get-timeline    → recent activity
 *   - list-forums     → forum structure (bbPress)
 *
 * Reference existing REST: /datamachine/v1/community/ (GET forums, notifications)
 */
final class CommunityProvider extends AbstractStubProvider {

	public function slug(): string {
		return 'community';
	}

	public function description(): string {
		return __( 'Extra Chill Community forums (community.extrachill.com). Discussion threads, replies, and forum structure for the music community.', 'extrachill-mcp' );
	}

	protected function ability_category(): string {
		return AbilityCategories::COMMUNITY;
	}

	public function tools(): array {
		return array(
			'search-threads' => array(
				'description'  => __( 'Search forum threads by keyword and forum.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'q'     => array( 'type' => 'string' ),
						'forum' => array( 'type' => 'string' ),
						'limit' => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 50, 'default' => 10 ),
					),
				),
			),
			'get-thread'     => array(
				'description'  => __( 'Fetch a full thread with replies.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'required'   => array( 'id' ),
					'properties' => array(
						'id'            => array( 'type' => 'integer' ),
						'include_replies' => array( 'type' => 'boolean', 'default' => true ),
					),
				),
			),
			'get-timeline'   => array(
				'description'  => __( 'Recent forum activity.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'days'  => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 30, 'default' => 7 ),
						'limit' => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 50, 'default' => 20 ),
					),
				),
			),
			'list-forums'    => array(
				'description'  => __( 'Top-level forum structure.', 'extrachill-mcp' ),
				'input_schema' => array( 'type' => 'object' ),
			),
		);
	}
}
