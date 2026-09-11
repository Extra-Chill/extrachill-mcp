<?php
/**
 * MCP meta-tools: load-provider + execute-tool.
 *
 * @package ExtraChillMcp
 */

declare( strict_types = 1 );

namespace ExtraChillMcp\Server;

use ExtraChillMcp\AbilityCategories;
use ExtraChillMcp\Registry\ProviderRegistry;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the two meta-tools that form the MCP surface.
 *
 * The LLM sees two tools only:
 *   - extrachill-mcp/load-provider
 *   - extrachill-mcp/execute-tool
 *
 * Everything else is reached by calling these with a provider slug
 * and/or a tool name. This keeps the client's context window lean
 * no matter how many providers/tools we add.
 */
final class MetaTools {

	public function __construct( private ProviderRegistry $registry ) {}

	/**
	 * Register both meta-tools as abilities on the Abilities API init hook.
	 */
	public function register(): void {
		$register = function (): void {
			$this->register_load_provider();
			$this->register_execute_tool();
		};

		if ( did_action( 'wp_abilities_api_init' ) ) {
			$register();
		} else {
			add_action( 'wp_abilities_api_init', $register );
		}
	}

	/**
	 * load-provider: returns the tool catalog for a provider, or the
	 * list of all providers if no slug is passed.
	 */
	private function register_load_provider(): void {
		wp_register_ability(
			'extrachill-mcp/load-provider',
			array(
				'label'               => __( 'Load MCP Provider', 'extrachill-mcp' ),
				'description'         => __( "Load an Extra Chill MCP provider and return its tool catalog. Call without 'provider' to list all available providers. Each provider groups tools covering one slice of the Extra Chill platform (editorial, artists, events, github, etc.).", 'extrachill-mcp' ),
				'category'            => AbilityCategories::META,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'provider' => array(
							'type'        => 'string',
							'description' => __( 'Provider slug to load. Omit to list all providers.', 'extrachill-mcp' ),
						),
					),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'providers' => array(
							'type'        => 'array',
							'description' => __( 'Available providers (only present when no provider slug is given).', 'extrachill-mcp' ),
							'items'       => array(
								'type'       => 'object',
								'properties' => array(
									'slug'        => array( 'type' => 'string' ),
									'description' => array( 'type' => 'string' ),
									'tools'       => array(
										'type'  => 'array',
										'items' => array( 'type' => 'string' ),
									),
								),
							),
						),
						'provider'  => array( 'type' => 'string' ),
						'tools'     => array(
							'type'                 => 'object',
							'description'          => __( 'Tool catalog for the loaded provider: tool name => schema.', 'extrachill-mcp' ),
							'additionalProperties' => true,
						),
					),
				),
				'execute_callback'    => array( $this, 'execute_load_provider' ),
				'permission_callback' => '__return_true',
				'meta'                => array(
					'show_in_rest' => true,
					'annotations'  => array(
						'readonly'   => true,
						'idempotent' => true,
					),
					// Off the adapter's default server; reached via our own server only.
					'mcp'          => array( 'public' => false ),
				),
			)
		);
	}

	/**
	 * execute-tool: dispatches a call to a provider's tool.
	 */
	private function register_execute_tool(): void {
		wp_register_ability(
			'extrachill-mcp/execute-tool',
			array(
				'label'               => __( 'Execute MCP Tool', 'extrachill-mcp' ),
				'description'         => __( "Execute a tool within an Extra Chill MCP provider. Use load-provider first to discover available providers and tools. Arguments must match the tool's input schema.", 'extrachill-mcp' ),
				'category'            => AbilityCategories::META,
				'input_schema'        => array(
					'type'       => 'object',
					'required'   => array( 'provider', 'tool' ),
					'properties' => array(
						'provider'  => array(
							'type'        => 'string',
							'description' => __( 'Provider slug (see load-provider for the list).', 'extrachill-mcp' ),
						),
						'tool'      => array(
							'type'        => 'string',
							'description' => __( 'Tool name within the provider.', 'extrachill-mcp' ),
						),
						'arguments' => array(
							'type'                 => 'object',
							'description'          => __( 'Tool arguments (must match the tool input_schema).', 'extrachill-mcp' ),
							'additionalProperties' => true,
						),
					),
				),
				'output_schema'       => array(
					'type'                 => 'object',
					'additionalProperties' => true,
				),
				'execute_callback'    => array( $this, 'execute_tool' ),
				'permission_callback' => '__return_true',
				'meta'                => array(
					'show_in_rest' => true,
					'annotations'  => array(
						'readonly'   => true,
						'idempotent' => true,
					),
					// Off the adapter's default server; reached via our own server only.
					'mcp'          => array( 'public' => false ),
				),
			)
		);
	}

	/**
	 * Handler for load-provider.
	 */
	public function execute_load_provider( array $input ): array|\WP_Error {
		$slug = isset( $input['provider'] ) ? (string) $input['provider'] : '';

		if ( '' === $slug ) {
			return array(
				'providers' => $this->registry->catalog(),
			);
		}

		$provider = $this->registry->get( $slug );
		if ( ! $provider ) {
			return new \WP_Error(
				'unknown_provider',
				sprintf(
					/* translators: %s: provider slug */
					__( "Provider '%s' is not registered. Call load-provider without arguments to list available providers.", 'extrachill-mcp' ),
					$slug
				),
				array( 'status' => 404 )
			);
		}

		return array(
			'provider' => $slug,
			'tools'    => $provider->tools(),
		);
	}

	/**
	 * Handler for execute-tool.
	 */
	public function execute_tool( array $input ): array|\WP_Error {
		$slug = isset( $input['provider'] ) ? (string) $input['provider'] : '';
		$tool = isset( $input['tool'] ) ? (string) $input['tool'] : '';
		$args = isset( $input['arguments'] ) && is_array( $input['arguments'] ) ? $input['arguments'] : array();

		$provider = $this->registry->get( $slug );
		if ( ! $provider ) {
			return new \WP_Error(
				'unknown_provider',
				sprintf(
					/* translators: %s: provider slug */
					__( "Provider '%s' is not registered.", 'extrachill-mcp' ),
					$slug
				),
				array( 'status' => 404 )
			);
		}

		$tools = $provider->tools();
		if ( ! isset( $tools[ $tool ] ) ) {
			return new \WP_Error(
				'unknown_tool',
				sprintf(
					/* translators: 1: tool name, 2: provider slug */
					__( "Tool '%1\$s' is not registered in provider '%2\$s'.", 'extrachill-mcp' ),
					$tool,
					$slug
				),
				array( 'status' => 404 )
			);
		}

		return $provider->execute( $tool, $args );
	}
}
