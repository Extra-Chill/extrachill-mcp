<?php
/**
 * Abstract provider contract.
 *
 * @package ExtraChillMcp
 */

declare( strict_types = 1 );

namespace ExtraChillMcp\Providers;

defined( 'ABSPATH' ) || exit;

/**
 * Base class for all MCP context providers.
 *
 * A provider wraps one data source (or one logical slice of a data source)
 * and exposes it via a consistent tool taxonomy:
 *
 *   search-{resource}    Search/filter
 *   get-{resource}       Get one
 *   get-timeline         Recent activity feed
 *   list-{taxonomy}      Discovery helper
 *
 * Providers do not talk to MCP directly — the meta-tools (load-provider,
 * execute-tool) dispatch into providers via the registry. Each provider
 * also registers its tools as WordPress abilities for REST/CLI parity.
 */
abstract class AbstractProvider {

	/**
	 * Short, stable slug for this provider (e.g. "editorial", "github").
	 */
	abstract public function slug(): string;

	/**
	 * Human-readable description shown to LLMs during provider discovery.
	 */
	abstract public function description(): string;

	/**
	 * Tool catalog: array of tool name => tool definition.
	 *
	 * Each tool definition is an array with:
	 *   - description: string
	 *   - input_schema: JSON Schema object
	 *   - output_schema: JSON Schema object (optional)
	 *
	 * @return array<string, array{description: string, input_schema: array, output_schema?: array}>
	 */
	abstract public function tools(): array;

	/**
	 * Execute a tool within this provider.
	 *
	 * @param string $tool Tool name (must be a key from tools()).
	 * @param array  $args Tool arguments.
	 *
	 * @return array|\WP_Error Tool output (should match output_schema) or error.
	 */
	abstract public function execute( string $tool, array $args ): array|\WP_Error;

	/**
	 * Category slug for abilities registered by this provider.
	 */
	abstract protected function ability_category(): string;

	/**
	 * Ability namespace prefix (e.g. "extrachill-mcp/editorial-").
	 *
	 * Tools registered as abilities become `{prefix}{tool_name}`.
	 */
	protected function ability_prefix(): string {
		return 'extrachill-mcp/' . $this->slug() . '-';
	}

	/**
	 * Register every tool in this provider as a WordPress ability.
	 *
	 * This gives CLI / REST API / direct ability access parity with MCP.
	 */
	public function register_abilities(): void {
		$register = function (): void {
			foreach ( $this->tools() as $tool_name => $definition ) {
				$ability_id = $this->ability_prefix() . $tool_name;
				wp_register_ability(
					$ability_id,
					array(
						'label'               => $definition['label'] ?? ucwords( str_replace( '-', ' ', $tool_name ) ),
						'description'         => $definition['description'],
						'category'            => $this->ability_category(),
						'input_schema'        => $definition['input_schema'] ?? array( 'type' => 'object' ),
						'output_schema'       => $definition['output_schema'] ?? array( 'type' => 'object' ),
						'execute_callback'    => function ( array $input ) use ( $tool_name ) {
							return $this->execute( $tool_name, $input );
						},
						'permission_callback' => array( $this, 'permission_check' ),
						'meta'                => array(
							'show_in_rest' => true,
							'annotations'  => array(
								'readonly'   => true,
								'idempotent' => true,
							),
							'mcp'          => array(
								// Keep provider tools off mcp-adapter's default server.
								// Reaching them is the job of the load-provider /
								// execute-tool meta-tools, which is the whole point of
								// the pattern. This flag governs the default server
								// only: McpComponentRegistry::register_ability_tool()
								// registers whatever ability names a server is handed
								// without consulting exposure, so our own server is
								// unaffected.
								'public' => false,
							),
						),
					)
				);
			}
		};

		if ( did_action( 'wp_abilities_api_init' ) ) {
			$register();
		} else {
			add_action( 'wp_abilities_api_init', $register );
		}
	}

	/**
	 * Default permission — public read-only.
	 *
	 * Phase 1.5 adds bearer-token write tools; those providers will override
	 * this to enforce token validation.
	 */
	public function permission_check(): bool {
		return true;
	}
}
