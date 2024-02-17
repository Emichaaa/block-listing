<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://growscratch.com
 * @since      1.0.0
 *
 * @package    Block_Listing
 * @subpackage Block_Listing/admin/partials
 */
?>
<div class="wrap">
    <h2><?php echo __('Block Listing Shortcode Usage', 'block-listing'); ?></h2>
    <p><?php echo __('Use the [bl_display_block_list] shortcode to list blocks in your posts and pages. You can customize the output with the following attributes:', 'block-listing'); ?></p>
    <ul>
        <li><strong>post_type</strong><?php echo __(': Specify one or multiple post types, separated by commas. If omitted, all post types are considered.', 'block-listing'); ?></li>
        <li><strong>post_id</strong><?php echo __(': Specify a single post ID to list blocks from a specific post. If omitted, blocks from all posts of the specified post types are listed.', 'block-listing'); ?></li>
        <li><strong>unique_blocks</strong><?php echo __(': Set to true to list only unique blocks across all specified posts. Default is false.', 'block-listing'); ?></li>
    </ul>
    <p><?php echo __('Example usage:', 'block-listing'); ?></p>
    <p><code>[bl_display_block_list]</code></p>
    <p><code>[bl_display_block_list post_type="post,page"]</code></p>
    <p><code>[bl_display_block_list post_id="123"]</code></p>
    <p><code>[bl_display_block_list unique_blocks="true"]</code></p>
    <p><code>[bl_display_block_list post_type="post,page" unique_blocks="true"]</code></p>
    <p><?php echo __('This shortcode will list all unique blocks used in posts and pages.', 'block-listing'); ?></p></p>
</div>
