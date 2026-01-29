<?php

/**
 * Provide a Kitchen Sink admin view for the plugin
 *
 * This file displays all blocks, patterns, reusable blocks, CSS classes,
 * font sizes, and colors used in the theme.
 *
 * @link       https://growscratch.com
 * @since      2.0.0
 *
 * @package    Block_Listing
 * @subpackage Block_Listing/admin/partials
 */

// Initialize Block_Listing instance
$block_listing = new Block_Listing();

// Get all data
$all_blocks = $block_listing->bl_get_all_site_blocks();
$all_patterns = $block_listing->bl_get_all_patterns();
$reusable_blocks = $block_listing->bl_get_reusable_blocks_with_usage();
$custom_blocks = $block_listing->bl_get_custom_blocks();
$theme_colors = $block_listing->bl_get_theme_colors();
$theme_font_sizes = $block_listing->bl_get_theme_font_sizes();
$theme_classes = $block_listing->bl_get_theme_css_classes();

?>
<div class="wrap bl-kitchen-sink">
    <h1><?php echo __('Kitchen Sink', 'block-listing'); ?></h1>
    <p class="description"><?php echo __('A comprehensive overview of all blocks, patterns, reusable blocks, CSS classes, font sizes, and colors available in your theme.', 'block-listing'); ?></p>
    
    <div class="notice notice-info" style="margin: 20px 0;">
        <p><strong><?php echo __('💡 How to use:', 'block-listing'); ?></strong></p>
        <ul style="margin-left: 20px; list-style: disc;">
            <li><?php echo __('Browse through patterns, reusable blocks, and custom blocks below', 'block-listing'); ?></li>
            <li><?php echo __('Click "Copy Gutenberg Code" to copy the block markup', 'block-listing'); ?></li>
            <li><?php echo __('Paste the copied code into any WordPress page/post editor (in Code Editor mode)', 'block-listing'); ?></li>
            <li><?php echo __('Switch back to Visual Editor to see your blocks rendered', 'block-listing'); ?></li>
            <li><?php echo __('Preview sections show how blocks will appear on your site', 'block-listing'); ?></li>
        </ul>
    </div>

    <div class="bl-kitchen-sink-grid">

        <!-- All Blocks Section -->
        <div class="bl-section bl-blocks-section">
            <div class="bl-section-header-with-button">
                <div>
                    <h2><span class="dashicons dashicons-block-default"></span> <?php echo __('All Blocks Used', 'block-listing'); ?></h2>
                    <p class="section-description">
                        <span id="bl-blocks-count-text"><?php echo sprintf(__('Total: %d unique blocks', 'block-listing'), count($all_blocks)); ?></span>
                        <span id="bl-blocks-loaded-text" style="display:none; margin-left: 10px; color: #2271b1;"></span>
                    </p>
                </div>
                <button id="bl-export-blocks-csv" class="button button-primary bl-export-btn">
                    <span class="dashicons dashicons-download"></span> <?php echo __('Export to CSV', 'block-listing'); ?>
                </button>
            </div>
            <div class="bl-items-container" id="bl-blocks-container" data-loaded="0">
                <div class="bl-loading-initial" style="text-align: center; padding: 40px; color: #666;">
                    <span class="dashicons dashicons-update-alt bl-spin" style="font-size: 32px; width: 32px; height: 32px;"></span>
                    <p><?php echo __('Loading blocks...', 'block-listing'); ?></p>
                </div>
                <?php if (!empty($all_blocks)): ?>
                    <div id="bl-blocks-list" style="display:none;">
                    <?php foreach ($all_blocks as $block_name => $block_data): ?>
                        <div class="bl-block-with-pages">
                            <div class="bl-block-header">
                                <code class="bl-block-name"><?php echo esc_html($block_data['name']); ?></code>
                                <span class="bl-usage-count"><?php echo sprintf(__('Used on %d page(s)', 'block-listing'), count($block_data['pages'])); ?></span>
                            </div>
                            <div class="bl-block-pages">
                                <?php if (count($block_data['pages']) <= 3): ?>
                                    <?php // Show all pages if 3 or fewer ?>
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
                                    <?php // Show first 3, then collapsible section for the rest ?>
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
                    <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-items"><?php echo __('No blocks found.', 'block-listing'); ?></p>
                <?php endif; ?>
                <div id="bl-load-more-container" style="display:none; text-align: center; padding: 20px;">
                    <button id="bl-load-more-blocks" class="button button-secondary">
                        <span class="dashicons dashicons-update-alt"></span> <?php echo __('Load More Blocks', 'block-listing'); ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- Custom Blocks Section -->
        <?php if (!empty($custom_blocks)): ?>
        <div class="bl-section bl-custom-blocks-section bl-section-full-width">
            <h2><span class="dashicons dashicons-welcome-widgets-menus"></span> <?php echo __('Custom Theme Blocks', 'block-listing'); ?></h2>
            <p class="section-description"><?php echo sprintf(__('Total: %d custom blocks', 'block-listing'), count($custom_blocks)); ?></p>
            <div class="bl-items-container">
                <?php foreach ($custom_blocks as $block): ?>
                    <div class="bl-custom-block-item">
                        <div class="bl-custom-block-header">
                            <h3><?php echo esc_html($block['title']); ?></h3>
                            <code><?php echo esc_html($block['name']); ?></code>
                        </div>
                        <?php if (!empty($block['description'])): ?>
                            <p class="bl-block-description"><?php echo esc_html($block['description']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($block['attributes'])): ?>
                            <details class="bl-block-attributes">
                                <summary><?php echo __('Attributes', 'block-listing'); ?> (<?php echo count($block['attributes']); ?>)</summary>
                                <ul class="bl-attributes-list">
                                    <?php foreach ($block['attributes'] as $attr_name => $attr_config): ?>
                                        <li>
                                            <strong><?php echo esc_html($attr_name); ?></strong>
                                            <span class="attr-type"><?php echo isset($attr_config['type']) ? esc_html($attr_config['type']) : 'any'; ?></span>
                                            <?php if (isset($attr_config['default'])): ?>
                                                <span class="attr-default">Default: <code><?php echo esc_html(json_encode($attr_config['default'])); ?></code></span>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </details>
                        <?php endif; ?>
                        <?php
                        // Determine which example to use: real example from posts or default/example from block.json
                        $use_real_example = !empty($block['real_example']);
                        $block_markup = '';
                        
                        if ($use_real_example) {
                            // Use real example found in posts
                            $real_block = $block['real_example'];
                            $block_markup = serialize_block($real_block);
                        } else {
                            // Fallback to example or default attributes
                            if (!empty($block['example']['attributes'])) {
                                $example_attrs = $block['example']['attributes'];
                            } else {
                                // Build default attributes from block.json
                                $example_attrs = array();
                                if (!empty($block['attributes'])) {
                                    foreach ($block['attributes'] as $attr_name => $attr_config) {
                                        if (isset($attr_config['default'])) {
                                            $example_attrs[$attr_name] = $attr_config['default'];
                                        }
                                    }
                                }
                            }
                            
                            // Build block markup
                            $inner_content = !empty($block['example']['innerBlocks']) ? serialize_blocks($block['example']['innerBlocks']) : '';
                            if ($inner_content) {
                                $block_markup = '<!-- wp:' . $block['name'] . ' ' . json_encode($example_attrs) . ' -->' . $inner_content . '<!-- /wp:' . $block['name'] . ' -->';
                            } else {
                                $block_markup = '<!-- wp:' . $block['name'] . ' ' . json_encode($example_attrs) . ' /-->';
                            }
                        }
                        ?>
                        
                        <?php if ($use_real_example): ?>
                            <div class="bl-real-example-badge" style="display: inline-block; background: #00a32a; color: white; padding: 4px 10px; border-radius: 3px; font-size: 11px; font-weight: 600; margin-bottom: 10px;">
                                ✓ <?php echo __('Real Example from Your Site', 'block-listing'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <details class="bl-custom-block-preview" open>
                            <summary><?php echo __('Show Preview', 'block-listing'); ?></summary>
                            <div class="bl-custom-block-preview-content">
                                <?php
                                // Render the block using WordPress's render system
                                echo apply_filters('the_content', $block_markup); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                ?>
                            </div>
                        </details>
                        <div class="bl-pattern-code-section">
                            <button class="bl-copy-code-btn button button-secondary" data-clipboard-target="#custom-block-code-<?php echo esc_attr(sanitize_title($block['name'])); ?>">
                                <span class="dashicons dashicons-clipboard"></span> <?php echo __('Copy Gutenberg Code', 'block-listing'); ?>
                            </button>
                            <details class="bl-code-details">
                                <summary><?php echo __('Show Gutenberg Code', 'block-listing'); ?></summary>
                                <textarea id="custom-block-code-<?php echo esc_attr(sanitize_title($block['name'])); ?>" class="bl-gutenberg-code" readonly><?php echo esc_textarea($block_markup); ?></textarea>
                            </details>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Gutenberg Patterns Section -->
        <div class="bl-section bl-patterns-section bl-section-full-width">
            <h2><span class="dashicons dashicons-screenoptions"></span> <?php echo __('Gutenberg Patterns', 'block-listing'); ?></h2>
            <p class="section-description"><?php echo sprintf(__('Total: %d patterns', 'block-listing'), count($all_patterns)); ?></p>
            <div class="bl-items-container">
                <?php if (!empty($all_patterns)): ?>
                    <?php foreach ($all_patterns as $pattern): ?>
                        <div class="bl-pattern-item-enhanced">
                            <div class="bl-pattern-header">
                                <strong><?php echo esc_html($pattern['title']); ?></strong>
                                <small><code><?php echo esc_html($pattern['name']); ?></code></small>
                            </div>
                            <?php if (!empty($pattern['description'])): ?>
                                <p class="bl-pattern-description"><?php echo esc_html($pattern['description']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($pattern['categories'])): ?>
                                <div class="bl-pattern-categories">
                                    <?php echo __('Categories:', 'block-listing'); ?>
                                    <?php foreach ($pattern['categories'] as $category): ?>
                                        <span class="bl-category-badge"><?php echo esc_html($category); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php if (!empty($pattern['content'])): ?>
                            <div class="bl-pattern-code-section">
                                <button class="bl-copy-code-btn button button-secondary" data-clipboard-target="#pattern-code-<?php echo esc_attr(sanitize_title($pattern['name'])); ?>">
                                    <span class="dashicons dashicons-clipboard"></span> <?php echo __('Copy Gutenberg Code', 'block-listing'); ?>
                                </button>
                                <details class="bl-code-details">
                                    <summary><?php echo __('Show Gutenberg Code', 'block-listing'); ?></summary>
                                    <textarea id="pattern-code-<?php echo esc_attr(sanitize_title($pattern['name'])); ?>" class="bl-gutenberg-code" readonly><?php echo esc_textarea($pattern['content']); ?></textarea>
                                </details>
                            </div>
                            <details class="bl-pattern-preview" open>
                                <summary><?php echo __('Show Preview', 'block-listing'); ?></summary>
                                <div class="bl-pattern-preview-content">
                                    <?php
                                    // Render the pattern content with proper context
                                    echo apply_filters('the_content', $pattern['content']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    ?>
                                </div>
                            </details>
                        <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-items"><?php echo __('No patterns found.', 'block-listing'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Reusable Blocks Section -->
        <div class="bl-section bl-reusable-section bl-section-full-width">
            <h2><span class="dashicons dashicons-admin-page"></span> <?php echo __('Reusable Blocks', 'block-listing'); ?></h2>
            <p class="section-description"><?php echo sprintf(__('Total: %d reusable blocks', 'block-listing'), count($reusable_blocks)); ?></p>
            <div class="bl-items-container">
                <?php if (!empty($reusable_blocks)): ?>
                    <?php foreach ($reusable_blocks as $reusable): ?>
                        <div class="bl-reusable-item-enhanced">
                            <div class="bl-reusable-header">
                                <a href="<?php echo esc_url($reusable['edit_link']); ?>" target="_blank">
                                    <strong><?php echo esc_html($reusable['title']); ?></strong>
                                </a>
                                <small><?php echo sprintf(__('ID: %d', 'block-listing'), $reusable['id']); ?></small>
                            </div>
                            <?php if (!empty($reusable['usage'])): ?>
                                <div class="bl-reusable-usage">
                                    <strong><?php echo sprintf(__('Used on %d page(s):', 'block-listing'), count($reusable['usage'])); ?></strong>
                                    <div class="bl-usage-list">
                                        <?php if (count($reusable['usage']) <= 3): ?>
                                            <?php // Show all pages if 3 or fewer ?>
                                            <?php foreach ($reusable['usage'] as $usage): ?>
                                                <div class="bl-usage-link">
                                                    <a href="<?php echo esc_url($usage['edit_link']); ?>" target="_blank">
                                                        <span class="dashicons dashicons-edit"></span>
                                                    </a>
                                                    <a href="<?php echo esc_url($usage['view_link']); ?>" target="_blank">
                                                        <?php echo esc_html($usage['title']); ?>
                                                    </a>
                                                    <span class="bl-post-type">(<?php echo esc_html($usage['post_type']); ?>)</span>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <?php 
                                            $first_usage = array_slice($reusable['usage'], 0, 3);
                                            $remaining_usage = array_slice($reusable['usage'], 3);
                                            ?>
                                            <?php foreach ($first_usage as $usage): ?>
                                                <div class="bl-usage-link">
                                                    <a href="<?php echo esc_url($usage['edit_link']); ?>" target="_blank">
                                                        <span class="dashicons dashicons-edit"></span>
                                                    </a>
                                                    <a href="<?php echo esc_url($usage['view_link']); ?>" target="_blank">
                                                        <?php echo esc_html($usage['title']); ?>
                                                    </a>
                                                    <span class="bl-post-type">(<?php echo esc_html($usage['post_type']); ?>)</span>
                                                </div>
                                            <?php endforeach; ?>
                                            
                                            <details class="bl-more-pages-details">
                                                <summary class="bl-show-more-pages">
                                                    <?php echo sprintf(__('+ Show %d more page(s)', 'block-listing'), count($remaining_usage)); ?>
                                                </summary>
                                                <div class="bl-remaining-pages">
                                                    <?php foreach ($remaining_usage as $usage): ?>
                                                        <div class="bl-usage-link">
                                                            <a href="<?php echo esc_url($usage['edit_link']); ?>" target="_blank">
                                                                <span class="dashicons dashicons-edit"></span>
                                                            </a>
                                                            <a href="<?php echo esc_url($usage['view_link']); ?>" target="_blank">
                                                                <?php echo esc_html($usage['title']); ?>
                                                            </a>
                                                            <span class="bl-post-type">(<?php echo esc_html($usage['post_type']); ?>)</span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </details>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="bl-no-usage"><em><?php echo __('Not currently used on any page', 'block-listing'); ?></em></p>
                            <?php endif; ?>
                            <?php if (!empty($reusable['content'])): ?>
                                <div class="bl-pattern-code-section">
                                    <button class="bl-copy-code-btn button button-secondary" data-clipboard-target="#reusable-code-<?php echo esc_attr($reusable['id']); ?>">
                                        <span class="dashicons dashicons-clipboard"></span> <?php echo __('Copy Gutenberg Code', 'block-listing'); ?>
                                    </button>
                                    <details class="bl-code-details">
                                        <summary><?php echo __('Show Gutenberg Code', 'block-listing'); ?></summary>
                                        <textarea id="reusable-code-<?php echo esc_attr($reusable['id']); ?>" class="bl-gutenberg-code" readonly><?php echo esc_textarea($reusable['content']); ?></textarea>
                                    </details>
                                </div>
                                <details class="bl-reusable-preview" open>
                                    <summary><?php echo __('Show Preview', 'block-listing'); ?></summary>
                                    <div class="bl-reusable-preview-content">
                                        <?php echo apply_filters('the_content', $reusable['content']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    </div>
                                </details>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-items"><?php echo __('No reusable blocks found.', 'block-listing'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Theme Colors Section -->
        <div class="bl-section bl-colors-section">
            <h2><span class="dashicons dashicons-admin-appearance"></span> <?php echo __('Theme Colors', 'block-listing'); ?></h2>
            <p class="section-description"><?php echo sprintf(__('Total: %d colors', 'block-listing'), count($theme_colors)); ?></p>
            <div class="bl-items-container">
                <?php if (!empty($theme_colors)): ?>
                    <div class="bl-color-grid">
                        <?php foreach ($theme_colors as $color): ?>
                            <div class="bl-color-item">
                                <div class="bl-color-swatch" style="background-color: <?php echo esc_attr($color['color']); ?>;" title="<?php echo esc_attr($color['color']); ?>"></div>
                                <div class="bl-color-info">
                                    <strong><?php echo esc_html($color['name']); ?></strong>
                                    <br>
                                    <code><?php echo esc_html($color['slug']); ?></code>
                                    <br>
                                    <span class="color-value"><?php echo esc_html($color['color']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-items"><?php echo __('No theme colors found.', 'block-listing'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Font Sizes Section -->
        <div class="bl-section bl-font-sizes-section">
            <h2><span class="dashicons dashicons-editor-textcolor"></span> <?php echo __('Theme Font Sizes', 'block-listing'); ?></h2>
            <p class="section-description"><?php echo sprintf(__('Total: %d font sizes', 'block-listing'), count($theme_font_sizes)); ?></p>
            <div class="bl-items-container">
                <?php if (!empty($theme_font_sizes)): ?>
                    <ul class="bl-list bl-font-list">
                        <?php foreach ($theme_font_sizes as $size): ?>
                            <li class="bl-item bl-font-item">
                                <div class="bl-font-preview" style="font-size: <?php echo esc_attr($size['size']); ?>;">
                                    Aa
                                </div>
                                <div class="bl-font-info">
                                    <strong><?php echo esc_html($size['name']); ?></strong>
                                    <br>
                                    <code><?php echo esc_html($size['slug']); ?></code>
                                    <br>
                                    <span class="size-value"><?php echo esc_html($size['size']); ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="no-items"><?php echo __('No theme font sizes found.', 'block-listing'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- CSS Classes Section -->
        <div class="bl-section bl-classes-section">
            <h2><span class="dashicons dashicons-editor-code"></span> <?php echo __('Theme CSS Classes', 'block-listing'); ?></h2>
            <p class="section-description"><?php echo sprintf(__('Total: %d classes (from CSS and SCSS files)', 'block-listing'), count($theme_classes)); ?></p>
            <div class="bl-items-container">
                <?php if (!empty($theme_classes)): ?>
                    <div class="bl-classes-grid">
                        <?php foreach ($theme_classes as $class): ?>
                            <code class="bl-class-item">.<?php echo esc_html($class); ?></code>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-items"><?php echo __('No CSS classes found.', 'block-listing'); ?></p>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    var blData = {
        ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
        exportNonce: '<?php echo wp_create_nonce('bl_export_blocks_nonce'); ?>',
        blocksNonce: '<?php echo wp_create_nonce('bl_blocks_nonce'); ?>',
        totalBlocks: <?php echo count($all_blocks); ?>,
        chunkSize: 10
    };
</script>