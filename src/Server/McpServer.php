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
 * Exposes the two meta-tools (load-provider, execute-tool) as the primary
 * MCP surface. Individual provider abilities are also exposed so clients
 * that prefer flat tool lists can still reach them directly.
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
		// Build the ability list: meta-tools plus every provider tool.
		$abilities = array(
			'extrachill-mcp/load-provider',
			'extrachill-mcp/execute-tool',
		);

		foreach ( $this->registry->all() as $provider ) {
			foreach ( array_keys( $provider->tools() ) as $tool_name ) {
				$abilities[] = 'extrachill-mcp/' . $provider->slug() . '-' . $tool_name;
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
