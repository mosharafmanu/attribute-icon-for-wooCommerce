<?php
/**
 * Uninstall handler — runs when plugin is deleted via WordPress admin.
 *
 * Removes all attribute image options from the database.
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

call_user_func(
	static function (): void {
		global $wpdb;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery
		$option_names = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
				$wpdb->esc_like( 'wc_attribute_image_' ) . '%'
			)
		);
		// phpcs:enable

		if ( empty( $option_names ) ) {
			return;
		}

		foreach ( $option_names as $option_name ) {
			delete_option( $option_name );
		}

		wp_cache_flush();
	}
);
