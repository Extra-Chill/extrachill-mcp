<?php
/**
 * Provider registry.
 *
 * @package ExtraChillMcp
 */

declare( strict_types = 1 );

namespace ExtraChillMcp\Registry;

use ExtraChillMcp\Providers\AbstractProvider;

defined( 'ABSPATH' ) || exit;

/**
 * Holds provider instances keyed by slug.
 *
 * The registry is the single source of truth for which providers exist
 * and what tools they expose. Both MetaTools (load-provider / execute-tool)
 * and the MCP server read from here.
 */
final class ProviderRegistry {

	/**
	 * @var array<string, AbstractProvider>
	 */
	private array $providers = array();

	/**
	 * Register a provider.
	 */
	public function register( AbstractProvider $provider ): void {
		$this->providers[ $provider->slug() ] = $provider;
	}

	/**
	 * Get a provider by slug.
	 */
	public function get( string $slug ): ?AbstractProvider {
		return $this->providers[ $slug ] ?? null;
	}

	/**
	 * Does this slug correspond to a registered provider?
	 */
	public function has( string $slug ): bool {
		return isset( $this->providers[ $slug ] );
	}

	/**
	 * @return array<string, AbstractProvider>
	 */
	public function all(): array {
		return $this->providers;
	}

	/**
	 * Provider slug list for discovery.
	 *
	 * @return string[]
	 */
	public function slugs(): array {
		return array_keys( $this->providers );
	}

	/**
	 * Lightweight catalog for load-provider output — all providers,
	 * each described by slug, description, and tool names (no schemas).
	 *
	 * @return array<int, array{slug: string, description: string, tools: string[]}>
	 */
	public function catalog(): array {
		$out = array();
		foreach ( $this->providers as $provider ) {
			$out[] = array(
				'slug'        => $provider->slug(),
				'description' => $provider->description(),
				'tools'       => array_keys( $provider->tools() ),
			);
		}
		return $out;
	}
}
