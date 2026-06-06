<?php
/**
 * Attribute Frontend — displays attribute images on the storefront.
 *
 * Prepends an icon/image before each attribute label on the single product page.
 * Provides template helpers for custom themes.
 */

namespace AttrIconWoo;

defined( 'ABSPATH' ) || exit;

class AttributeFrontend {

	public function init(): void {
		add_filter( 'woocommerce_attribute_label', array( $this, 'prepend_attribute_image' ), 10, 3 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_styles' ) );
	}

	/**
	 * Prepend the attribute image before the label.
	 *
	 * Looks up the attribute by its taxonomy slug, then fetches the stored image.
	 *
	 * @param string $label The attribute label.
	 * @param string $name  Attribute taxonomy name (e.g. 'pa_color').
	 * @param array  $product WC_Product attribute data.
	 */
	public function prepend_attribute_image( string $label, string $name, $_product ): string {
		if ( is_admin() ) {
			return $label;
		}

		$attr_obj = $this->find_attribute_by_taxonomy( $name );

		if ( ! $attr_obj ) {
			return $label;
		}

		$image_url = AttributeImageManager::get_image_url( $attr_obj->attribute_id, 'thumbnail' );

		if ( ! $image_url ) {
			return $label;
		}

		$img = sprintf(
			'<img src="%s" alt="%s" class="wc-attribute-thumbnail" width="24" height="24" loading="lazy">',
			esc_url( $image_url ),
			esc_attr( $label )
		);

		return $img . ' ' . $label;
	}

	/**
	 * Find an attribute taxonomy object by its registered taxonomy name.
	 */
	private function find_attribute_by_taxonomy( string $taxonomy ): ?object {
		$taxonomies = wc_get_attribute_taxonomies();

		foreach ( $taxonomies as $attr ) {
			if ( wc_attribute_taxonomy_name( $attr->attribute_name ) === $taxonomy ) {
				return $attr;
			}
		}

		return null;
	}

	/**
	 * Render an attribute image by attribute ID.
	 *
	 * @param int          $attribute_id Attribute ID.
	 * @param string|array $size         Image size.
	 */
	public static function render_image( int $attribute_id, $size = 'thumbnail' ): void {
		$image_url = AttributeImageManager::get_image_url( $attribute_id, $size );

		if ( ! $image_url ) {
			return;
		}

		printf(
			'<img src="%s" class="wc-attribute-thumbnail" alt="" loading="lazy">',
			esc_url( $image_url )
		);
	}

	/**
	 * Get attribute image HTML by attribute ID.
	 *
	 * @param int          $attribute_id Attribute ID.
	 * @param string|array $size         Image size.
	 * @param string       $alt          Alt text.
	 */
	public static function get_image_html( int $attribute_id, $size = 'thumbnail', string $alt = '' ): string {
		$image_id = AttributeImageManager::get_image_id( $attribute_id );

		if ( ! $image_id ) {
			return '';
		}

		return wp_get_attachment_image(
			$image_id,
			$size,
			false,
			array(
				'class'   => 'wc-attribute-thumbnail',
				'alt'     => $alt,
				'loading' => 'lazy',
			)
		);
	}

	public function enqueue_frontend_styles(): void {
		if ( ! $this->should_load_styles() ) {
			return;
		}

		$css = '
			.wc-attribute-thumbnail {
				display: inline-block;
				vertical-align: middle;
				margin-right: 6px;
				border-radius: 2px;
			}
		';

		wp_register_style( 'attribute-icon-for-woocommerce-frontend', false, array(), PLUGIN_VERSION );
		wp_enqueue_style( 'attribute-icon-for-woocommerce-frontend' );
		wp_add_inline_style( 'attribute-icon-for-woocommerce-frontend', $css );
	}

	private function should_load_styles(): bool {
		return is_product() || is_shop() || is_product_category() || is_product_tag();
	}
}
