<?php
/**
 * Stub provider base.
 *
 * @package ExtraChillMcp
 */

declare( strict_types = 1 );

namespace ExtraChillMcp\Providers;

defined( 'ABSPATH' ) || exit;

/**
 * Shared scaffolding for providers that are declared in v1 but not yet
 * implemented. Each stub defines its tool catalog (so load-provider shows
 * the full v1 surface to clients) but returns a not-implemented error
 * on execute.
 *
 * As providers are implemented for real, they graduate to extending
 * AbstractProvider directly.
 */
abstract class AbstractStubProvider extends AbstractProvider {

	public function execute( string $tool, array $args ): array|\WP_Error {
		return new \WP_Error(
			'not_implemented',
			sprintf(
				/* translators: 1: tool name, 2: provider slug */
				__( "Tool '%1\$s' on provider '%2\$s' is not yet implemented — Phase 1 scope.", 'extrachill-mcp' ),
				$tool,
				$this->slug()
			),
			array( 'status' => 501 )
		);
	}
}
