# extrachill-mcp

MCP context server for the Extra Chill platform — provides AI agents with access to the Extra Chill multisite network and Extra-Chill GitHub org via the `load-provider` / `execute-tool` meta-tool pattern.

Inspired by [`Automattic/mcp-context-wporg`](https://github.com/Automattic/mcp-context-wporg). Built as a PHP plugin on the WordPress [`mcp-adapter`](https://github.com/WordPress/mcp-adapter).

**Status:** Phase 0 scaffold. `editorial` provider fully implemented; other providers stubbed with tool schemas declared.

## What it is

One uniform surface exposing everything relevant to Extra Chill through a consistent MCP interface:

- **editorial** — posts, authors, tags, categories on extrachill.com
- **wire** — news wire aggregation (wire.extrachill.com)
- **docs** — documentation hub (docs.extrachill.com)
- **artists** — artist profiles + link pages (artist.extrachill.com)
- **events** — shows, festivals, venues (events.extrachill.com)
- **community** — forums (community.extrachill.com)
- **shop** — shop.extrachill.com products
- **github** — Extra-Chill GitHub org (issues, PRs, releases)

## Architecture

Two meta-tools form the entire MCP surface. Everything else is reached by dispatch:

```
extrachill-mcp/load-provider    Load a provider and return its tool catalog
extrachill-mcp/execute-tool     Execute a tool within a loaded provider
```

This pattern (borrowed from `mcp-context-wporg`) keeps the client's context window lean no matter how many providers or tools we add.

Each provider follows a consistent tool shape:

```
search-{resource}    Search/filter
get-{resource}       Get one
get-timeline         Recent activity feed
list-{taxonomy}      Discovery helper
```

## Endpoint

Once installed on a site, the MCP server is available at:

```
https://<site>/wp-json/extrachill-mcp/v1/mcp
```

## Usage

### With Claude Desktop (HTTP via mcp-wordpress-remote proxy)

```json
{
  "mcpServers": {
    "extrachill": {
      "command": "npx",
      "args": ["-y", "@automattic/mcp-wordpress-remote@latest"],
      "env": {
        "WP_API_URL": "https://extrachill.com/wp-json/extrachill-mcp/v1/mcp",
        "WP_API_USERNAME": "your-wp-username",
        "WP_API_PASSWORD": "your-application-password"
      }
    }
  }
}
```

### With Claude Code / WP-CLI (STDIO)

```bash
wp mcp-adapter serve --server=extrachill-mcp --user=admin
```

## Providers

| Provider | Status | Description |
|----------|--------|-------------|
| editorial | Working | Posts, authors, tags, categories on extrachill.com |
| wire | Stubbed (Phase 1) | News wire items |
| docs | Stubbed (Phase 1) | Documentation hub |
| artists | Stubbed (Phase 1) | Artist profiles + link pages |
| events | Stubbed (Phase 1) | Shows, festivals, venues |
| community | Stubbed (Phase 1) | Forums |
| shop | Stubbed (Phase 1) | Products |
| github | Stubbed (Phase 1) | Extra-Chill/* GitHub org |

## Development

```bash
# Install dependencies
composer install

# Run linter
composer run phpcs

# Run tests
composer run phpunit
```

## Requirements

- PHP 8.1+
- WordPress 6.9+ (for core Abilities API)
- [`wordpress/mcp-adapter`](https://github.com/WordPress/mcp-adapter) (installed via composer)

## Roadmap

- **Phase 0 (current)** — Plugin scaffold, meta-tools, registry, editorial reference implementation, other providers stubbed
- **Phase 1** — All 8 providers fully implemented; cache layer; `GITHUB_TOKEN` support for the github provider
- **Phase 1.5** — Bearer token auth for write tools (post-reply, submit-event, update-profile)
- **Phase 2** — Roadie consumes this server as its primary MCP client
- **Phase 3** — Private providers (analytics, sendy, mediavine, gdrive) gated behind auth
- **Phase 4** — External enrichment (spotify, bandcamp, setlist.fm) naturally extending the artist platform

## License

GPL-2.0-or-later
