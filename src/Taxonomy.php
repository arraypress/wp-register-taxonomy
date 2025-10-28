<?php
/**
 * Taxonomy Registration Class
 *
 * @package     ArrayPress\WP\RegisterTaxonomy
 * @copyright   Copyright (c) 2025, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 * @author      David Sherlock
 */

declare( strict_types=1 );

namespace ArrayPress\WP\RegisterTaxonomy;

use ArrayPress\WP\RegisterTaxonomy\Helpers\LabelGenerator;

class Taxonomy {

	/**
	 * Taxonomy slug
	 *
	 * @var string
	 */
	private string $taxonomy;

	/**
	 * Taxonomy configuration
	 *
	 * @var array
	 */
	private array $config;

	/**
	 * Object types (post types or custom objects)
	 *
	 * @var array
	 */
	private array $object_types;

	/**
	 * Whether rewrite rules need to be flushed
	 *
	 * @var bool
	 */
	private static bool $needs_flush = false;

	/**
	 * Constructor.
	 *
	 * @param string $taxonomy Taxonomy slug.
	 * @param array  $config   Configuration array.
	 */
	public function __construct( string $taxonomy, array $config ) {
		$this->taxonomy = $taxonomy;
		$this->config   = $this->parse_config( $config );

		// Extract object types
		$this->object_types = $this->config['object_type'] ?? [];
		unset( $this->config['object_type'] );

		// Register immediately if init has fired, otherwise wait
		if ( did_action( 'init' ) ) {
			$this->register();
		} else {
			add_action( 'init', [ $this, 'register' ] );
		}

		// Handle rewrite flush on activation
		add_action( 'activated_plugin', [ $this, 'maybe_flush_rewrites' ] );
	}

	/**
	 * Parse and normalize configuration.
	 *
	 * @param array $config Raw configuration.
	 *
	 * @return array Parsed configuration.
	 */
	private function parse_config( array $config ): array {
		$defaults = [
			'labels'             => [],
			'public'             => true,
			'hierarchical'       => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_in_rest'       => true,
			'show_tagcloud'      => true,
			'show_in_quick_edit' => true,
			'show_admin_column'  => false,
			'rewrite'            => true,
			'permalink'          => [],
			'post_types'         => [],
			'objects'            => [],
			'meta_box'           => null
		];

		$config = wp_parse_args( $config, $defaults );

		// Determine object types (post_types, objects, or both)
		$object_types = [];
		if ( ! empty( $config['post_types'] ) ) {
			$object_types = (array) $config['post_types'];
		} elseif ( ! empty( $config['objects'] ) ) {
			$object_types = (array) $config['objects'];
		}
		$config['object_type'] = $object_types;

		// Generate labels if not provided
		if ( ! empty( $config['labels'] ) && ( ! empty( $config['labels']['singular'] ) || ! empty( $config['labels']['plural'] ) ) ) {
			$config['labels'] = LabelGenerator::generate(
				$config['labels']['singular'] ?? '',
				$config['labels']['plural'] ?? '',
				$config['labels'],
				$config['hierarchical']
			);
		}

		// Handle permalink/rewrite
		if ( ! empty( $config['permalink'] ) ) {
			$config['rewrite'] = $this->parse_permalink( $config['permalink'], $config['hierarchical'] );
			unset( $config['permalink'] );
		}

		// Handle meta box type
		if ( ! empty( $config['meta_box'] ) ) {
			$config['meta_box_cb'] = $this->get_meta_box_callback( $config['meta_box'] );
			unset( $config['meta_box'] );
		}

		return $config;
	}

	/**
	 * Parse permalink configuration.
	 *
	 * @param array $permalink    Permalink configuration.
	 * @param bool  $hierarchical Whether taxonomy is hierarchical.
	 *
	 * @return array|bool Rewrite configuration.
	 */
	private function parse_permalink( array $permalink, bool $hierarchical ) {
		if ( isset( $permalink['disabled'] ) && $permalink['disabled'] ) {
			return false;
		}

		$defaults = [
			'slug'         => $this->taxonomy,
			'with_front'   => true,
			'hierarchical' => $hierarchical,
			'ep_mask'      => EP_NONE
		];

		return wp_parse_args( $permalink, $defaults );
	}

	/**
	 * Get meta box callback based on type.
	 *
	 * @param string|array $meta_box Meta box configuration.
	 *
	 * @return string|callable|null Meta box callback.
	 */
	private function get_meta_box_callback( $meta_box ) {
		if ( is_callable( $meta_box ) ) {
			return $meta_box;
		}

		if ( is_string( $meta_box ) ) {
			switch ( $meta_box ) {
				case 'radio':
					return [ $this, 'render_radio_meta_box' ];
				case 'simple':
					return 'post_categories_meta_box';
				case false:
				case 'false':
					return false;
				default:
					return null; // Use default
			}
		}

		return null;
	}

	/**
	 * Register the taxonomy.
	 *
	 * @return void
	 */
	public function register(): void {
		// Remove custom keys that aren't part of register_taxonomy args
		unset( $this->config['post_types'], $this->config['objects'] );

		// Register the taxonomy
		register_taxonomy( $this->taxonomy, $this->object_types, $this->config );

		// Mark that we might need to flush rewrites
		self::$needs_flush = true;
	}

	/**
	 * Render radio button meta box for non-hierarchical taxonomies.
	 *
	 * @param \WP_Post $post Post object.
	 * @param array    $box  Meta box arguments.
	 *
	 * @return void
	 */
	public function render_radio_meta_box( $post, $box ): void {
		$taxonomy = $box['args']['taxonomy'];
		$terms    = get_terms( [
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		] );

		$current    = wp_get_object_terms( $post->ID, $taxonomy, [ 'fields' => 'ids' ] );
		$current_id = ! empty( $current ) ? $current[0] : 0;

		?>
        <div id="taxonomy-<?php echo esc_attr( $taxonomy ); ?>" class="categorydiv">
            <ul>
                <li>
                    <label class="selectit">
                        <input type="radio" name="tax_input[<?php echo esc_attr( $taxonomy ); ?>][]"
                               value="0" <?php checked( $current_id, 0 ); ?>>
						<?php esc_html_e( 'None' ); ?>
                    </label>
                </li>
				<?php foreach ( $terms as $term ) : ?>
                    <li>
                        <label class="selectit">
                            <input type="radio" name="tax_input[<?php echo esc_attr( $taxonomy ); ?>][]"
                                   value="<?php echo esc_attr( $term->term_id ); ?>" <?php checked( $current_id, $term->term_id ); ?>>
							<?php echo esc_html( $term->name ); ?>
                        </label>
                    </li>
				<?php endforeach; ?>
            </ul>
        </div>
		<?php
	}

	/**
	 * Maybe flush rewrite rules.
	 *
	 * @return void
	 */
	public function maybe_flush_rewrites(): void {
		if ( self::$needs_flush ) {
			flush_rewrite_rules();
			self::$needs_flush = false;
		}
	}

}