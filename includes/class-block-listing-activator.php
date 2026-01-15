<?php

/**
 * Fired during plugin activation
 *
 * @link       https://growscratch.com
 * @since      1.0.0
 *
 * @package    Block_Listing
 * @subpackage Block_Listing/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Block_Listing
 * @subpackage Block_Listing/includes
 * @author     Emicha <emil.atanasov@growscratch.com>
 */
class Block_Listing_Activator {

	/**
	 * Plugin activation hook
	 *
	 * Creates the Kitchen Sink page automatically on plugin activation
	 *
	 * @since    2.0.0
	 */
	public static function activate() {
		self::create_kitchen_sink_page();
	}

	/**
	 * Create Kitchen Sink page
	 *
	 * Creates a page with the kitchen_sink shortcode if it doesn't exist
	 *
	 * @since    2.0.0
	 */
	private static function create_kitchen_sink_page() {
		// Check if page already exists
		$existing_page_id = get_option('bl_kitchen_sink_page_id');

		if ($existing_page_id) {
			$existing_page = get_post($existing_page_id);
			if ($existing_page && $existing_page->post_status !== 'trash') {
				// Page already exists and is not trashed
				return;
			}
		}

		// Create the page
		$page_data = array(
			'post_title'    => __('Kitchen Sink', 'block-listing'),
			'post_content'  => '[kitchen_sink]',
			'post_status'   => 'publish',
			'post_type'     => 'page',
			'post_author'   => 1,
			'comment_status' => 'closed',
			'ping_status'   => 'closed',
		);

		$page_id = wp_insert_post($page_data);

		if ($page_id && !is_wp_error($page_id)) {
			// Store the page ID in options
			update_option('bl_kitchen_sink_page_id', $page_id);

			// Add a meta field to identify this as a Kitchen Sink page
			update_post_meta($page_id, '_bl_kitchen_sink_page', true);
		}
	}

}
