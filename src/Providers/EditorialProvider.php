<?php
/**
 * Editorial provider — posts/authors/tags/categories on extrachill.com.
 *
 * This is the reference implementation for the 4-tool provider shape:
 *   search-posts · get-post · get-timeline · list-categories · list-tags · list-authors
 *
 * @package ExtraChillMcp
 */

declare( strict_types = 1 );

namespace ExtraChillMcp\Providers;

use ExtraChillMcp\AbilityCategories;

defined( 'ABSPATH' ) || exit;

final class EditorialProvider extends AbstractProvider {

	public function slug(): string {
		return 'editorial';
	}

	public function description(): string {
		return __( 'Editorial content on extrachill.com — posts, authors, tags, categories. Use for music journalism, features, reviews, interviews, and news coverage.', 'extrachill-mcp' );
	}

	protected function ability_category(): string {
		return AbilityCategories::EDITORIAL;
	}

	public function tools(): array {
		return array(
			'search-posts'     => array(
				'label'         => __( 'Search Editorial Posts', 'extrachill-mcp' ),
				'description'   => __( 'Search extrachill.com posts by keyword, category, tag, author, or date range.', 'extrachill-mcp' ),
				'input_schema'  => array(
					'type'       => 'object',
					'properties' => array(
						'q'         => array(
							'type'        => 'string',
							'description' => __( 'Keyword search query.', 'extrachill-mcp' ),
						),
						'category'  => array(
							'type'        => 'string',
							'description' => __( 'Category slug to filter by.', 'extrachill-mcp' ),
						),
						'tag'       => array(
							'type'        => 'string',
							'description' => __( 'Tag slug to filter by.', 'extrachill-mcp' ),
						),
						'author'    => array(
							'type'        => 'string',
							'description' => __( 'Author slug (user nicename) to filter by.', 'extrachill-mcp' ),
						),
						'date_from' => array(
							'type'        => 'string',
							'format'      => 'date',
							'description' => __( 'Published on or after this date (YYYY-MM-DD).', 'extrachill-mcp' ),
						),
						'date_to'   => array(
							'type'        => 'string',
							'format'      => 'date',
							'description' => __( 'Published on or before this date (YYYY-MM-DD).', 'extrachill-mcp' ),
						),
						'limit'     => array(
							'type'        => 'integer',
							'minimum'     => 1,
							'maximum'     => 50,
							'default'     => 10,
							'description' => __( 'Max results (1-50, default 10).', 'extrachill-mcp' ),
						),
					),
				),
				'output_schema' => self::post_list_schema(),
			),
			'get-post'         => array(
				'label'         => __( 'Get Editorial Post', 'extrachill-mcp' ),
				'description'   => __( 'Get a single editorial post by slug or ID, including full content.', 'extrachill-mcp' ),
				'input_schema'  => array(
					'type'       => 'object',
					'properties' => array(
						'slug' => array(
							'type'        => 'string',
							'description' => __( 'Post slug (post_name).', 'extrachill-mcp' ),
						),
						'id'   => array(
							'type'        => 'integer',
							'description' => __( 'Post ID.', 'extrachill-mcp' ),
						),
					),
				),
				'output_schema' => self::post_full_schema(),
			),
			'get-timeline'     => array(
				'label'         => __( 'Editorial Timeline', 'extrachill-mcp' ),
				'description'   => __( 'Recent posts published in the last N days.', 'extrachill-mcp' ),
				'input_schema'  => array(
					'type'       => 'object',
					'properties' => array(
						'days'  => array(
							'type'        => 'integer',
							'minimum'     => 1,
							'maximum'     => 90,
							'default'     => 7,
							'description' => __( 'Days back to fetch (1-90, default 7).', 'extrachill-mcp' ),
						),
						'limit' => array(
							'type'        => 'integer',
							'minimum'     => 1,
							'maximum'     => 50,
							'default'     => 20,
							'description' => __( 'Max results.', 'extrachill-mcp' ),
						),
					),
				),
				'output_schema' => self::post_list_schema(),
			),
			'list-categories'  => array(
				'label'         => __( 'List Editorial Categories', 'extrachill-mcp' ),
				'description'   => __( 'List all post categories with counts. Hierarchical.', 'extrachill-mcp' ),
				'input_schema'  => array( 'type' => 'object' ),
				'output_schema' => self::term_list_schema(),
			),
			'list-tags'        => array(
				'label'         => __( 'List Popular Tags', 'extrachill-mcp' ),
				'description'   => __( 'Most-used tags across editorial content.', 'extrachill-mcp' ),
				'input_schema'  => array(
					'type'       => 'object',
					'properties' => array(
						'limit' => array(
							'type'    => 'integer',
							'minimum' => 1,
							'maximum' => 200,
							'default' => 50,
						),
					),
				),
				'output_schema' => self::term_list_schema(),
			),
			'list-authors'     => array(
				'label'         => __( 'List Editorial Authors', 'extrachill-mcp' ),
				'description'   => __( 'Users who have published at least one post. Co-Authors Plus aware.', 'extrachill-mcp' ),
				'input_schema'  => array( 'type' => 'object' ),
				'output_schema' => array(
					'type'       => 'object',
					'properties' => array(
						'authors' => array(
							'type'  => 'array',
							'items' => array(
								'type'       => 'object',
								'properties' => array(
									'id'          => array( 'type' => 'integer' ),
									'nicename'    => array( 'type' => 'string' ),
									'display_name' => array( 'type' => 'string' ),
									'post_count'  => array( 'type' => 'integer' ),
									'url'         => array( 'type' => 'string' ),
								),
							),
						),
					),
				),
			),
		);
	}

