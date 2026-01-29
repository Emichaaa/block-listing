<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://growscratch.com
 * @since      1.0.0
 *
 * @package    Block_Listing
 * @subpackage Block_Listing/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Block_Listing
 * @subpackage Block_Listing/admin
 * @author     Emicha <emil.atanasov@growscratch.com>
 */
class Block_Listing_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action('admin_menu', array($this, 'bl_add_plugin_admin_menu'));
		add_filter('plugin_action_links_' . BLOCK_LISTING_BASENAME, array($this, 'bl_apd_settings_link') );
		add_action('wp_ajax_bl_export_blocks_csv', array($this, 'bl_export_blocks_csv'));
		add_action('wp_ajax_bl_load_blocks_chunk', array($this, 'bl_load_blocks_chunk'));
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Block_Listing_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Block_Listing_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/block-listing-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Block_Listing_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Block_Listing_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/block-listing-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function bl_add_plugin_admin_menu() {
		add_menu_page(
			'Block Listing Settings', // Page title
			'Block Listing', // Menu title
			'manage_options', // Capability
			'block-listing-settings', // Menu slug
			array($this, 'bl_display_plugin_admin_page'), // Function to display the admin page
			'dashicons-admin-generic'
		);

		// Add Kitchen Sink submenu
		add_submenu_page(
			'block-listing-settings', // Parent slug
			__('Kitchen Sink', 'block-listing'), // Page title
			__('Kitchen Sink', 'block-listing'), // Menu title
			'manage_options', // Capability
			'block-listing-kitchen-sink', // Menu slug
			array($this, 'bl_display_kitchen_sink_page') // Function to display the page
		);
	}

	public function bl_display_plugin_admin_page() {
		include plugin_dir_path(__FILE__) . 'partials/block-listing-admin-display.php';
	}

	public function bl_display_kitchen_sink_page() {
		// Enqueue block editor styles for proper preview rendering
		wp_enqueue_style('wp-block-library');
		wp_enqueue_style('wp-block-library-theme');
		
		// Enqueue theme styles if available
		if (function_exists('wp_enqueue_block_style')) {
			wp_enqueue_style('global-styles');
		}
		
		include plugin_dir_path(__FILE__) . 'partials/block-listing-kitchen-sink-display.php';
	}

	public function bl_apd_settings_link($links) {
		$settings_link = '<a href="' . admin_url('options-general.php?page=block-listing-settings') . '">Settings</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	/**
	 * Export blocks and their pages to CSV
	 *
	 * @since    2.0.0
	 */
	public function bl_export_blocks_csv() {
		// Check nonce for security
		check_ajax_referer('bl_export_blocks_nonce', 'nonce');

		// Check user capabilities
		if (!current_user_can('manage_options')) {
			wp_send_json_error(array('message' => __('Unauthorized access', 'block-listing')));
			return;
		}

		// Get all blocks data
		$block_listing = new Block_Listing();
		$all_blocks = $block_listing->bl_get_all_site_blocks();

		if (empty($all_blocks)) {
			wp_send_json_error(array('message' => __('No blocks found to export', 'block-listing')));
			return;
		}

		// Prepare CSV data
		$csv_data = array();

		// Add header row
		$csv_data[] = array(
			__('Block Name', 'block-listing'),
			__('Page Title', 'block-listing'),
			__('Post Type', 'block-listing'),
			__('Edit Link', 'block-listing'),
			__('View Link', 'block-listing')
		);

		// Add data rows
		foreach ($all_blocks as $block_name => $block_data) {
			if (!empty($block_data['pages'])) {
				foreach ($block_data['pages'] as $page) {
					$csv_data[] = array(
						$block_data['name'],
						$page['title'],
						$page['post_type'],
						$page['edit_link'],
						$page['view_link']
					);
				}
			}
		}

		// Generate CSV content
		$output = fopen('php://temp', 'r+');
		foreach ($csv_data as $row) {
			fputcsv($output, $row);
		}
		rewind($output);
		$csv_content = stream_get_contents($output);
		fclose($output);

		// Return CSV content as JSON
		wp_send_json_success(array(
			'csv' => $csv_content,
			'filename' => 'blocks-usage-' . date('Y-m-d-His') . '.csv'
		));
	}

	/**
	 * Load blocks in chunks via AJAX
	 *
	 * @since    2.0.0
	 */
	public function bl_load_blocks_chunk() {
		// Check nonce for security
		check_ajax_referer('bl_blocks_nonce', 'nonce');

		// Check user capabilities
		if (!current_user_can('manage_options')) {
			wp_send_json_error(array('message' => __('Unauthorized access', 'block-listing')));
			return;
		}

		$offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
		$limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;

		// Get all blocks data
		$block_listing = new Block_Listing();
		$all_blocks = $block_listing->bl_get_all_site_blocks();

		if (empty($all_blocks)) {
			wp_send_json_success(array(
				'blocks' => array(),
				'total' => 0,
				'has_more' => false
			));
			return;
		}

		// Get total count
		$total_blocks = count($all_blocks);

		// Slice array to get current chunk
		$blocks_chunk = array_slice($all_blocks, $offset, $limit, true);

		// Prepare HTML for blocks
		$html = '';
		foreach ($blocks_chunk as $block_name => $block_data) {
			ob_start();
			?>
			<div class="bl-block-with-pages">
				<div class="bl-block-header">
					<code class="bl-block-name"><?php echo esc_html($block_data['name']); ?></code>
					<span class="bl-usage-count"><?php echo sprintf(__('Used on %d page(s)', 'block-listing'), count($block_data['pages'])); ?></span>
				</div>
				<div class="bl-block-pages">
					<?php if (count($block_data['pages']) <= 3): ?>
						<?php foreach ($block_data['pages'] as $page): ?>
							<div class="bl-page-link">
								<a href="<?php echo esc_url($page['edit_link']); ?>" target="_blank" title="<?php echo __('Edit', 'block-listing'); ?>">
									<span class="dashicons dashicons-edit"></span>
								</a>
								<a href="<?php echo esc_url($page['view_link']); ?>" target="_blank" title="<?php echo __('View', 'block-listing'); ?>">
									<?php echo esc_html($page['title']); ?>
								</a>
								<span class="bl-post-type">(<?php echo esc_html($page['post_type']); ?>)</span>
							</div>
						<?php endforeach; ?>
					<?php else: ?>
						<?php
						$first_pages = array_slice($block_data['pages'], 0, 3);
						$remaining_pages = array_slice($block_data['pages'], 3);
						?>
						<?php foreach ($first_pages as $page): ?>
							<div class="bl-page-link">
								<a href="<?php echo esc_url($page['edit_link']); ?>" target="_blank" title="<?php echo __('Edit', 'block-listing'); ?>">
									<span class="dashicons dashicons-edit"></span>
								</a>
								<a href="<?php echo esc_url($page['view_link']); ?>" target="_blank" title="<?php echo __('View', 'block-listing'); ?>">
									<?php echo esc_html($page['title']); ?>
								</a>
								<span class="bl-post-type">(<?php echo esc_html($page['post_type']); ?>)</span>
							</div>
						<?php endforeach; ?>

						<details class="bl-more-pages-details">
							<summary class="bl-show-more-pages">
								<?php echo sprintf(__('+ Show %d more page(s)', 'block-listing'), count($remaining_pages)); ?>
							</summary>
							<div class="bl-remaining-pages">
								<?php foreach ($remaining_pages as $page): ?>
									<div class="bl-page-link">
										<a href="<?php echo esc_url($page['edit_link']); ?>" target="_blank" title="<?php echo __('Edit', 'block-listing'); ?>">
											<span class="dashicons dashicons-edit"></span>
										</a>
										<a href="<?php echo esc_url($page['view_link']); ?>" target="_blank" title="<?php echo __('View', 'block-listing'); ?>">
											<?php echo esc_html($page['title']); ?>
										</a>
										<span class="bl-post-type">(<?php echo esc_html($page['post_type']); ?>)</span>
									</div>
								<?php endforeach; ?>
							</div>
						</details>
					<?php endif; ?>
				</div>
			</div>
			<?php
			$html .= ob_get_clean();
		}

		wp_send_json_success(array(
			'html' => $html,
			'total' => $total_blocks,
			'loaded' => $offset + count($blocks_chunk),
			'has_more' => ($offset + $limit) < $total_blocks
		));
	}
}
