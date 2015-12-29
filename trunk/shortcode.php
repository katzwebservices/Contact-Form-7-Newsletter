<?php

class CTCTCF7_Shortcode extends CTCTCF7 {

	function __construct() {

		self::get_includes();
		// Add lists selection dropdown;
		add_action( 'admin_init', array( &$this, 'init_tag_generator' ), 900 );
		add_action( 'admin_head', array( &$this, 'init_tag_script' ) );
		add_action( 'wpcf7_init', array( &$this, 'add_shortcode' ), 6 );
		add_filter( 'ctctcf7_push', array( &$this, 'process_submission_shortcode' ), 10, 2 );

		add_action( 'wp_ajax_ctctcf7_generate_dropdowns', array( &$this, 'ajax_generate_dropdowns_from_code' ) );
	}

	function add_shortcode() {
		if ( function_exists( 'wpcf7_add_shortcode' ) ) {
			wpcf7_add_shortcode( array( 'ctct', 'ctct*' ), array( $this, 'shortcode_handler' ), true );
		}
	}

	/**
	 * Get an array of all shortcodes from text, via ajax
	 *
	 * @return array Array of shortcodes
	 */
	function ajax_generate_dropdowns_from_code() {

		check_ajax_referer( 'ctctcf7_generate_dropdowns', 'ctctcf7_generate_dropdowns' );

		// Fix issue where the WPCF7 plugin isn't around
		if ( ! defined( 'WPCF7_PLUGIN_DIR' ) ) {
			return;
		}

		// Again, attempt to fix
		// https://github.com/katzwebservices/Contact-Form-7-Newsletter/issues/24
		if ( ! class_exists( 'WPCF7_ShortcodeManager' ) ) {
			include_once WPCF7_PLUGIN_DIR . '/includes/shortcodes.php';
		}

		// They must be using version CF7 3.0 or less
		if ( ! class_exists( 'WPCF7_ShortcodeManager' ) ) {
			return;
		}

		$output = array(
				'' => __( 'Select a Field', 'ctctcf7' )
		);

		// Form code to scan
		$code = stripslashes_deep( @$_REQUEST['data'] );

		// Get the tags from the form code
		$scanned_form_tags = WPCF7_ShortcodeManager::get_instance()->scan_shortcode( $code );

		if ( count( $scanned_form_tags ) ) {
			foreach ( $scanned_form_tags as $fe ) {
				if ( empty( $fe['name'] ) ) {
					continue;
				}

				if ( $fe['basetype'] === 'ctct' ) {
					continue;
				}

				$name           = $fe['name'];
				$is_placeholder = ( isset( $fe['options'][0] ) && $fe['options'][0] === 'placeholder' );
				$label          = ( ! empty( $fe['labels'] ) ) ? $fe['labels'][0] : $name;
				$type           = str_replace( '*', '', $fe['type'] );

				$output[ $name ] = esc_attr( "{$label} ($name)" );
			}
		}

		header( 'Cache-Control: no-cache, must-revalidate' );
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
		header( 'Content-Type: application/json' );

		exit( json_encode( $output ) );
	}

