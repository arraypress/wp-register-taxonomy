# WordPress Register Taxonomy

A powerful, elegant library for registering taxonomies in WordPress with smart defaults, automatic label generation, and support for custom objects beyond post types.

## Features

- 🎯 **Simple API** - Register taxonomies with minimal configuration
- 🏷️ **Auto Labels** - Generates all labels from singular/plural forms
- 📊 **Hierarchical & Flat** - Smart label generation for both types
- 🔗 **Permalink Management** - Easy slug configuration with auto-flush
- 📦 **Custom Objects** - Attach taxonomies to custom database tables (not just post types!)
- 🎨 **Meta Box Options** - Radio buttons, checkboxes, or custom
- 💼 **Admin Column** - Easy admin column integration
- ✨ **Clean Code** - Modern PHP 7.4+, fully typed, well-documented

## Installation

Install via Composer:

```bash
composer require arraypress/wp-register-taxonomy
```

## Quick Start

### Minimal Example

```php
register_tax( 'product_brand', [
    'post_types' => [ 'product' ],
    'labels' => [
        'singular' => 'Brand',
        'plural'   => 'Brands'
    ]
] );
```

That's it! This creates a fully functional taxonomy with:
- ✅ All labels auto-generated
- ✅ Public and queryable
- ✅ REST API enabled
- ✅ Show in UI

### Full Example

```php
register_tax( 'product_brand', [
    'post_types' => [ 'product', 'review' ],
    'labels' => [
        'singular' => 'Brand',
        'plural'   => 'Brands'
    ],
    'hierarchical'      => true,         // Like categories
    'show_admin_column' => true,         // Show column in admin
    'meta_box'          => 'radio',      // Radio button selection
    'permalink' => [
        'slug'       => 'brand',
        'with_front' => false
    ]
] );
```

## Configuration Options

### Labels

The library automatically generates all taxonomy labels from just two inputs:

```php
'labels' => [
    'singular' => 'Brand',
    'plural'   => 'Brands'  // Optional - auto-pluralized if not provided
]
```

**For hierarchical taxonomies (like categories):**
- Generates parent/child related labels

**For flat taxonomies (like tags):**
- Generates popular items, comma separation labels

You can override any generated label:

```php
'labels' => [
    'singular'  => 'Brand',
    'plural'    => 'Brands',
    'all_items' => 'All My Brands'  // Override specific label
]
```

### Hierarchical vs Flat

**Hierarchical (like categories):**
```php
'hierarchical' => true
```
- Parent/child relationships
- Checkbox meta box by default
- Tree-style admin interface

**Flat (like tags):**
```php
'hierarchical' => false
```
- No parent/child relationships
- Tag-style input by default
- Flat admin interface

### Post Types

Attach to one or more post types:

```php
// Single post type
'post_types' => [ 'product' ]

// Multiple post types
'post_types' => [ 'product', 'review', 'portfolio' ]
```

### Custom Objects (Killer Feature!)

Attach taxonomies to custom database tables or objects:

```php
register_tax( 'project_status', [
    'objects' => [ 'my_custom_projects_table' ],  // Not a post type!
    'labels' => [
        'singular' => 'Status',
        'plural'   => 'Statuses'
    ],
    'show_ui' => true,
    'show_in_menu' => 'tools.php'  // Show under Tools menu
] );
```

This is perfect for:
- Custom database tables
- External data sources
- Non-post WordPress objects
- Third-party integrations

### Meta Box Types

Control how the taxonomy appears in the post editor:

```php
// Radio buttons (select one)
'meta_box' => 'radio'

// Simple (WordPress default based on hierarchical setting)
'meta_box' => 'simple'

// Custom callback
'meta_box' => 'my_custom_meta_box_function'

// Disable meta box
'meta_box' => false
```

**Radio button example:**
```php
register_tax( 'product_status', [
    'post_types'   => [ 'product' ],
    'labels'       => [
        'singular' => 'Status',
        'plural'   => 'Statuses'
    ],
    'hierarchical' => false,
    'meta_box'     => 'radio'  // Radio buttons instead of checkboxes
] );
```

### Permalink Structure

Configure custom URL structure:

```php
'permalink' => [
    'slug'         => 'brand',          // Custom slug
    'with_front'   => false,            // Don't prepend site's base
    'hierarchical' => true              // /parent/child/ structure
]
```

**Examples:**

```php
// Simple slug
'permalink' => [
    'slug' => 'brands'
]
// Result: yoursite.com/brands/nike/

// Nested structure
'permalink' => [
    'slug' => 'shop/brands'
]
// Result: yoursite.com/shop/brands/nike/

// Hierarchical with parent/child
'permalink' => [
    'slug'         => 'category',
    'hierarchical' => true
]
// Result: yoursite.com/category/parent/child/

// Disable rewrites entirely
'permalink' => [
    'disabled' => true
]
```

### Admin Column

Show taxonomy in admin list table:

```php
'show_admin_column' => true
```

### Common Options

All standard `register_taxonomy()` options are supported:

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `public` | `bool` | `true` | Public facing taxonomy |
| `hierarchical` | `bool` | `false` | Parent/child relationships |
| `show_ui` | `bool` | `true` | Show in admin UI |
| `show_in_rest` | `bool` | `true` | Enable Gutenberg & REST API |
| `show_admin_column` | `bool` | `false` | Show column in list table |
| `show_in_nav_menus` | `bool` | `true` | Show in nav menu selector |
| `show_tagcloud` | `bool` | `true` | Show in tag cloud widget |

## Real-World Examples

### E-Commerce Product Taxonomies

