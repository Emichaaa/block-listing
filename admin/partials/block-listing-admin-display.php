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
    <h2>Block Listing Shortcode Usage</h2>
    <p>Use the [bl_display_block_list] shortcode to list blocks in your posts and pages. You can customize the output with the following attributes:</p>
    <ul>
        <li><strong>post_type</strong>: Specify one or multiple post types, separated by commas. If omitted, all post types are considered.</li>
        <li><strong>post_id</strong>: Specify a single post ID to list blocks from a specific post. If omitted, blocks from all posts of the specified post types are listed.</li>
        <li><strong>unique_blocks</strong>: Set to true to list only unique blocks across all specified posts. Default is false.</li>
    </ul>
    <p>Example usage:</p>
    <p><code>[bl_display_block_list]</code></p>
    <p><code>[bl_display_block_list post_type="post,page"]</code></p>
    <p><code>[bl_display_block_list post_id="123"]</code></p>
    <p><code>[bl_display_block_list unique_blocks="true"]</code></p>
    <p><code>[bl_display_block_list post_type="post,page" unique_blocks="true"]</code></p>
    <p>This shortcode will list all unique blocks used in posts and pages.</p></p>
</div>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