	/**
	 * Get the lists from the submitted form $_POST data
	 *
	 * @param  array $cf7_ctct Constant Contact contact array
	 * @param  WPCF7_ContactForm $obj Form object
	 *
	 * @return array           Modified array, if lists existed
	 */
	public function process_submission_shortcode( $cf7_ctct, $obj ) {

		// Now we check whether the optin form has been accepted,
		// if the optin method is the `single` checkbox.
		// If not, then keep going.
		if ( isset( $obj->posted_data['ctctcf7_lists'] ) ) {
			if ( empty( $obj->posted_data['ctctcf7_lists'] ) ) {
				return $cf7_ctct;
			} else {

				// We let CTCT know they opted in.
				$cf7_ctct['accept'] = true;

			}
		}

		// If there are no scanned form tags, return.
		if ( empty( $obj->scanned_form_tags ) || ! is_array( $obj->scanned_form_tags ) ) {
			return $cf7_ctct;
		}

		// Process the submitted lists
		foreach ( $obj->scanned_form_tags as $value ) {
			if ( $value['basetype'] === 'ctct' ) {
				$lists = isset( $obj->posted_data[ $value['name'] ] ) ? (array) $obj->posted_data[ $value['name'] ] : false;

				if ( ! empty( $lists ) ) {

					$username = self::get_username();

					// Lists that are posted are ONLY the IDs, not the path.
					// we convert that to the path now.
					foreach ( $lists as &$list ) {
						$list = sprintf( '/ws/customers/%s/lists/%d', $username, $list );
					}

					$cf7_ctct['lists'] = $lists;

					// We "check the box" in the settings by setting this
					$cf7_ctct['active'] = true;

					// Only process the first ctct tag. Only one allowed.
					break;
				}
			}
		}

		return $cf7_ctct;
	}

	function shortcode_handler( $tag ) {

		$tag = new WPCF7_Shortcode( $tag );

		if ( empty( $tag->name ) ) {
			return '';
		}

		// The lists are in {name of list}::#{id} format.
		// This parses them into an array
		$print_lists = array();
		foreach ( $tag->values as $key => $value ) {
			list( $name, $id ) = explode( '::#', $value );
			$print_lists[ (int) $id ] = trim( rtrim( $name ) );
		}

		/**
		 * Get the list type
		 *
		 * @uses WPCF7_Shortcode::get_option()
		 */
		$options['type']      = $tag->get_option( 'type', 'id', true );
		$options['label']     = urldecode( $tag->get_option( 'label', '', true ) );
		$options['first']     = urldecode( $tag->get_option( 'first', '', true ) );
		$options['lists']     = $print_lists;
		$options['name_attr'] = $tag->name;
		if ( $tag->has_option( 'default:on' ) ) {
			$options['checked'] = 'checked';
		}

		$output = $this->outputHTML( $options );


		$class            = wpcf7_form_controls_class( $tag->type );
		$validation_error = wpcf7_get_validation_error( $tag->name );

		if ( $validation_error ) {
			$class .= ' wpcf7-not-valid';
		}

		$atts          = array();
		$atts['class'] = $tag->get_class_option( $class );
		$atts['id']    = $tag->get_option( 'id', 'id', true );
		$atts          = wpcf7_format_atts( $atts );

		$output = sprintf( '<span class="wpcf7-form-control-wrap %1$s"><span %2$s>%3$s</span>%4$s</span>',
				$tag->name, $atts, $output, $validation_error );

		return $output;
	}