```php
// Hierarchical brand taxonomy
register_tax( 'product_brand', [
    'post_types' => [ 'product' ],
    'labels' => [
        'singular' => 'Brand',
        'plural'   => 'Brands'
    ],
    'hierarchical'      => true,
    'show_admin_column' => true,
    'permalink' => [
        'slug' => 'brand'
    ]
] );

// Product tags
register_tax( 'product_tag', [
    'post_types' => [ 'product' ],
    'labels' => [
        'singular' => 'Product Tag',
        'plural'   => 'Product Tags'
    ],
    'hierarchical' => false,
    'show_admin_column' => true
] );

// Product status with radio buttons
register_tax( 'product_status', [
    'post_types' => [ 'product' ],
    'labels' => [
        'singular' => 'Status',
        'plural'   => 'Statuses'
    ],
    'hierarchical' => false,
    'meta_box' => 'radio',
    'show_admin_column' => true
] );
```

### Portfolio Taxonomies

```php
// Portfolio categories
register_tax( 'portfolio_category', [
    'post_types' => [ 'portfolio' ],
    'labels' => [
        'singular' => 'Portfolio Category',
        'plural'   => 'Portfolio Categories'
    ],
    'hierarchical' => true,
    'show_admin_column' => true,
    'permalink' => [
        'slug'         => 'portfolio/category',
        'hierarchical' => true
    ]
] );

// Skills (shared across multiple post types)
register_tax( 'skill', [
    'post_types' => [ 'portfolio', 'team' ],
    'labels' => [
        'singular' => 'Skill',
        'plural'   => 'Skills'
    ],
    'hierarchical' => false,
    'show_admin_column' => true
] );
```

### Custom Object Taxonomy

```php
// Taxonomy for custom database table
register_tax( 'project_status', [
    'objects' => [ 'my_custom_projects' ],  // Custom object!
    'labels' => [
        'singular' => 'Project Status',
        'plural'   => 'Project Statuses'
    ],
    'hierarchical' => false,
    'show_ui' => true,
    'show_in_menu' => 'tools.php'
] );

// Then attach terms to your custom objects
wp_set_object_terms( $project_id, [ 'active', 'high-priority' ], 'project_status' );
```

### Simple Taxonomy with Auto-Plural

```php
register_tax( 'difficulty', [
    'post_types' => [ 'course' ],
    'labels' => [
        'singular' => 'Difficulty'
        // Plural will be auto-generated as 'Difficulties'
    ],
    'hierarchical' => true,
    'show_admin_column' => true
] );
```

### Internal Taxonomy (No Public URLs)

```php
register_tax( 'internal_category', [
    'post_types' => [ 'product' ],
    'labels' => [
        'singular' => 'Internal Category',
        'plural'   => 'Internal Categories'
    ],
    'hierarchical' => true,
    'public'       => false,
    'show_ui'      => true,
    'permalink'    => [
        'disabled' => true
    ]
] );
```

## Using with register_cpt()

If you're using `wp-register-post-type`, you can register taxonomies and attach them:

```php
// Register post type
register_cpt( 'product', [
    'labels' => [ 'singular' => 'Product' ],
    'icon'   => 'cart',
    'taxonomies' => [ 'product_brand', 'product_tag' ]  // Attach taxonomies
] );

// Register taxonomies
register_tax( 'product_brand', [
    'post_types' => [ 'product' ],
    'labels'     => [ 'singular' => 'Brand', 'plural' => 'Brands' ],
    'hierarchical' => true
] );

register_tax( 'product_tag', [
    'post_types' => [ 'product' ],
    'labels'     => [ 'singular' => 'Tag', 'plural' => 'Tags' ],
    'hierarchical' => false
] );
```

## How It Works

### Automatic Label Generation

The library uses intelligent pluralization and generates different labels based on whether the taxonomy is hierarchical:

**Hierarchical (categories):**
- Includes parent/child labels
- "Parent Category:", etc.

**Flat (tags):**
- Includes popular items labels
- "Separate tags with commas", etc.

### Permalink Management

- Rewrites are automatically configured based on your `permalink` settings
- Rewrite rules are flushed on plugin activation (no manual flush needed)
- Supports hierarchical URL structures

### Custom Objects

The library extends WordPress's taxonomy system to work with any object type, not just post types. This is done by:

1. Accepting `objects` parameter instead of just `post_types`
2. Passing objects to `register_taxonomy()`
3. Allowing you to use `wp_set_object_terms()` with your custom objects

## Function Reference

### `register_tax( string $taxonomy, array $config )`

Register a custom taxonomy.

**Parameters:**
- `$taxonomy` (string) - Taxonomy slug (max 32 characters, lowercase)
- `$config` (array) - Configuration array

**Returns:** `Taxonomy` instance

## Requirements

- PHP 7.4 or higher
- WordPress 5.0 or higher

## Best Practices

1. **Keep slugs short** - Max 32 characters for taxonomy slug
2. **Use singular form for slug** - `product_brand` not `product_brands`
3. **Provide both singular and plural** - For best label generation
4. **Choose hierarchical carefully** - Can't change easily after data exists
5. **Test permalink structure** - Always test your URLs after registration

## Troubleshooting

### Permalinks not working?

Try visiting **Settings → Permalinks** to manually flush rewrite rules.

### Taxonomy not showing in REST API?

Make sure `show_in_rest` is `true` (it is by default).

### Meta box not showing?

Check that `show_ui` is `true` and the taxonomy is registered for the correct post type.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

GPL-2.0-or-later

## Credits

Developed by [ArrayPress](https://arraypress.com/)

## Support

- [Documentation](https://github.com/arraypress/wp-register-taxonomy)
- [Issue Tracker](https://github.com/arraypress/wp-register-taxonomy/issues)