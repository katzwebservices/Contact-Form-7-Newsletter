<script>
	jQuery(document).ready(function($) {

		/**
		 * When the form code changes, generate a drop-down list of possible merge tags.
		 */
		$( '#wpcf7-form' ).on( 'change', function ( event ) {
			var data = {
				'action': 'ctctcf7_generate_dropdowns',
				'dataType': 'json',
				'ctctcf7_generate_dropdowns': "<?php echo wp_create_nonce("ctctcf7_generate_dropdowns") ?>",
				'data': $( this ).val()
			};

			var jqxhr = $.post( "<?php echo admin_url("admin-ajax.php")?>", data, function ( response ) {

						// Build the dropdown
						var dropdown = CTCTCF7_Build_Dropdown( response );

						// Replace text with dropdown
						$( '.ctct-fields *[name*="wpcf7-ctct"]' ).each( function () {
							var $that = $( this );
							var value = $that.val();

							// Clone and modify the dropdown for each merge field
							var theclone = dropdown
									.clone()
									.attr( 'id', $that.attr( 'id' ) )
									.attr( 'class', $that.attr( 'class' ) )
									.attr( 'name', $that.attr( 'name' ) )

									// Set the value to the value of the input.
									// We remove [] so that it's always the same, regardless
									// of how it was saved.
									.val( value.replace( '[', '' ).replace( ']', '' ) );

							// Replace the existing input with the new one
							$that.replaceWith( theclone );
						} );

					} )
					.fail( function ( response ) {
						if ( typeof console == "object" ) {
							console.log( "Error while generating shortcode dropdowns.", response );
						}
					} );

		} ).trigger( 'change' );

		/**
		 * Build a <select> dropdown based on the AJAX response
		 * @param {jQuery} response Select dropdown
		 */
		function CTCTCF7_Build_Dropdown( response ) {
			$select = $( '<select />' );
			for ( var key in response ) {
				$( '<option value="' + key + '">' + response[ key ] + '</option>' ).appendTo( $select );
			}
			return $select;
		}

		/**
		 * Clear the error on changing a <select> element with an error
		 */
		$( document ).on( 'change', '.half-left select, .half-right select', function () {
			if ( $( this ).val() !== '' ) {
				$( this ).parents( '.half-right,.half-left' ).removeClass( 'error' );
			}
		} );

		$( document ).on( 'submit', '#wpcf7-admin-form-element', function () {
			// If the integration is enabled
			if ( $( '#wpcf7-ctct-active' ).is( ':checked' ) ) {
				if ( $( '#wpcf7-ctct-email_address' ).val() === '' ) {
					alert( 'You have not provided a field to use as the email address. This is required for the Constant Contact integration.' );
					$( '#wpcf7-ctct-email_address' ).focus().parents( '.half-left,.half-right' ).addClass( 'error' );
					return false;
				}
			}
		} );

		/**
		 * When the subscribe type is changed
		 */
		$( document ).on( 'change', '.subscribe_options input.option', function ( event ) {

			var checked = $( this ).prop( 'checked' ) ? false : true;

			$( '#wpcf7-tg-pane-ctct' ).find( '.subscribe_options input.option' ).prop( 'checked', false );

			$( this ).prop( 'checked', !checked );

			ctct_cf7_change_subscribe_type();
		} );

		function ctct_cf7_get_subscription_type() {

			var value = $( '#wpcf7-tg-pane-ctct' ).find( '.subscribe_options input.option' ).filter( ':checked' ).val();

			return value;
		}

		/**
		 * When the subscribe type is changed
		 */
		function ctct_cf7_change_subscribe_type( event ) {

			// When hiding the values not in use,
			// set default values for all inputs with the current value
			// and then reset the value (to clear the shortcode generation)
			$( '*[class*=subscribe_type]' ).hide( 0, function () {
				$( 'input', $( this ) ).each( function () {
					value = $( this ).val();
					if ( value ) {
						$( this ).attr( 'data-default', value );
					}
					$( this ).val( '' ).trigger( 'change' );
				} );
			} );

			var type = ctct_cf7_get_subscription_type();

			if ( type ) {

				$( '.ctctcf7_subscribe_list, #ctctcf7-tg-tags' ).show();

				// When showing the values in use,
				// Get and set default values for all inputs
				$( '.subscribe_type_' + type ).show( 0, function () {
					// Get and set default values for all inputs
					$( 'input,select', $( this ) ).each( function () {
						if ( $( this ).val() === '' ) {
							var dataDefault = $( this ).attr( 'data-default' );
							if ( dataDefault ) {
								$( this ).val( dataDefault ).trigger( 'change' );
							}
						}
					} );
				} );
			} else {
				// Only show the lists and tag code if there's a type selected.
				$( '.ctctcf7_subscribe_list, #ctctcf7-tg-tags' ).hide();
			}
		}

		// End change subscribe type

		// Spaces = different values in CF7, so we URL encode the labels
		//  to allow for passing more data than the poor labels are designed for.
		$( document ).on( 'ctct_change_type change keyup', '.urlencode:visible', function () {
			var data_target = $( this ).attr( 'data-target' );
			var encoded_value = ctctcf7_url_encode( $( this ).val() );
			$( data_target ).val( encoded_value ).trigger( 'change' );
		} );

		// When lists are changed, show/hide the tag field
		$( document ).on( 'change', '.ctctcf7_subscribe_list', function () {

			// If the lists haven't been chosen yet, hide the tag code
			// and show the instructions message
			var checked_lists = $( '.ctctcf7_subscribe_list input' ).serialize();

			if ( checked_lists === '' ) {
				$( '.control-box:visible' ).css( 'height', '100%' );
				$( '#ctctcf7-tg-tags div, #wpcf7-tg-pane-ctct .insert-box' ).hide();
				$( '#ctctcf7-tg-tags h4' ).show();
			} else {
				$( '.control-box:visible' ).css( 'height', '343px' );
				$( '#ctctcf7-tg-tags div, #wpcf7-tg-pane-ctct .insert-box' ).show();
				$( '#ctctcf7-tg-tags h4' ).hide();
			}

		} );


		/**
		 * Url Encoding of a string
		 *
		 * Remove whitespace from tag string to prevent errors with CF7.
		 *
		 * @param  {string} str Text to URL encode
		 * @return {string}     URL encoded text (in PHP format, with mods)
		 */
		function ctctcf7_url_encode( str ) {
			return encodeURIComponent( str ).replace( /'/g, '%27' ).replace( /\(/g, '%28' ).
			replace( /\)/g, '%29' ).replace( /%20/g, '+' );
		}
	});
</script>