	public function execute( string $tool, array $args ): array|\WP_Error {
		return match ( $tool ) {
			'search-posts'    => $this->search_posts( $args ),
			'get-post'        => $this->get_post( $args ),
			'get-timeline'    => $this->get_timeline( $args ),
			'list-categories' => $this->list_terms( 'category' ),
			'list-tags'       => $this->list_terms( 'post_tag', (int) ( $args['limit'] ?? 50 ) ),
			'list-authors'    => $this->list_authors(),
			default           => new \WP_Error(
				'unknown_tool',
				/* translators: %s: tool name */
				sprintf( __( "Tool '%s' is not implemented in editorial provider.", 'extrachill-mcp' ), $tool ),
				array( 'status' => 404 )
			),
		};
	}

	/* ---------------------------------------------------------------
	 * Tool handlers
	 * ------------------------------------------------------------- */

	private function search_posts( array $args ): array {
		$query_args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => max( 1, min( 50, (int) ( $args['limit'] ?? 10 ) ) ),
			'no_found_rows'  => false,
		);

		if ( ! empty( $args['q'] ) ) {
			$query_args['s'] = (string) $args['q'];
		}
		if ( ! empty( $args['category'] ) ) {
			$query_args['category_name'] = (string) $args['category'];
		}
		if ( ! empty( $args['tag'] ) ) {
			$query_args['tag'] = (string) $args['tag'];
		}
		if ( ! empty( $args['author'] ) ) {
			$query_args['author_name'] = (string) $args['author'];
		}

		$date_query = array();
		if ( ! empty( $args['date_from'] ) ) {
			$date_query['after'] = (string) $args['date_from'];
		}
		if ( ! empty( $args['date_to'] ) ) {
			$date_query['before'] = (string) $args['date_to'];
		}
		if ( ! empty( $date_query ) ) {
			$date_query['inclusive']  = true;
			$query_args['date_query'] = array( $date_query );
		}

		$query = new \WP_Query( $query_args );
		$posts = array_map( array( $this, 'format_post_summary' ), $query->posts );

