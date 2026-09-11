<?php
/**
 * MCP server registration via wordpress/mcp-adapter.
 *
 * @package ExtraChillMcp
 */

declare( strict_types = 1 );

namespace ExtraChillMcp\Server;

use ExtraChillMcp\Registry\ProviderRegistry;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the Extra Chill MCP server with mcp-adapter.
 *
 * Public endpoint: /wp-json/extrachill-mcp/v1/mcp
 *
 * Exposes exactly two meta-tools (load-provider, execute-tool) as the MCP
 * surface. Provider tools are reached through execute-tool rather than being
 * advertised individually, so the tool count stays flat as providers grow.
 * Opt into flat advertising with the `extrachill_mcp_advertise_flat_tools`
 * filter.
 */
final class McpServer {

	public function __construct( private ProviderRegistry $registry ) {}

	public function register(): void {
		add_action( 'mcp_adapter_init', array( $this, 'on_mcp_adapter_init' ) );
	}

	/**
	 * @param \WP\MCP\Core\McpAdapter $adapter
	 */
	public function on_mcp_adapter_init( $adapter ): void {
		// The meta-tools are the entire advertised surface. Two tools, regardless
		// of how many providers exist — that is the point of the pattern.
		$abilities = array(
			'extrachill-mcp/load-provider',
			'extrachill-mcp/execute-tool',
		);

		/**
		 * Filters whether every provider tool is also advertised as a flat tool.
		 *
		 * Off by default. Enabling it advertises 2 + N tools instead of 2, which
		 * grows without bound as providers are implemented and reintroduces the
		 * context bloat the meta-tool pattern exists to avoid. Useful only for a
		 * client that cannot do the two-step load-provider / execute-tool dance.
		 *
		 * Provider tools remain fully reachable via execute-tool either way.
		 *
		 * @param bool $flat Whether to advertise provider tools individually.
		 */
		if ( apply_filters( 'extrachill_mcp_advertise_flat_tools', false ) ) {
			foreach ( $this->registry->all() as $provider ) {
				foreach ( array_keys( $provider->tools() ) as $tool_name ) {
					$abilities[] = 'extrachill-mcp/' . $provider->slug() . '-' . $tool_name;
				}
			}
		}

		$adapter->create_server(
			'extrachill-mcp',
			'extrachill-mcp/v1',
			'mcp',
			__( 'Extra Chill MCP', 'extrachill-mcp' ),
			__( 'MCP context server for the Extra Chill platform — music publication, artists, events, community, shop, and the Extra-Chill GitHub org.', 'extrachill-mcp' ),
			EXTRACHILL_MCP_VERSION,
			array( \WP\MCP\Transport\HttpTransport::class ),
			\WP\MCP\Infrastructure\ErrorHandling\ErrorLogMcpErrorHandler::class,
			\WP\MCP\Infrastructure\Observability\NullMcpObservabilityHandler::class,
			$abilities,
			array(), // resources
			array()  // prompts
		);
	}
}
