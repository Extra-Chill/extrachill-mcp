<?php
/**
 * Plugin Name:       Extra Chill MCP
 * Plugin URI:        https://github.com/Extra-Chill/extrachill-mcp
 * Description:       MCP context server for the Extra Chill platform. Exposes the Extra Chill multisite network and GitHub org to MCP clients via the load-provider / execute-tool meta-tool pattern.
 * Version:           0.1.0
 * Requires at least: 6.9
 * Requires PHP:      8.1
 * Author:            Extra Chill
 * Author URI:        https://extrachill.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       extrachill-mcp
 *
 * @package ExtraChillMcp
 */

declare( strict_types = 1 );

namespace ExtraChillMcp;

defined( 'ABSPATH' ) || exit;

define( 'EXTRACHILL_MCP_VERSION', '0.1.0' );
define( 'EXTRACHILL_MCP_FILE', __FILE__ );
define( 'EXTRACHILL_MCP_DIR', plugin_dir_path( __FILE__ ) );
define( 'EXTRACHILL_MCP_URL', plugin_dir_url( __FILE__ ) );

// Composer autoload (mcp-adapter + any other deps).
$autoload = EXTRACHILL_MCP_DIR . 'vendor/autoload.php';
if ( file_exists( $autoload ) ) {
	require_once $autoload;
}

// PSR-4 autoload for src/ (fallback if composer not installed during dev).
spl_autoload_register(
	static function ( string $class ): void {
		$prefix = __NAMESPACE__ . '\\';
		if ( strpos( $class, $prefix ) !== 0 ) {
			return;
		}
		$relative = substr( $class, strlen( $prefix ) );
		$path     = EXTRACHILL_MCP_DIR . 'src/' . str_replace( '\\', '/', $relative ) . '.php';
		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
);

add_action(
	'plugins_loaded',
	static function (): void {
		if ( ! function_exists( 'wp_register_ability' ) ) {
			add_action(
				'admin_notices',
				static function (): void {
					echo '<div class="notice notice-error"><p><strong>Extra Chill MCP</strong> requires the WordPress Abilities API (WP 6.9+).</p></div>';
				}
			);
			return;
		}

		if ( ! class_exists( \WP\MCP\Core\McpAdapter::class ) ) {
			add_action(
				'admin_notices',
				static function (): void {
					echo '<div class="notice notice-error"><p><strong>Extra Chill MCP</strong> requires <code>wordpress/mcp-adapter</code>. Run <code>composer install</code> in the plugin directory.</p></div>';
				}
			);
			return;
		}

		Plugin::boot();
	},
	20
);
