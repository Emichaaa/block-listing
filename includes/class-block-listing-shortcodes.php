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
 * The shordcodes plugin class.
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
class Block_Listing_Shortcodes {

	public function __construct() {
		add_action('init', array($this, 'register_shortcodes'));
	}

	public function bl_display_block_list_shortcode($atts) {
		// Extend shortcode attributes with unique_blocks
		$atts = shortcode_atts(
			array(
				'post_type' => '', // empty default post type
				'post_id' => '',   // empty default post ID
				'unique_blocks' => false, // Add unique_blocks attribute, default to false
			),
			$atts,
			'bl_display_block_list'
		);

		$post_type = $atts['post_type'];
		$post_id = $atts['post_id'];
		$unique_blocks = $atts['unique_blocks'];

		// Initialize Block_Listing instance
		$block_list = new Block_Listing();
		$all_used_blocks = []; // Array to collect all used blocks

		$output = "";
		// Check if post_id is provided
		if (!empty($post_id)) {
			$used_blocks = $block_list->bl_list_blocks_by_post($post_id);
			if ($unique_blocks) {
				$used_blocks = array_unique($used_blocks);
			}
			// Format the output for used blocks
			$output = $this->format_blocks_output($post_id, $used_blocks);
			$output = "<div class='block-listing' style='padding: 40px; display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: flex-start; gap: 32px'>". $output . "</div>";

			return $output;
		} else {
			// Handle by post type(s)
			$post_types = !empty($post_type) ? explode(',', $post_type) : array();
			$post_type_posts = $block_list->bl_get_all_posts_of_post_type($post_types);

			foreach ($post_type_posts as $type => $posts) {
				foreach ($posts as $pid) {
					$used_blocks = $block_list->bl_list_blocks_by_post($pid);
					// Optionally filter for unique blocks
					if ($unique_blocks) {
						$all_used_blocks = array_merge($all_used_blocks, $used_blocks);
					} else {
						$output .= $this->format_blocks_output($pid, $used_blocks);
					}
				}
			}

			if ($unique_blocks) {
				$all_used_blocks = array_unique($all_used_blocks);
				$output = $this->format_blocks_output(false, $all_used_blocks);
			}

			$output = "<div class='block-listing' style='padding: 40px; display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: flex-start; gap: 32px'>". $output . "</div>";

			return !empty($output) ? $output : 'No blocks found.';
		}
	}

	// Method to register the shortcode
	public function register_shortcodes() {
		add_shortcode('bl_display_block_list', array($this, 'bl_display_block_list_shortcode'));
	}

// Helper function to format the output of blocks in a styled table with page titles and IDs as separate columns
	private function format_blocks_output($post_ids, $blocks) {
		// Fetch the post title for each post ID
		$post_title = ( $post_ids ? get_the_title($post_ids) : "Unique blocks for this parameters");

		// Initialize the table with headers for Post Title, Post ID, and Block Name
		$output = '<table style="width:450px; border-collapse: collapse; border: 1px solid #ccc; margin-top: 10px; font-family: Arial, sans-serif;">';
		$output .= '<thead>';
		$output .= '<tr style="background-color: #f2f2f2;">';
		if( $post_ids ){
			$output .= '<th style="border: 1px solid #ddd; padding: 10px 15px; text-align: left;"><a href="'.get_the_permalink($post_ids).'">' . esc_html($post_title) . "</a> ( ID: <a href='".get_edit_post_link($post_ids)."'>" . esc_html($post_ids) . "</a> ) " . '</th>';
		}
		else{
			$output .= '<th style="border: 1px solid #ddd; padding: 10px 15px; text-align: left;">' . esc_html($post_title) . '</th>';
		}
		$output .= '</tr>';
		$output .= '</thead>';
		$output .= '<tbody>';

		// Check if blocks are provided
		if (!empty($blocks)) {
			foreach ($blocks as $block) {

				$output .= '<tr>';
				$output .= '<td style="border: 1px solid #ddd; padding: 8px 15px;">' . esc_html($block) . '</td>';
				$output .= '</tr>';
			}
		} else {
			// Display a message if no blocks are found
			$output .= '<tr><td colspan="3" style="border: 1px solid #ddd; padding: 8px 15px; text-align: center;">No blocks found.</td></tr>';
		}

		// Close the table
		$output .= '</tbody></table>';

		return $output;
	}
}
