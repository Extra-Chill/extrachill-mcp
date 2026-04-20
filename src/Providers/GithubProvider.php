<?php
/**
 * GitHub provider — Extra-Chill/* org repos (hardcoded allowlist).
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
 *   - search-issues   → GitHub search API, scoped to Extra-Chill/* allowlist
 *   - get-issue       → single issue + comments
 *   - get-pr          → single PR + review state
 *   - get-timeline    → cross-repo activity stream
 *   - list-repos      → the hardcoded allowlist, hydrated with metadata
 *   - get-release     → tagged release notes
 *
 * Repo allowlist lives in config/github-repos.php, filterable via
 * `extrachill_mcp_github_repos`. GITHUB_TOKEN env var used for auth when
 * present to bump rate limits.
 *
 * Mirrors the `github` provider in Automattic/mcp-context-wporg.
 */
final class GithubProvider extends AbstractStubProvider {

	public function slug(): string {
		return 'github';
	}

	public function description(): string {
		return __( 'Extra-Chill GitHub organization — issues, pull requests, releases, and cross-repo activity across the Extra Chill platform codebase.', 'extrachill-mcp' );
	}

	protected function ability_category(): string {
		return AbilityCategories::GITHUB;
	}

	public function tools(): array {
		return array(
			'search-issues' => array(
				'description'  => __( 'Search issues/PRs across tracked Extra-Chill/* repos.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'q'     => array( 'type' => 'string' ),
						'repo'  => array( 'type' => 'string', 'description' => __( 'Restrict to a single repo (name only, no org).', 'extrachill-mcp' ) ),
						'state' => array( 'type' => 'string', 'enum' => array( 'open', 'closed', 'all' ), 'default' => 'open' ),
						'label' => array( 'type' => 'string' ),
						'limit' => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 50, 'default' => 10 ),
					),
				),
			),
			'get-issue'     => array(
				'description'  => __( 'Get a single issue with comments.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'required'   => array( 'repo', 'number' ),
					'properties' => array(
						'repo'   => array( 'type' => 'string' ),
						'number' => array( 'type' => 'integer' ),
					),
				),
			),
			'get-pr'        => array(
				'description'  => __( 'Get a single pull request with review state.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'required'   => array( 'repo', 'number' ),
					'properties' => array(
						'repo'   => array( 'type' => 'string' ),
						'number' => array( 'type' => 'integer' ),
					),
				),
			),
			'get-timeline'  => array(
				'description'  => __( 'Cross-repo activity stream across tracked repos.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'days'  => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 30, 'default' => 7 ),
						'limit' => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 50, 'default' => 25 ),
					),
				),
			),
			'list-repos'    => array(
				'description'  => __( 'Tracked Extra-Chill/* repos with basic metadata.', 'extrachill-mcp' ),
				'input_schema' => array( 'type' => 'object' ),
			),
			'get-release'   => array(
				'description'  => __( 'Fetch a tagged release for a repo.', 'extrachill-mcp' ),
				'input_schema' => array(
					'type'       => 'object',
					'required'   => array( 'repo' ),
					'properties' => array(
						'repo' => array( 'type' => 'string' ),
						'tag'  => array( 'type' => 'string', 'description' => __( 'Release tag. Omit for latest.', 'extrachill-mcp' ) ),
					),
				),
			),
		);
	}
}
