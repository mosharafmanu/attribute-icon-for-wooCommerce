=== Attribute Icon for WooCommerce ===
Contributors: mosharafmanu
Donate link: https://github.com/mosharafmanu/attribute-icon-for-wooCommerce
Tags: woocommerce, attributes, icons, product attributes, attribute icon
Requires at least: 6.4
Tested up to: 7.0
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add an icon to each WooCommerce attribute — display it alongside the attribute label on the product page.

== Description ==

Attribute Icon for WooCommerce extends the WooCommerce attribute system with icon support. Each attribute (Color, Size, Material, etc.) gets a native WordPress media uploader field. Upload an icon — it appears before the attribute label on the single product page.

**Feature summary:**

* Icon upload field on every attribute — add and edit screens.
* Icon column in the attribute list table.
* Frontend display via `woocommerce_attribute_label` filter (automatic).
* Template helpers for custom themes — `render_image()` and `get_image_html()`.
* No configuration. Activate and every attribute gets the field.
* Uses WordPress media library — no custom uploader, no external dependencies.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` or install via Plugins > Add New.
2. Activate the plugin. WooCommerce must be active.
3. Edit any attribute (Products > Attributes > Edit). An icon upload field appears.

== Frequently Asked Questions ==

= Does this work with custom themes? =

Yes. If your theme uses `wc_display_product_attributes()` or `woocommerce_attribute_label`, icons appear automatically. For custom templates, use the static helpers:

```
use WcAttributeThumbnail\AttributeFrontend;
echo AttributeFrontend::get_image_html( $attribute_id, 'thumbnail', 'Color' );
```

= Where is the icon stored? =

The image attachment ID is stored in `wp_options` under the key `wc_attribute_image_{id}`. The actual file uses the WordPress media library.

= What happens when I deactivate? =

Icons stop displaying on the frontend. Options are preserved — reactivate and icons return. No data loss.

= What happens when I delete the plugin? =

All `wc_attribute_image_*` options are removed. Deleting via the Plugins screen runs the uninstall routine.

== Screenshots ==

1. Icon upload field on the Add New Attribute screen — dashed upload zone and Upload Icon button.
2. Attributes list table with the Icon column showing the uploaded icon alongside each attribute.
3. Edit Attribute screen with an icon set — Change Icon and Remove buttons.

== Changelog ==

= 1.0.0 =
* Initial release.
* Admin icon field on attribute add/edit screens.
* Icon column in attribute list table.
* Frontend display via `woocommerce_attribute_label` filter.
* Template helpers for custom theme integration.
* Uninstall cleanup of options.
