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

	/**
	 * Get all unique blocks used across the entire site with page links
	 *
	 * @since    2.0.0
	 * @return   array    Array of blocks with usage information
	 */
	public function bl_get_all_site_blocks() {
		$blocks_data = array();
		$post_types = $this->bl_get_all_posts_of_post_type(array());

		foreach ($post_types as $type => $posts) {
			foreach ($posts as $post_id) {
				$blocks = $this->bl_list_blocks_by_post($post_id);
				foreach ($blocks as $block) {
					if (!isset($blocks_data[$block])) {
						$blocks_data[$block] = array(
							'name' => $block,
							'pages' => array()
						);
					}
					$blocks_data[$block]['pages'][] = array(
						'id' => $post_id,
						'title' => get_the_title($post_id),
						'edit_link' => get_edit_post_link($post_id),
						'view_link' => get_permalink($post_id),
						'post_type' => $type
					);
				}
			}
		}

		return $blocks_data;
	}

	/**
	 * Get all registered Gutenberg patterns with content
	 *
	 * @since    2.0.0
	 * @return   array    Array of pattern information
	 */
	public function bl_get_all_patterns() {
		$patterns = array();

		if (function_exists('WP_Block_Patterns_Registry::get_instance')) {
			$registry = WP_Block_Patterns_Registry::get_instance();
			$all_patterns = $registry->get_all_registered();

			foreach ($all_patterns as $pattern) {
				$patterns[] = array(
					'name' => $pattern['name'],
					'title' => $pattern['title'],
					'categories' => isset($pattern['categories']) ? $pattern['categories'] : array(),
					'description' => isset($pattern['description']) ? $pattern['description'] : '',
					'content' => isset($pattern['content']) ? $pattern['content'] : '',
				);
			}
		}

		return $patterns;
	}

	/**
	 * Get all reusable blocks (wp_block post type)
	 *
	 * @since    2.0.0
	 * @return   array    Array of reusable blocks
	 */
	public function bl_get_all_reusable_blocks() {
		$args = array(
			'post_type'      => 'wp_block',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);

		$query = new WP_Query($args);
		$reusable_blocks = array();

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$reusable_blocks[] = array(
					'id' => get_the_ID(),
					'title' => get_the_title(),
					'edit_link' => get_edit_post_link(get_the_ID()),
				);
			}
			wp_reset_postdata();
		}

		return $reusable_blocks;
	}

	/**
	 * Get all theme font sizes from theme.json and editor settings
	 *
	 * @since    2.0.0
	 * @return   array    Array of font sizes
	 */
	public function bl_get_theme_font_sizes() {
		$font_sizes = array();

		// Get font sizes from theme.json (for block themes)
		if (function_exists('wp_get_global_settings')) {
			$global_settings = wp_get_global_settings();
			if (isset($global_settings['typography']['fontSizes'])) {
				$font_sizes = array_merge($font_sizes, $global_settings['typography']['fontSizes']);
			}
		}

		// Get font sizes from editor settings (for classic themes)
		$editor_settings = get_theme_support('editor-font-sizes');
		if ($editor_settings && is_array($editor_settings[0])) {
			$font_sizes = array_merge($font_sizes, $editor_settings[0]);
		}

		// Remove duplicates based on slug
		$unique_sizes = array();
		foreach ($font_sizes as $size) {
			if (isset($size['slug'])) {
				$unique_sizes[$size['slug']] = $size;
			}
		}

		return array_values($unique_sizes);
	}

	/**
	 * Get all theme colors from theme.json and editor settings
	 *
	 * @since    2.0.0
	 * @return   array    Array of colors
	 */
	public function bl_get_theme_colors() {
		$colors = array();

		// Get colors from theme.json (for block themes)
		if (function_exists('wp_get_global_settings')) {
			$global_settings = wp_get_global_settings();
			if (isset($global_settings['color']['palette'])) {
				$colors = array_merge($colors, $global_settings['color']['palette']);
			}
		}

		// Get colors from editor settings (for classic themes)
		$editor_colors = get_theme_support('editor-color-palette');
		if ($editor_colors && is_array($editor_colors[0])) {
			$colors = array_merge($colors, $editor_colors[0]);
		}

		// Remove duplicates based on slug
		$unique_colors = array();
		foreach ($colors as $color) {
			if (isset($color['slug'])) {
				$unique_colors[$color['slug']] = $color;
			}
		}

		return array_values($unique_colors);
	}

	/**
	 * Get CSS classes from theme stylesheets including SCSS files
	 *
	 * @since    2.0.0
	 * @return   array    Array of CSS classes
	 */
	public function bl_get_theme_css_classes() {
		$classes = array();
		$stylesheet_dir = get_stylesheet_directory();

		// Common CSS file locations
		$css_files = array(
			$stylesheet_dir . '/style.css',
			$stylesheet_dir . '/assets/css/style.css',
			$stylesheet_dir . '/css/style.css',
		);

		// Find SCSS files in theme (excluding node_modules)
		$scss_files = glob($stylesheet_dir . '/{assets,gutenberg-blocks}/**/*.scss', GLOB_BRACE);
		if ($scss_files) {
			foreach ($scss_files as $scss_file) {
				if (strpos($scss_file, 'node_modules') === false) {
					$css_files[] = $scss_file;
				}
			}
		}

		// Add theme.json classes if available
		if (function_exists('wp_get_global_stylesheet')) {
			$global_styles = wp_get_global_stylesheet();
			preg_match_all('/\.([a-zA-Z0-9_-]+)\s*\{/', $global_styles, $matches);
			if (!empty($matches[1])) {
				$classes = array_merge($classes, $matches[1]);
			}
		}

		// Parse CSS/SCSS files
		foreach ($css_files as $file) {
			if (file_exists($file)) {
				$content = file_get_contents($file);
				// Extract class names (basic regex - may not catch all edge cases)
				preg_match_all('/\.([a-zA-Z0-9_-]+)(?:\s|:|,|\{|&)/', $content, $matches);
				if (!empty($matches[1])) {
					$classes = array_merge($classes, $matches[1]);
				}
			}
		}

		// Remove duplicates and sort
		$classes = array_unique($classes);
		sort($classes);

		// Return all classes (removed limit)
		return $classes;
	}

	/**
	 * Get all reusable blocks with usage information
	 *
	 * @since    2.0.0
	 * @return   array    Array of reusable blocks with page usage
	 */
	public function bl_get_reusable_blocks_with_usage() {
		$args = array(
			'post_type'      => 'wp_block',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);

		$query = new WP_Query($args);
		$reusable_blocks = array();

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$block_id = get_the_ID();
				$reusable_blocks[] = array(
					'id' => $block_id,
					'title' => get_the_title(),
					'content' => get_post_field('post_content', $block_id),
					'edit_link' => get_edit_post_link($block_id),
					'usage' => $this->bl_find_reusable_block_usage($block_id),
				);
			}
			wp_reset_postdata();
		}

		return $reusable_blocks;
	}

	/**
	 * Find where a reusable block is used
	 *
	 * @since    2.0.0
	 * @param    int    $block_id    The reusable block ID
	 * @return   array    Array of pages using this block
	 */
	private function bl_find_reusable_block_usage($block_id) {
		$usage = array();
		$post_types = $this->bl_get_all_posts_of_post_type(array());

		foreach ($post_types as $type => $posts) {
			foreach ($posts as $post_id) {
				$content = get_post_field('post_content', $post_id);
				// Check if the reusable block ID is referenced
				if (strpos($content, '"ref":' . $block_id) !== false || strpos($content, '"ref":"' . $block_id . '"') !== false) {
					$usage[] = array(
						'id' => $post_id,
						'title' => get_the_title($post_id),
						'edit_link' => get_edit_post_link($post_id),
						'view_link' => get_permalink($post_id),
						'post_type' => $type
					);
				}
			}
		}

		return $usage;
	}

	/**
	 * Get all custom Gutenberg blocks from theme
	 *
	 * @since    2.0.0
	 * @return   array    Array of custom blocks with metadata
	 */
	public function bl_get_custom_blocks() {
		$custom_blocks = array();
		$theme_dir = get_stylesheet_directory();
		$blocks_dir = $theme_dir . '/gutenberg-blocks';

		if (!is_dir($blocks_dir)) {
			return $custom_blocks;
		}

		$block_dirs = glob($blocks_dir . '/*', GLOB_ONLYDIR);

		foreach ($block_dirs as $block_dir) {
			$block_name = basename($block_dir);
			$block_json_path = $block_dir . '/build/' . $block_name . '/block.json';

			// Try alternative path
			if (!file_exists($block_json_path)) {
				$block_json_path = $block_dir . '/block.json';
			}

			if (file_exists($block_json_path)) {
				$block_json = json_decode(file_get_contents($block_json_path), true);
				
				$block_full_name = isset($block_json['name']) ? $block_json['name'] : $block_name;

				$custom_blocks[] = array(
					'name' => $block_full_name,
					'title' => isset($block_json['title']) ? $block_json['title'] : ucfirst(str_replace('-', ' ', $block_name)),
					'description' => isset($block_json['description']) ? $block_json['description'] : '',
					'category' => isset($block_json['category']) ? $block_json['category'] : '',
					'icon' => isset($block_json['icon']) ? $block_json['icon'] : '',
					'attributes' => isset($block_json['attributes']) ? $block_json['attributes'] : array(),
					'example' => isset($block_json['example']) ? $block_json['example'] : null,
					'supports' => isset($block_json['supports']) ? $block_json['supports'] : array(),
					'dir_path' => $block_dir,
					'real_example' => $this->bl_find_real_block_example($block_full_name),
				);
			}
		}

		return $custom_blocks;
	}
	
	/**
	 * Find a real example of a block being used in posts
	 *
	 * @since    2.0.0
	 * @param    string    $block_name    The block name to search for
	 * @return   array|null    Array with block markup and attributes, or null
	 */
	private function bl_find_real_block_example($block_name) {
		$post_types = $this->bl_get_all_posts_of_post_type(array());
		
		foreach ($post_types as $type => $posts) {
			foreach ($posts as $post_id) {
				$content = get_post_field('post_content', $post_id);
				$blocks = parse_blocks($content);
				
				$found_block = $this->bl_search_blocks_recursively($blocks, $block_name);
				if ($found_block) {
					return $found_block;
				}
			}
		}
		
		return null;
	}
	
	/**
	 * Recursively search through blocks to find a specific block type
	 *
	 * @since    2.0.0
	 * @param    array     $blocks       Array of parsed blocks
	 * @param    string    $block_name   Block name to find
	 * @return   array|null    Found block data or null
	 */
	private function bl_search_blocks_recursively($blocks, $block_name) {
		foreach ($blocks as $block) {
			if (!empty($block['blockName']) && $block['blockName'] === $block_name) {
				return array(
					'blockName' => $block['blockName'],
					'attrs' => isset($block['attrs']) ? $block['attrs'] : array(),
					'innerHTML' => isset($block['innerHTML']) ? $block['innerHTML'] : '',
					'innerContent' => isset($block['innerContent']) ? $block['innerContent'] : array(),
					'innerBlocks' => isset($block['innerBlocks']) ? $block['innerBlocks'] : array(),
				);
			}
			
			// Search in inner blocks
			if (!empty($block['innerBlocks'])) {
				$found = $this->bl_search_blocks_recursively($block['innerBlocks'], $block_name);
				if ($found) {
					return $found;
				}
			}
		}
		
		return null;
	}
}