	/**
	 * Convert an array of List objects into HTML output
	 *
	 * @param  string $as Format of HTML; `list`|`select`
	 * @param  array $items List array
	 * @param  array $atts Settings; `fill`, `selected`, `format`; `format` should use replacement tags with the tag being the name of the var of the List object you want to replace. For example, `%%name%% (%%contact_count%% Contacts)` will return an item with the content "List Name (140 Contacts)"
	 *
	 * @return [type]        [description]
	 */
	static function outputHTML( $atts = array() ) {


		$settings = wp_parse_args( $atts, array(
				'type'      => 'checkboxes',
				'format'    => '<span>%%name%%</span>', // Choose HTML format for each item
				'id_attr'   => 'ctctcf7-list-%%id%%', // Pass a widget instance
				'name_attr' => 'ctctcf7_lists',
				'checked'   => false, // If as select, what's active?
				'lists'     => array(),
				'label'     => 'Sign up for our newsletter!',
				'first'     => 'Select a newsletter'
		) );

		extract( $settings );

		switch ( $type ) {
			case 'hidden':
				$before      = '<div>';
				$after       = '</div>';
				$before_item = $after_item = '';
				$format      = '<input type="hidden" value="%%id%%" name="%%name_attr%%[]" />';
				break;
			case 'dropdown':
			case 'select':
			case 'multiselect':
				$multiple = '';

				// Even though the multiselect option is no longer available
				// in the settings, keep this around for backward compatibility.
				// And if crazy people want multi-selects
				if ( $type === 'select' || $type === 'multiselect' ) {
					$multiple = ' multiple="multiple"';
				}
				$before = '<select name="%%name_attr%%"' . $multiple . ' class="select2 ctct-lists">';
				if ( ! empty( $first ) ) {
					$before .= sprintf( '<option value="">%s</option>', esc_html( $first ) );
				}
				$before_item = '<option value="%%id%%">';
				$after_item  = '</option>';
				$after       = '</select>';
				break;
			case 'single':
				$before      = '<input name="ctctcf7_lists" type="hidden" value="" /><label for="ctctcf7_lists"><input type="checkbox" id="ctctcf7_lists" value="signup" name="ctctcf7_lists" %%checked%% /> ';
				$format      = '<input type="hidden" value="%%id%%" name="%%name_attr%%[]" />';
				$before_item = $after_item = '';
				$after       = $label . '</label>';
				break;
			case 'checkboxes':
				$before      = '<ul class="ctct-lists ctct-checkboxes">';
				$before_item = '<li><label for="%%id_attr%%"><input type="checkbox" id="%%id_attr%%" value="%%id%%" name="%%name_attr%%[]" %%checked%% /> ';
				$after_item  = '</label></li>';
				$after       = '</ul>';
				break;
		}

		$output = $before;

		$items_output = '';
		foreach ( $lists as $list_id => $list_name ) {

			$item_content = ( ! is_null( $format ) ) ? $format : $list_name;

			$item_output = $before_item . $item_content . $after_item . "\n";

			// Make sure this is before %%name%%
			$item_output = str_replace( '%%name_attr%%', $name_attr, $item_output );
			$item_output = str_replace( '%%name%%', $list_name, $item_output );

			// Make sure this is before %%id%%
			$item_output = str_replace( '%%id_attr%%', $id_attr, $item_output );
			$item_output = str_replace( '%%id%%', $list_id, $item_output );

			$items_output .= $item_output;
		}

		$output .= $items_output;

		$output .= $after;

		$output = str_replace( '%%name_attr%%', $name_attr, $output );
		$output = str_replace( '%%checked%%', checked( ! empty( $checked ), true, false ), $output );

		return $output;
	}

	/**
	 * Create the Constant Contact Lists tag in the form dropdown menu
	 */
	function init_tag_generator() {

		if ( ! class_exists( 'WPCF7_TagGenerator' ) ) {
			return;
		}

		WPCF7_TagGenerator::get_instance()->add( 'ctct', __( 'Constant Contact Lists', 'ctctcf7' ), array(
				$this,
				'tag_generator'
		), array(
				'id'    => 'wpcf7-tg-pane-ctct',
				'title' => __( 'Constant Contact Lists', 'ctctcf7' ),
		) );
	}

	/**
	 * Handle JS for the Constant Contact tab as well as the Constant Contact Lists button
	 */
	function init_tag_script() {
		/** @define "$dir" "./" */
		$dir = plugin_dir_path( __FILE__ );
		include_once $dir . 'shortcode-js.php';
	}

	/**
	 * @return string HTML link to refresh lists
	 */
	function get_refresh_lists_link() {
		return '<small class="alignright"><a href="' . esc_url( add_query_arg( array( 'cache' => rand( 1, 20000 ) ) ) ) . '">' . esc_html__( 'Refresh Lists', 'ctctcf7' ) . '</a></small>';
	}

	function tag_generator( $args ) {
		/** @define "$dir" "./" */
		$dir = plugin_dir_path( __FILE__ );
		include_once $dir . 'tag-generator.php';
	}

}

new CTCTCF7_Shortcode;
