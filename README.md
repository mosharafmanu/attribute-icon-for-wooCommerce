# Attribute Icon for WooCommerce

> Adds an image upload field to each WooCommerce attribute — display icons alongside attribute labels on the product page.

![Attribute Icon Field]
<!-- Replace with cinematic screenshot of the attribute edit screen -->

## Architecture

A lightweight, single-purpose plugin that extends WooCommerce attributes with image support. Each attribute (Color, Size, Material) gets a media upload field. Images are stored as WordPress options and surfaced on the storefront via the `woocommerce_attribute_label` filter.

### Where images appear

- **Attribute edit screen** — Products > Attributes > Edit Attribute. Upload an icon next to the attribute name.
- **Attribute list table** — A Thumbnail column shows a preview for each attribute.
- **Product page** — The attribute image renders before the label in the Additional Information tab.
- **Theme helpers** — `AttributeFrontend::get_image_html()` for custom template integration.

### Plugin structure

```
attribute-icon-for-woocommerce.php      # Bootstrap
src/
├── class-attributeimagemanager.php           # Admin upload, save, list table
└── class-attributefrontend.php               # Storefront display, template helpers
assets/
├── css/admin.css
└── js/admin.js
```

## Design decisions

**Attribute level, not term level.** Variation images are already handled by WooCommerce. This plugin adds an image to the attribute *itself* — a palette icon next to "Color:", a ruler next to "Size:". Term-level swatches are a separate concern.

**Options storage, not term meta.** Attributes are stored in a custom WooCommerce table, not as WordPress taxonomy terms. Images are stored in `wp_options` under the key `attricfo_attribute_image_{id}` — clean, fast, and trivial to query.

**WordPress media library.** The plugin wraps `wp.media()` — the same uploader used everywhere in WordPress admin. No custom file handling.

**Template helpers, not template overrides.** The frontend class exposes static methods (`render_image()`, `get_image_html()`) for theme developers. The `woocommerce_attribute_label` filter adds images automatically for themes using default markup.

## Usage in themes

```php
use AttrIconWoo\AttributeFrontend;

// Render an attribute image directly
AttributeFrontend::render_image( $attribute_id, 'thumbnail' );

// Get the HTML string
echo AttributeFrontend::get_image_html( $attribute_id, 'thumbnail', 'Color' );
```

## Maintainability

Two classes with clear boundaries. Admin persistence through `get_option`/`update_option`. Frontend reads only, no writes. All hooks registered through `init()` methods. One nonce, one capability check, one field name.

## Scalability

One `get_option()` call per attribute rendered. No eager loading. The `wc_get_attribute_taxonomies()` call in the frontend is a lightweight taxonomy fetch — WooCommerce caches the result internally.

---

*Documentation is editorial, not tutorial. For implementation specifics, read the source.*
