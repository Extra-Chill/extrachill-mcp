<?php
/**
 * Docs provider — docs.extrachill.com.
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
 *   - search-docs    → cross-site REST to docs.extrachill.com
 *   - get-doc        → fetch by slug, include hierarchy breadcrumb
 *   - get-timeline   → recently updated docs (order by modified)
 *   - list-sections  → top-level doc hierarchy
 */
final class DocsProvider extends AbstractStubProvider {

	public function slug(): string {
		return 'docs';
	}

	public function description(): string {
		return __( 'Extra Chill documentation (docs.extrachill.com) — developer docs, platform guides, and internal references.', 'extrachill-mcp' );
	}

	protected function ability_category(): string {
		return AbilityCategories::DOCS;
	}

	public function tools(): array {
		return array(
			'search-docs'   => array(
				'description'  => __( 'Full-text search docs.extrachill.com.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'q'     => array( 'type' => 'string' ),
						'limit' => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 50, 'default' => 10 ),
					),
				),
			),
			'get-doc'       => array(
				'description'  => __( 'Fetch a single doc by slug, including content + breadcrumb.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'required'   => array( 'slug' ),
					'properties' => array(
						'slug' => array( 'type' => 'string' ),
					),
				),
			),
			'get-timeline'  => array(
				'description'  => __( 'Recently updated docs.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'limit' => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 50, 'default' => 20 ),
					),
				),
			),
			'list-sections' => array(
				'description'  => __( 'Top-level documentation sections.', 'extrachill-mcp' ),
				'input_schema' => array( 'type' => 'object' ),
			),
		);
	}
}
