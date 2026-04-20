<?php
/**
 * Ability category registration for Extra Chill MCP.
 *
 * @package ExtraChillMcp
 */

declare( strict_types = 1 );

namespace ExtraChillMcp;

defined( 'ABSPATH' ) || exit;

/**
 * Registers `extrachill-mcp/*` ability categories.
 *
 * Each provider gets its own category so abilities are organized
 * in the Abilities admin UI and `wp help abilities` listings.
 */
final class AbilityCategories {

	public const META       = 'extrachill-mcp/meta';
	public const EDITORIAL  = 'extrachill-mcp/editorial';
	public const WIRE       = 'extrachill-mcp/wire';
	public const DOCS       = 'extrachill-mcp/docs';
	public const ARTISTS    = 'extrachill-mcp/artists';
	public const EVENTS     = 'extrachill-mcp/events';
	public const COMMUNITY  = 'extrachill-mcp/community';
	public const SHOP       = 'extrachill-mcp/shop';
	public const GITHUB     = 'extrachill-mcp/github';

	private static bool $registered = false;

	/**
	 * Ensure categories are registered — safe to call at any time.
	 */
	public static function ensure_registered(): void {
		if ( self::$registered ) {
			return;
		}

		if ( did_action( 'wp_abilities_api_categories_init' ) ) {
			self::register();
		} else {
			add_action( 'wp_abilities_api_categories_init', array( self::class, 'register' ) );
		}
	}

	/**
	 * Register all Extra Chill MCP ability categories.
	 */
	public static function register(): void {
		if ( self::$registered ) {
			return;
		}

		$categories = array(
			self::META      => array(
				'label'       => __( 'MCP Meta', 'extrachill-mcp' ),
				'description' => __( 'Meta-tools for provider discovery and tool dispatch.', 'extrachill-mcp' ),
			),
			self::EDITORIAL => array(
				'label'       => __( 'MCP: Editorial', 'extrachill-mcp' ),
				'description' => __( 'Posts, authors, tags, categories on extrachill.com.', 'extrachill-mcp' ),
			),
			self::WIRE      => array(
				'label'       => __( 'MCP: Wire', 'extrachill-mcp' ),
				'description' => __( 'News wire aggregation (wire.extrachill.com).', 'extrachill-mcp' ),
			),
			self::DOCS      => array(
				'label'       => __( 'MCP: Docs', 'extrachill-mcp' ),
				'description' => __( 'Documentation hub (docs.extrachill.com).', 'extrachill-mcp' ),
			),
			self::ARTISTS   => array(
				'label'       => __( 'MCP: Artists', 'extrachill-mcp' ),
				'description' => __( 'Artist profiles + link pages (artist.extrachill.com).', 'extrachill-mcp' ),
			),
			self::EVENTS    => array(
				'label'       => __( 'MCP: Events', 'extrachill-mcp' ),
				'description' => __( 'Shows, festivals, venues (events.extrachill.com).', 'extrachill-mcp' ),
			),
			self::COMMUNITY => array(
				'label'       => __( 'MCP: Community', 'extrachill-mcp' ),
				'description' => __( 'Forums (community.extrachill.com).', 'extrachill-mcp' ),
			),
			self::SHOP      => array(
				'label'       => __( 'MCP: Shop', 'extrachill-mcp' ),
				'description' => __( 'Merch + music (shop.extrachill.com).', 'extrachill-mcp' ),
			),
			self::GITHUB    => array(
				'label'       => __( 'MCP: GitHub', 'extrachill-mcp' ),
				'description' => __( 'Extra-Chill/* GitHub org — issues, PRs, releases.', 'extrachill-mcp' ),
			),
		);

		foreach ( $categories as $slug => $args ) {
			wp_register_ability_category( $slug, $args );
		}

		self::$registered = true;
	}
}
