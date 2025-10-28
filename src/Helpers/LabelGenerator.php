<?php
/**
 * Label Generator Helper
 *
 * Automatically generates all required labels from singular and plural forms.
 *
 * @package     ArrayPress\WP\RegisterTaxonomy
 * @copyright   Copyright (c) 2025, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 * @author      David Sherlock
 */

declare( strict_types=1 );

namespace ArrayPress\WP\RegisterTaxonomy\Helpers;

class LabelGenerator {

	/**
	 * Generate all labels from singular and plural forms.
	 *
	 * @param string $singular     Singular form (e.g., 'Category').
	 * @param string $plural       Plural form (e.g., 'Categories').
	 * @param array  $overrides    Optional label overrides.
	 * @param bool   $hierarchical Whether taxonomy is hierarchical.
	 *
	 * @return array Complete set of labels.
	 */
	public static function generate( string $singular, string $plural = '', array $overrides = [], bool $hierarchical = false ): array {
		// If no plural provided, try to make one
		if ( empty( $plural ) ) {
			$plural = self::pluralize( $singular );
		}

		$singular_lower = strtolower( $singular );
		$plural_lower   = strtolower( $plural );

		// Base labels (same for both hierarchical and flat)
		$labels = [
			'name'                  => $plural,
			'singular_name'         => $singular,
			'search_items'          => sprintf( 'Search %s', $plural ),
			'all_items'             => sprintf( 'All %s', $plural ),
			'view_item'             => sprintf( 'View %s', $singular ),
			'edit_item'             => sprintf( 'Edit %s', $singular ),
			'update_item'           => sprintf( 'Update %s', $singular ),
			'add_new_item'          => sprintf( 'Add New %s', $singular ),
			'new_item_name'         => sprintf( 'New %s Name', $singular ),
			'not_found'             => sprintf( 'No %s found', $plural_lower ),
			'no_terms'              => sprintf( 'No %s', $plural_lower ),
			'items_list'            => sprintf( '%s list', $plural ),
			'items_list_navigation' => sprintf( '%s list navigation', $plural ),
			'menu_name'             => $plural,
			'back_to_items'         => sprintf( '← Back to %s', $plural ),
		];

		// Hierarchical-specific labels (like categories)
		if ( $hierarchical ) {
			$labels['parent_item']       = sprintf( 'Parent %s', $singular );
			$labels['parent_item_colon'] = sprintf( 'Parent %s:', $singular );
		} // Flat-specific labels (like tags)
		else {
			$labels['popular_items']              = sprintf( 'Popular %s', $plural );
			$labels['separate_items_with_commas'] = sprintf( 'Separate %s with commas', $plural_lower );
			$labels['add_or_remove_items']        = sprintf( 'Add or remove %s', $plural_lower );
			$labels['choose_from_most_used']      = sprintf( 'Choose from most used %s', $plural_lower );
		}

		// Merge with any overrides
		return array_merge( $labels, $overrides );
	}

	/**
	 * Simple pluralization.
	 *
	 * @param string $singular Singular form.
	 *
	 * @return string Plural form.
	 */
	private static function pluralize( string $singular ): string {
		// Simple English pluralization rules
		if ( preg_match( '/(s|ss|sh|ch|x|z)$/i', $singular ) ) {
			return $singular . 'es';
		} elseif ( preg_match( '/([^aeiou])y$/i', $singular ) ) {
			return preg_replace( '/y$/i', 'ies', $singular );
		} else {
			return $singular . 's';
		}
	}

}