		return array(
			'total'   => (int) $query->found_posts,
			'count'   => count( $posts ),
			'results' => $posts,
		);
	}

	private function get_post( array $args ): array|\WP_Error {
		$post = null;
		if ( ! empty( $args['id'] ) ) {
			$post = get_post( (int) $args['id'] );
		} elseif ( ! empty( $args['slug'] ) ) {
			$posts = get_posts(
				array(
					'name'           => (string) $args['slug'],
					'post_type'      => 'post',
					'post_status'    => 'publish',
					'posts_per_page' => 1,
				)
			);
			$post  = $posts[0] ?? null;
		}

		if ( ! $post || 'publish' !== $post->post_status ) {
			return new \WP_Error( 'post_not_found', __( 'Post not found.', 'extrachill-mcp' ), array( 'status' => 404 ) );
		}

		return $this->format_post_full( $post );
	}

	private function get_timeline( array $args ): array {
		$days  = max( 1, min( 90, (int) ( $args['days'] ?? 7 ) ) );
		$limit = max( 1, min( 50, (int) ( $args['limit'] ?? 20 ) ) );

		return $this->search_posts(
			array(
				'date_from' => gmdate( 'Y-m-d', time() - ( $days * DAY_IN_SECONDS ) ),
				'limit'     => $limit,
			)
		);
	}

	private function list_terms( string $taxonomy, int $limit = 0 ): array {
		$query_args = array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'orderby'    => 'count',
			'order'      => 'DESC',
		);
		if ( $limit > 0 ) {
			$query_args['number'] = $limit;
		}

		$terms = get_terms( $query_args );
		if ( is_wp_error( $terms ) ) {
			return array( 'terms' => array() );
		}

		$out = array();
		foreach ( $terms as $term ) {
			$url   = get_term_link( $term );
			$out[] = array(
				'term_id' => (int) $term->term_id,
				'name'    => $term->name,
				'slug'    => $term->slug,
				'count'   => (int) $term->count,
				'parent'  => (int) $term->parent,
				'url'     => is_wp_error( $url ) ? '' : $url,
			);
		}

		return array( 'terms' => $out );
	}

	private function list_authors(): array {
		$users = get_users(
			array(
				'has_published_posts' => array( 'post' ),
				'orderby'             => 'post_count',
				'order'               => 'DESC',
				'number'              => 200,
			)
		);

		$authors = array();
		foreach ( $users as $user ) {
			$authors[] = array(
				'id'           => (int) $user->ID,
				'nicename'     => $user->user_nicename,
				'display_name' => $user->display_name,
				'post_count'   => (int) count_user_posts( $user->ID, 'post', true ),
				'url'          => get_author_posts_url( $user->ID ),
			);
		}

		return array( 'authors' => $authors );
	}

	/* ---------------------------------------------------------------
	 * Formatters
	 * ------------------------------------------------------------- */

	private function format_post_summary( \WP_Post $post ): array {
		return array(
			'id'         => (int) $post->ID,
			'slug'       => $post->post_name,
			'title'      => get_the_title( $post ),
			'excerpt'    => wp_strip_all_tags( get_the_excerpt( $post ) ),
			'author'     => get_the_author_meta( 'display_name', (int) $post->post_author ),
			'date'       => mysql2date( 'c', $post->post_date_gmt ?: $post->post_date, false ),
			'url'        => get_permalink( $post ),
			'categories' => wp_list_pluck( get_the_category( $post->ID ), 'slug' ),
			'tags'       => wp_list_pluck( get_the_tags( $post->ID ) ?: array(), 'slug' ),
		);
	}

	private function format_post_full( \WP_Post $post ): array {
		$summary            = $this->format_post_summary( $post );
		$summary['content'] = wp_strip_all_tags( apply_filters( 'the_content', $post->post_content ) );
		$summary['raw_html'] = apply_filters( 'the_content', $post->post_content );
		$thumbnail_id       = get_post_thumbnail_id( $post );
		$summary['featured_image'] = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'large' ) : null;

		return $summary;
	}

	/* ---------------------------------------------------------------
	 * Reusable schema fragments
	 * ------------------------------------------------------------- */

	private static function post_list_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'total'   => array( 'type' => 'integer' ),
				'count'   => array( 'type' => 'integer' ),
				'results' => array(
					'type'  => 'array',
					'items' => self::post_summary_schema(),
				),
			),
		);
	}

	private static function post_summary_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'id'         => array( 'type' => 'integer' ),
				'slug'       => array( 'type' => 'string' ),
				'title'      => array( 'type' => 'string' ),
				'excerpt'    => array( 'type' => 'string' ),
				'author'     => array( 'type' => 'string' ),
				'date'       => array( 'type' => 'string' ),
				'url'        => array( 'type' => 'string' ),
				'categories' => array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ),
				'tags'       => array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ),
			),
		);
	}

	private static function post_full_schema(): array {
		$schema = self::post_summary_schema();
		$schema['properties']['content']        = array( 'type' => 'string' );
		$schema['properties']['raw_html']       = array( 'type' => 'string' );
		$schema['properties']['featured_image'] = array( 'type' => array( 'string', 'null' ) );
		return $schema;
	}

	private static function term_list_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'terms' => array(
					'type'  => 'array',
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'term_id' => array( 'type' => 'integer' ),
							'name'    => array( 'type' => 'string' ),
							'slug'    => array( 'type' => 'string' ),
							'count'   => array( 'type' => 'integer' ),
							'parent'  => array( 'type' => 'integer' ),
							'url'     => array( 'type' => 'string' ),
						),
					),
				),
			),
		);
	}
}
