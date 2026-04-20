<?php
/**
 * Main plugin bootstrap.
 *
 * @package ExtraChillMcp
 */

declare( strict_types = 1 );

namespace ExtraChillMcp;

use ExtraChillMcp\Providers\ArtistsProvider;
use ExtraChillMcp\Providers\CommunityProvider;
use ExtraChillMcp\Providers\DocsProvider;
use ExtraChillMcp\Providers\EditorialProvider;
use ExtraChillMcp\Providers\EventsProvider;
use ExtraChillMcp\Providers\GithubProvider;
use ExtraChillMcp\Providers\ShopProvider;
use ExtraChillMcp\Providers\WireProvider;
use ExtraChillMcp\Registry\ProviderRegistry;
use ExtraChillMcp\Server\McpServer;
use ExtraChillMcp\Server\MetaTools;

defined( 'ABSPATH' ) || exit;

/**
 * Boot class — single entry point.
 */
final class Plugin {

	private static bool $booted = false;

	/**
	 * Boot the plugin once.
	 */
	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		// 1. Register ability categories.
		AbilityCategories::ensure_registered();

		// 2. Build provider registry.
		$registry = self::build_registry();

		// 3. Register meta-tools (load-provider / execute-tool) as abilities.
		( new MetaTools( $registry ) )->register();

		// 4. Register per-provider abilities (tool catalogs + dispatchers).
		foreach ( $registry->all() as $provider ) {
			$provider->register_abilities();
		}

		// 5. Spin up MCP server on mcp_adapter_init.
		( new McpServer( $registry ) )->register();
	}

	/**
	 * Build the provider registry with all 8 providers.
	 *
	 * v1 providers: editorial, wire, docs, artists, events, community, shop, github.
	 * Only editorial is fully implemented. Others are stubs with tool schemas
	 * defined but execute callbacks returning not-implemented errors.
	 */
	private static function build_registry(): ProviderRegistry {
		$registry = new ProviderRegistry();
		$registry->register( new EditorialProvider() );
		$registry->register( new WireProvider() );
		$registry->register( new DocsProvider() );
		$registry->register( new ArtistsProvider() );
		$registry->register( new EventsProvider() );
		$registry->register( new CommunityProvider() );
		$registry->register( new ShopProvider() );
		$registry->register( new GithubProvider() );

		/**
		 * Filter registered providers.
		 *
		 * Allows external plugins to add providers (e.g. private analytics
		 * provider ships as a separate plugin and hooks in here).
		 *
		 * @param ProviderRegistry $registry The provider registry.
		 */
		do_action( 'extrachill_mcp_register_providers', $registry );

		return $registry;
	}
}
