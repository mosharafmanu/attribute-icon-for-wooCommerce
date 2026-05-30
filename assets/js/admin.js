/**
 * WooCommerce Attribute Thumbnail — admin scripts.
 *
 * Wires up the WordPress media uploader for the attribute image field.
 * Handles upload, selection, and removal with preview.
 */
( function ( $ ) {
	'use strict';

	var config = window.wcAttributeThumbnail || {};

	var emptyStateHtml =
		'<span class="wc-attribute-image-empty">' +
			'<span class="dashicons dashicons-format-image"></span>' +
		'</span>';

	var removeOverlayHtml =
		'<span class="wc-attribute-image-remove-overlay">' +
			'<span class="dashicons dashicons-trash"></span>' +
		'</span>';

	function initField( container ) {
		var preview   = container.find( '.wc-attribute-image-preview' );
		var input     = container.find( 'input[type="hidden"]' );
		var uploadBtn = container.find( '.wc-attribute-image-upload' );
		var removeBtn = container.find( '.wc-attribute-image-remove' );
		var frame;

		// Seed empty state if no image on load
		if ( ! input.val() ) {
			preview.html( emptyStateHtml );
		} else {
			preview.addClass( 'has-image' );
			if ( ! preview.find( '.wc-attribute-image-remove-overlay' ).length ) {
				preview.append( removeOverlayHtml );
			}
		}

		function openUploader() {
			if ( frame ) {
				frame.open();
				return;
			}

			frame = wp.media( {
				title: config.uploadTitle || 'Choose Attribute Image',
				button: { text: config.uploadButton || 'Use this image' },
				multiple: false,
				library: { type: 'image' }
			} );

			frame.on( 'select', function () {
				var attachment = frame.state().get( 'selection' ).first().toJSON();
				var url = attachment.sizes && attachment.sizes.thumbnail
					? attachment.sizes.thumbnail.url
					: attachment.url;

				input.val( attachment.id );

				preview
					.addClass( 'has-image' )
					.html(
						'<img src="' + url + '" alt="">' +
						removeOverlayHtml
					);

				uploadBtn.text( config.changeLabel || 'Change Image' );

				if ( removeBtn.length ) {
					removeBtn.show();
				} else {
					removeBtn = $( '<button type="button" class="button wc-attribute-image-remove">' +
						( config.removeLabel || 'Remove' ) +
						'</button>'
					);
					removeBtn.on( 'click', removeImage );
					uploadBtn.after( removeBtn );
				}
			} );

			frame.open();
		}

		function removeImage( e ) {
			if ( e ) {
				e.preventDefault();
				e.stopPropagation();
			}
			input.val( '' );
			preview
				.removeClass( 'has-image' )
				.html( emptyStateHtml );
			uploadBtn.text( config.uploadLabel || 'Upload Image' );
			if ( removeBtn.length ) {
				removeBtn.hide();
			}
		}

		// Clicking the preview opens the uploader (or removes if overlay clicked)
		preview.on( 'click', function ( e ) {
			if ( preview.hasClass( 'has-image' ) ) {
				removeImage( e );
			} else {
				openUploader();
			}
		} );

		uploadBtn.on( 'click', function ( e ) {
			e.preventDefault();
			openUploader();
		} );

		if ( removeBtn.length ) {
			removeBtn.on( 'click', removeImage );
		}
	}

	$( function () {
		$( '.wc-attribute-image-field' ).each( function () {
			initField( $( this ) );
		} );
	} );

} )( jQuery );
