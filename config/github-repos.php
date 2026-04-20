<?php
/**
 * Hardcoded allowlist of Extra-Chill/* GitHub repositories.
 *
 * This is the surface the `github` MCP provider will query. Filterable via
 * `extrachill_mcp_github_repos` so other plugins can extend the list without
 * editing this file.
 *
 * Inspired by Automattic/mcp-context-wporg's github provider, which
 * hardcodes its tracked WordPress/* repos the same way.
 *
 * @package ExtraChillMcp
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

return array(
	'org'   => 'Extra-Chill',
	'repos' => array(
		// Platform core
		'extrachill',
		'extrachill-multisite',
		'extrachill-users',
		'extrachill-api',
		'extrachill-api-client',
		'extrachill-search',
		'extrachill-seo',
		'extrachill-cli',
		'extrachill-admin-tools',
		'extrachill-analytics',
		'extrachill-tokens',
		'extrachill-components',

		// Sites
		'extrachill-blog',
		'extrachill-community',
		'extrachill-shop',
		'extrachill-artist-platform',
		'extrachill-events',
		'extrachill-docs',
		'extrachill-news-wire',
		'extrachill-newsletter',
		'extrachill-studio',
		'extrachill-chat',

		// Content / UX
		'extrachill-content-blocks',
		'extrachill-ai-adventure',

		// Agent/automation stack
		'data-machine',
		'data-machine-code',
		'data-machine-editor',
		'data-machine-socials',
		'data-machine-business',
		'data-machine-events',
		'data-machine-chat-bridge',
		'data-machine-frontend-chat',
		'data-machine-skills',
		'extrachill-roadie',
		'mautrix-data-machine',

		// Tooling / infra
		'homeboy',
		'homeboy-extensions',
		'homeboy-action',
		'homeboy-desktop',
		'homeboy-skills',
		'homebrew-tap',
		'wp-coding-agents',

		// MCP
		'extrachill-mcp',
	),
);
