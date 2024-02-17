<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://growscratch.com
 * @since      1.0.0
 *
 * @package    Block_Listing
 * @subpackage Block_Listing/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Block_Listing
 * @subpackage Block_Listing/includes
 * @author     Emicha <emil.atanasov@growscratch.com>
 */
class Block_Listing {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Block_Listing_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'BLOCK_LISTING_VERSION' ) ) {
			$this->version = BLOCK_LISTING_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'block-listing';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Block_Listing_Loader. Orchestrates the hooks of the plugin.
	 * - Block_Listing_i18n. Defines internationalization functionality.
	 * - Block_Listing_Admin. Defines all hooks for the admin area.
	 * - Block_Listing_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-block-listing-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-block-listing-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-block-listing-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-block-listing-public.php';

		$this->loader = new Block_Listing_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Block_Listing_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Block_Listing_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Block_Listing_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Block_Listing_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Block_Listing_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}


	public function bl_list_blocks_by_post($post_id) {
		// Function to recursively get block names, including deeply nested blocks

		// Get the post content by ID
		$post_content = get_post_field('post_content', $post_id);

		// Parse the blocks from the content
		$blocks = parse_blocks($post_content);

		// Initialize an array to hold the list of used block names
		$used_blocks = array();

		// Recursively get names of all blocks and nested blocks
		$this->bl_get_block_names($blocks, $used_blocks);

		// Remove duplicates and return the list of used block names
		return array_unique($used_blocks);
	}

	public function bl_get_block_names($blocks, &$used_blocks) {
		foreach ($blocks as $block) {
			if (!empty($block['blockName'])) {
				// Add the block name to the used blocks array
				$used_blocks[] = $block['blockName'];
			}
			// Check for innerBlocks recursively
			if (!empty($block['innerBlocks'])) {
				$this->bl_get_block_names($block['innerBlocks'], $used_blocks);
			} elseif (!empty($block['attrs']['ref'])) {
				// Handle reusable blocks: Fetch and parse their content
				$reusable_blocks_content = get_post_field('post_content', $block['attrs']['ref']);
				if (!empty($reusable_blocks_content)) {
					$reusable_blocks = parse_blocks($reusable_blocks_content);
					$this->bl_get_block_names($reusable_blocks, $used_blocks);
				}
			}
		}
	}

	public function bl_get_all_posts_of_post_type($post_types) {
		$result = array();

		// If no specific post types are provided, get all public post types, including custom ones
		if (empty($post_types)) {
			$args = array(
				'public'   => true,
				'_builtin' => false // Fetch only custom post types; setting it to false excludes built-in types
			);
			$custom_post_types = get_post_types($args, 'names', 'and');

			// Get built-in post types but exclude attachments
			$builtin_post_types = get_post_types(['public' => true, '_builtin' => true], 'names', 'and');
			unset($builtin_post_types['attachment']); // Exclude 'attachment' post type

			// Merge custom and filtered built-in post types
			$post_types = array_merge($custom_post_types, $builtin_post_types);
		}

		foreach ($post_types as $post_type) {
			$args = array(
				'post_type'      => $post_type,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'suppress_filters' => true,
			);

			$query = new WP_Query($args);

			if ($query->have_posts()) {
				$result[$post_type] = $query->posts;
			} else {
				$result[$post_type] = array();
			}
		}

		return $result;
	}
}
