(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(function() {
		// Kitchen Sink - Copy to Clipboard functionality
		$(document).on('click', '.bl-copy-code-btn', function(e) {
			e.preventDefault();
			
			var $button = $(this);
			var targetId = $button.data('clipboard-target');
			var $textarea = $(targetId);
			
			console.log('Copy button clicked', targetId, $textarea.length); // Debug
			
			if ($textarea.length) {
				var textToCopy = $textarea.val();
				
				console.log('Text to copy length:', textToCopy.length); // Debug
				
				// Try multiple methods to ensure copy works
				var copySuccess = false;
				
				// Method 1: Modern Clipboard API
				if (navigator.clipboard && navigator.clipboard.writeText) {
					navigator.clipboard.writeText(textToCopy).then(function() {
						console.log('Clipboard API success'); // Debug
						showCopySuccess($button);
						copySuccess = true;
					}).catch(function(err) {
						console.log('Clipboard API failed, trying fallback', err); // Debug
						// Try fallback
						copySuccess = fallbackCopy($textarea, $button);
					});
				} else {
					// Method 2: execCommand fallback
					copySuccess = fallbackCopy($textarea, $button);
				}
			} else {
				console.error('Textarea not found:', targetId);
				alert('Copy failed: Target element not found');
			}
		});
		
		// Fallback copy method
		function fallbackCopy($textarea, $button) {
			try {
				// Make sure textarea is visible and selectable
				$textarea.css('display', 'block');
				$textarea.show();
				
				// Focus and select the text
				$textarea[0].focus();
				$textarea[0].select();
				$textarea[0].setSelectionRange(0, $textarea.val().length);
				
				// Execute copy command
				var successful = document.execCommand('copy');
				console.log('execCommand result:', successful); // Debug
				
				if (successful) {
					showCopySuccess($button);
					return true;
				} else {
					throw new Error('execCommand returned false');
				}
			} catch (err) {
				console.error('Fallback copy failed:', err);
				$button.removeClass('copied');
				$button.addClass('copy-failed');
				$button.html('<span class="dashicons dashicons-no"></span> Copy Failed');
				setTimeout(function() {
					$button.removeClass('copy-failed');
					$button.html('<span class="dashicons dashicons-clipboard"></span> Copy Gutenberg Code');
				}, 2000);
				return false;
			}
		}
		
		// Show success state
		function showCopySuccess($button) {
			var originalHTML = $button.html();
			$button.addClass('copied');
			$button.html('<span class="dashicons dashicons-yes"></span> Copied!');
			
			setTimeout(function() {
				$button.removeClass('copied');
				$button.html(originalHTML);
			}, 2000);
		}
		
		// Make textareas auto-select on focus
		$(document).on('focus', '.bl-gutenberg-code', function() {
			$(this).select();
		});
		
		// Debug: Log when script loads
		console.log('Block Listing Kitchen Sink JS loaded');

		// Load blocks in chunks
		var blocksLoaded = 0;
		var isLoading = false;

		function loadBlocksChunk() {
			if (isLoading) return;
			isLoading = true;

			$.ajax({
				url: blData.ajaxUrl,
				type: 'POST',
				data: {
					action: 'bl_load_blocks_chunk',
					nonce: blData.blocksNonce,
					offset: blocksLoaded,
					limit: blData.chunkSize
				},
				success: function(response) {
					isLoading = false;

					if (response.success) {
						// Hide initial loading message
						$('.bl-loading-initial').hide();

						// Show blocks list
						$('#bl-blocks-list').show();

						// Append new blocks
						$('#bl-blocks-list').append(response.data.html);

						// Update loaded count
						blocksLoaded = response.data.loaded;
						$('#bl-blocks-container').attr('data-loaded', blocksLoaded);

						// Update loaded text
						$('#bl-blocks-loaded-text').text('(Loaded ' + blocksLoaded + ' of ' + response.data.total + ')').show();

						// Show/hide load more button
						if (response.data.has_more) {
							$('#bl-load-more-container').show();
						} else {
							$('#bl-load-more-container').hide();
							$('#bl-blocks-loaded-text').text('(All blocks loaded)');
						}
					} else {
						alert('Failed to load blocks: ' + (response.data.message || 'Unknown error'));
					}
				},
				error: function(xhr, status, error) {
					isLoading = false;
					console.error('Load blocks error:', error);
					$('.bl-loading-initial').html('<p style="color: #d63638;">Failed to load blocks. Please refresh the page.</p>');
				}
			});
		}

		// Load initial chunk on page load
		if ($('#bl-blocks-container').length && blData.totalBlocks > 0) {
			loadBlocksChunk();
		}

		// Load more button click handler
		$(document).on('click', '#bl-load-more-blocks', function(e) {
			e.preventDefault();
			var $button = $(this);
			var originalHTML = $button.html();

			$button.prop('disabled', true);
			$button.html('<span class="dashicons dashicons-update-alt bl-spin"></span> Loading...');

			loadBlocksChunk();

			setTimeout(function() {
				$button.prop('disabled', false);
				$button.html(originalHTML);
			}, 500);
		});

		// Export to CSV functionality
		$(document).on('click', '#bl-export-blocks-csv', function(e) {
			e.preventDefault();

			var $button = $(this);
			var originalHTML = $button.html();

			// Disable button and show loading state
			$button.prop('disabled', true);
			$button.html('<span class="dashicons dashicons-update-alt bl-spin"></span> Exporting...');

			// Make AJAX request
			$.ajax({
				url: blData.ajaxUrl,
				type: 'POST',
				data: {
					action: 'bl_export_blocks_csv',
					nonce: blData.exportNonce
				},
				success: function(response) {
					if (response.success) {
						// Create a blob from the CSV content
						var blob = new Blob([response.data.csv], { type: 'text/csv;charset=utf-8;' });

						// Create a download link and trigger it
						var link = document.createElement('a');
						if (link.download !== undefined) {
							var url = URL.createObjectURL(blob);
							link.setAttribute('href', url);
							link.setAttribute('download', response.data.filename);
							link.style.visibility = 'hidden';
							document.body.appendChild(link);
							link.click();
							document.body.removeChild(link);
							URL.revokeObjectURL(url);
						}

						// Show success state
						$button.html('<span class="dashicons dashicons-yes"></span> Exported!');
						setTimeout(function() {
							$button.prop('disabled', false);
							$button.html(originalHTML);
						}, 2000);
					} else {
						// Show error
						alert('Export failed: ' + (response.data.message || 'Unknown error'));
						$button.prop('disabled', false);
						$button.html(originalHTML);
					}
				},
				error: function(xhr, status, error) {
					console.error('Export error:', error);
					alert('Export failed: ' + error);
					$button.prop('disabled', false);
					$button.html(originalHTML);
				}
			});
		});
	});

})( jQuery );
