<?php
/**
 * Helper Functions
 *
 * @package     ArrayPress\WP\RegisterTaxonomy
 * @copyright   Copyright (c) 2025, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 * @author      David Sherlock
 */

declare( strict_types=1 );

namespace ArrayPress\WP\RegisterTaxonomy;

if ( ! function_exists( 'register_tax' ) ) {
	/**
	 * Register a custom taxonomy with smart defaults and automatic label generation.
	 *
	 * @param string $taxonomy Taxonomy slug (max 32 characters).
	 * @param array  $config   Configuration array.
	 *
	 * @return Taxonomy The Taxonomy instance.
	 *
	 * @example
	 * ```php
	 * // For post types
	 * register_tax( 'product_brand', [
	 *     'post_types' => [ 'product' ],
	 *     'labels' => [
	 *         'singular' => 'Brand',
	 *         'plural'   => 'Brands'
	 *     ],
	 *     'hierarchical' => true
	 * ] );
	 *
	 * // For custom objects
	 * register_tax( 'project_status', [
	 *     'objects' => [ 'my_custom_table' ],
	 *     'labels' => [
	 *         'singular' => 'Status',
	 *         'plural'   => 'Statuses'
	 *     ]
	 * ] );
	 * ```
	 */
	function register_tax( string $taxonomy, array $config = [] ): Taxonomy {
		return new Taxonomy( $taxonomy, $config );
	}
}