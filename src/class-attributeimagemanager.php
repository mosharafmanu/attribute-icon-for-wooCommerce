<?php
/**
 * Attribute Icon Manager — admin-side icon field for WooCommerce attributes.
 *
 * Hooks into the attribute add/edit screens (Products > Attributes) to add
 * an image upload field. Handles rendering, saving, and thumbnail display
 * in the attribute list table.
 */

namespace WcAttributeThumbnail;

defined( 'ABSPATH' ) || exit;

class AttributeImageManager {

	private const OPTION_PREFIX = 'wc_attribute_image_';
	private const NONCE_ACTION  = 'wc_attribute_image_save';
	private const NONCE_NAME    = '_wc_attribute_image_nonce';
	private const FIELD_NAME    = 'wc_attribute_image_id';

	public function init(): void {
		add_action( 'woocommerce_after_add_attribute_fields', array( $this, 'render_add_form_field' ) );
		add_action( 'woocommerce_after_edit_attribute_fields', array( $this, 'render_edit_form_field' ) );
		add_action( 'woocommerce_attribute_added', array( $this, 'save_attribute_image' ), 10, 2 );
		add_action( 'woocommerce_attribute_updated', array( $this, 'save_attribute_image' ), 10, 2 );
		add_action( 'woocommerce_attribute_deleted', array( $this, 'delete_attribute_image' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_footer', array( $this, 'inject_list_table_column' ) );
	}

	// ─── Add attribute form ─────────────────────────────────────────────

	public function render_add_form_field(): void {
		?>
		<div class="form-field term-image-wrap">
			<label><?php esc_html_e( 'Attribute Icon', 'attribute-icon-for-woocommerce' ); ?></label>
			<div class="wc-attribute-image-field"
				data-field="<?php echo esc_attr( self::FIELD_NAME ); ?>">
				<div class="wc-attribute-image-preview"></div>
				<input type="hidden"
					name="<?php echo esc_attr( self::FIELD_NAME ); ?>"
					id="<?php echo esc_attr( self::FIELD_NAME ); ?>"
					value="">
				<div class="wc-attribute-image-actions">
					<button type="button" class="button wc-attribute-image-upload">
						<?php esc_html_e( 'Upload Icon', 'attribute-icon-for-woocommerce' ); ?>
					</button>
				</div>
			</div>
		</div>
		<?php
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );
	}

	// ─── Edit attribute form ─────────────────────────────────────────────

	public function render_edit_form_field(): void {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$attribute_id = isset( $_GET['edit'] ) ? absint( wp_unslash( $_GET['edit'] ) ) : 0;

		if ( ! $attribute_id && isset( $_GET['page'] ) && 'product_attributes' === $_GET['page'] ) {
			$attribute_id = isset( $_GET['attribute_id'] ) ? absint( wp_unslash( $_GET['attribute_id'] ) ) : 0;
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		$image_id  = self::get_image_id( $attribute_id );
		$image_src = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';
		?>
		<tr class="form-field term-image-wrap">
			<th scope="row">
				<label><?php esc_html_e( 'Attribute Icon', 'attribute-icon-for-woocommerce' ); ?></label>
			</th>
			<td>
				<div class="wc-attribute-image-field"
					data-field="<?php echo esc_attr( self::FIELD_NAME ); ?>">
					<div class="wc-attribute-image-preview">
						<?php if ( $image_src ) : ?>
							<img src="<?php echo esc_url( $image_src ); ?>" alt="">
						<?php endif; ?>
					</div>
					<input type="hidden"
						name="<?php echo esc_attr( self::FIELD_NAME ); ?>"
						id="<?php echo esc_attr( self::FIELD_NAME ); ?>"
						value="<?php echo esc_attr( $image_id ); ?>">
					<div class="wc-attribute-image-actions">
						<button type="button" class="button wc-attribute-image-upload">
							<?php
							echo $image_id
								? esc_html__( 'Change Icon', 'attribute-icon-for-woocommerce' )
								: esc_html__( 'Upload Icon', 'attribute-icon-for-woocommerce' );
							?>
						</button>
						<?php if ( $image_id ) : ?>
							<button type="button" class="button wc-attribute-image-remove">
								<?php esc_html_e( 'Remove', 'attribute-icon-for-woocommerce' ); ?>
							</button>
						<?php endif; ?>
					</div>
				</div>
			</td>
		</tr>
		<?php
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );
	}

	// ─── Save ───────────────────────────────────────────────────────────

	public function save_attribute_image( int $attribute_id, array $_data ): void {
		if ( ! isset( $_POST[ self::NONCE_NAME ] ) ) {
			return;
		}

		if ( ! wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_POST[ self::NONCE_NAME ] ) ),
			self::NONCE_ACTION
		) ) {
			return;
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$image_id = isset( $_POST[ self::FIELD_NAME ] )
			? absint( $_POST[ self::FIELD_NAME ] )
			: 0;

		if ( $image_id ) {
			update_option( self::OPTION_PREFIX . $attribute_id, $image_id );
		} else {
			delete_option( self::OPTION_PREFIX . $attribute_id );
		}
	}

	// ─── Delete ─────────────────────────────────────────────────────────

	public function delete_attribute_image( int $attribute_id ): void {
		delete_option( self::OPTION_PREFIX . $attribute_id );
	}

	// ─── Assets ─────────────────────────────────────────────────────────

	public function enqueue_admin_assets( string $hook ): void {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$is_attribute_page = 'product_page_product_attributes' === $hook
			|| ( isset( $_GET['page'] ) && 'product_attributes' === $_GET['page'] );
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( ! $is_attribute_page ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_style(
			'attribute-icon-for-woocommerce-admin',
			PLUGIN_URL . 'assets/css/admin.css',
			array(),
			PLUGIN_VERSION
		);

		wp_enqueue_script(
			'attribute-icon-for-woocommerce-admin',
			PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			PLUGIN_VERSION,
			true
		);

		wp_localize_script(
			'attribute-icon-for-woocommerce-admin',
			'wcAttributeThumbnail',
			array(
				'fieldName'    => self::FIELD_NAME,
				'uploadTitle'  => __( 'Choose Attribute Icon', 'attribute-icon-for-woocommerce' ),
				'uploadButton' => __( 'Use this icon', 'attribute-icon-for-woocommerce' ),
				'removeLabel'  => __( 'Remove icon', 'attribute-icon-for-woocommerce' ),
				'noImageLabel' => __( 'No icon selected', 'attribute-icon-for-woocommerce' ),
				'changeLabel'  => __( 'Change Icon', 'attribute-icon-for-woocommerce' ),
				'uploadLabel'  => __( 'Upload Icon', 'attribute-icon-for-woocommerce' ),
			)
		);
	}

	// ─── List table thumbnail injection ─────────────────────────────────

	public function inject_list_table_column(): void {
		$screen = get_current_screen();

		if ( ! $screen || 'product_page_product_attributes' !== $screen->id ) {
			return;
		}

		$attributes = wc_get_attribute_taxonomies();
		$thumbnails = array();

		foreach ( $attributes as $attr ) {
			$thumbnails[ $attr->attribute_id ] = self::get_image_url( $attr->attribute_id, array( 40, 40 ) );
		}

		$json = wp_json_encode( $thumbnails );
		?>
		<script>
		( function () {
			'use strict';
			var thumbs = <?php echo $json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>;

			// Add Thumbnail column header to thead and tfoot
			[ '.wp-list-table.widefat thead tr', '.wp-list-table.widefat tfoot tr' ].forEach( function ( selector ) {
				var row = document.querySelector( selector );
				if ( row ) {
					var th = document.createElement( 'th' );
					th.className = 'column-attribute_thumbnail';
					th.textContent = '<?php echo esc_js( __( 'Icon', 'attribute-icon-for-woocommerce' ) ); ?>';
					row.insertBefore( th, row.children[1] );
				}
			} );

			// Add thumbnail cells to each row
			var rows = document.querySelectorAll( '.wp-list-table.widefat tbody tr' );
			rows.forEach( function ( row ) {
				// WooCommerce's attribute table is custom HTML — no .column-name or .row-title classes.
				// Find the edit link by its href parameter instead.
				var editLink = row.querySelector( 'a[href*="edit="]' );
				var href     = editLink ? editLink.getAttribute( 'href' ) : '';
				var match    = href.match( /[?&]edit=(\d+)/ );
				var attrId   = match ? parseInt( match[1], 10 ) : 0;
				var url      = thumbs[ attrId ] || '';

				var td = document.createElement( 'td' );
				td.className = 'column-attribute_thumbnail';
				if ( url ) {
					td.innerHTML = '<img src="' + url + '" alt="">';
				} else {
					td.innerHTML = '<span style="color:#999">&mdash;</span>';
				}
				row.insertBefore( td, row.children[1] );

				// Align all other cells in this row to match
				row.querySelectorAll( 'td' ).forEach( function ( cell ) {
					cell.style.verticalAlign = 'middle';
				} );
			} );
		} )();
		</script>
		<?php
	}

	// ─── Helpers ────────────────────────────────────────────────────────

	/**
	 * Get the image attachment ID for a given attribute ID.
	 */
	public static function get_image_id( int $attribute_id ): int {
		return (int) get_option( self::OPTION_PREFIX . $attribute_id, 0 );
	}

	/**
	 * Get the image URL for a given attribute ID.
	 *
	 * @param int          $attribute_id Attribute ID.
	 * @param string|array $size         Image size.
	 */
	public static function get_image_url( int $attribute_id, $size = 'thumbnail' ): string {
		$image_id = self::get_image_id( $attribute_id );
		return $image_id ? ( wp_get_attachment_image_url( $image_id, $size ) ?: '' ) : '';
	}
}
