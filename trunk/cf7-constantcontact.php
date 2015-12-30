<?php
/*
Plugin Name: Contact Form 7 Newsletter
Plugin URI: https://katz.co/plugins/contact-form-7-newsletter/
Description: Add the power of Constant Contact to Contact Form 7
Author: Katz Web Services, Inc.
Author URI: http://www.katzwebservices.com
Version: 2.2
*/

/*  Copyright 2015 Katz Web Services, Inc. (email: info@katzwebservices.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * Update the version number when the plugin is activated.
 */
register_activation_hook(__FILE__, array('CTCTCF7', 'was_updated'));

class CTCTCF7 {

	/**
	 * The current version of the plugin.
	 *
	 * @var string
	 */
	private static $version = '2.2';

	/**
	 * The version of CF7 that is required
	 *
	 * @since 2.2
	 * @type string
	 */
	private static $required_cf7_version = '4.3';

	function __construct() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
	}

	function plugins_loaded() {

		// Require Version 4.2 of CF7
		if ( ! $this->is_cf7_valid() ) {
			add_action( 'admin_notices', array( $this, 'cf7_version_required_notice' ) );

			return;
		}

		// Upgrade messages
		add_action( 'admin_notices', array( $this, 'updated_message' ) );
		add_action( 'admin_init', array( $this, 'hide_updated_message' ) );

		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );

		add_filter( 'plugin_action_links', array( $this, 'plugins_action_links' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'wpcf7_after_save', array( $this, 'save_form_settings' ) );

		/** @since 2.1 * */
		add_filter( 'wpcf7_editor_panels', array( $this, 'wpcf7_editor_panels' ) );

		// Add icon to activated form list
		add_action( 'admin_footer', array( $this, 'add_enabled_icon' ) );

		// CF7 Processing
		add_action( 'wpcf7_mail_sent', array( $this, 'process_submission' ) );

		/** @define "$path" "./" */
		$path = plugin_dir_path( __FILE__ );
		include_once( $path . "shortcode.php" );
	}

	/**
	 * Show a notice when CF7 4.2 isn't active
	 *
	 * @since 2.1
	 * @return void
	 */
	public function cf7_version_required_notice() {

		if ( NULL === $this->is_cf7_valid() ) {
			$button = function_exists( 'is_network_admin' ) && is_network_admin() ? '<a href="#contact-form-7">' : '<a href="' . wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=contact-form-7/wp-contact-form-7.php' ), 'activate-plugin_contact-form-7/wp-contact-form-7.php' ) . '" class="button button-primary">';
			$button .= esc_html__( 'Activate Contact Form 7 now', 'ctctcf7' );
			$button .= '</a>';
			$message = esc_html__( 'Contact Form 7 Newsletter requires Contact Form 7 to be active. %s', 'ctctcf7' );
			$message = sprintf( $message, $button );
		} else {
			$message = sprintf( esc_html__( 'Contact Form 7 Newsletter requires Contact Form 7 version %s or greater. Please %supdate the Contact Form 7 plugin%s.', 'ctctcf7' ), self::$required_cf7_version, '<a href="' . admin_url( 'update-core.php' ) . '">', '</a>' );
		}

		echo '<div class="error notice is-dismissible">' . wpautop( $message ) . '</div>';
	}

	/**
	 * @since 2.1
	 * @return bool True: CF7 4.2 or greater is active. False: lesser version. NULL: inactive.
	 */
	public function is_cf7_valid() {

		if ( ! defined( 'WPCF7_VERSION' ) ) {
			return NULL;
		}

		return version_compare( WPCF7_VERSION, self::$required_cf7_version, '>=' );
	}

	/**
	 * Add panels in Contact Form 7 4.2+
	 *
	 * @since 2.1
	 *
	 * @param array $panels registered tabs in Form Editor
	 *
	 * @return array tabs with CTCTCF7 tab added
	 */
	function wpcf7_editor_panels( $panels = array() ) {

		if ( wpcf7_admin_has_edit_cap() ) {
			$panels['ctctcf7'] = array(
					'title'    => __( 'Constant Contact', 'ctctcf7' ),
					'callback' => array( $this, 'metabox' )
			);
		}

		return $panels;
	}

	/**
	 * Get the plugin version string
	 *
	 * @return string Version string
	 */
	static function get_version() {
		return self::$version;
	}

	static public function was_updated() {

		$previous_version = get_option( 'ctct_cf7_version' );
		$version          = self::get_version();
		$updated          = false;

		// Pre-Version 2.0
		if ( ! $previous_version ) {
			$previous         = true;
			$previous_version = '1.1';
		}

		// DB doesn't match
		if ( $previous_version !== $version ) {
			$updated = true;
		}

		if ( $updated ) { // @todo
			add_option( 'ctct_cf7_updated', $previous_version );
			update_option( 'ctct_cf7_version', $version );
		} else {
			delete_option( 'ctct_cf7_updated' );
		}
	}

	/**
	 * Hide updated messages for users when the click the hide button.
	 *
	 * Fake AJAX. No security really needed...
	 */
	public function hide_updated_message() {

		if ( isset( $_REQUEST['hide-cf7-update'] ) && current_user_can( 'manage_options' ) ) {
			$deleted = delete_option( 'ctct_cf7_updated' );

			// If processing using ajax, we can exit immediately.
			if ( isset( $_REQUEST['ajax'] ) ) {
				die( floatval( $deleted ) );
			}

			// Otherwise, it's a plain link, and keep loading.
			return;
		}
	}

	/**
	 * If the settings show that the plugin has been updated,
	 * then show an updated message.
	 *
	 */
	public function updated_message() {
		if ( $previous_version = get_option( 'ctct_cf7_updated' ) ) {

			// If this upgrade is from V1 to V2
			$version1update = version_compare( $previous_version, '2.0', '<' );
			if ( $version1update ) {
				echo '<style>.wrap .updated #message { display: none; }</style>';
				echo '<div class="wrap" style="max-width:800px;"><div class="updated inline">';
				echo wpautop( sprintf( __( '<h2>You need to update your existing Contact Form 7 Newsletter form configurations.</h2>

					<h3><a href="%s" target="_blank">Read detailed form integration instructions</a>.</h3>

					The Contact Form 7 Newsletter plugin has made significant changes. While your existing configurations should still work, <strong>you should make sure that the integrations are still configured properly</strong>.

					The forms with the <img src="%s" width="16" height="16" alt="" /> icon next to their name are the forms with Constant Contact integration enabled.

					<a class="button-primary button" href="%s">Go to Contact Form 7</a><a class="button-secondary button alignright" id="hide-cf7-update" href="%s">Got it, hide this 	message.</a>
					' ),
								plugins_url( 'help/howto.html#step_10', __FILE__ ),
								plugins_url( 'favicon.png', __FILE__ ),
								admin_url( 'admin.php?page=wpcf7' ),
								esc_url( add_query_arg( array( 'hide-cf7-update' => true ) ) ) )
				);

				echo '</div></div>';
			}

			// Placeholder for future messages.
			switch ( $previous_version ) {
				case '2.0.3':
				case '2.0.2':
					break;
			}

			?>
			<script>
				jQuery( document ).ready( function ( $ ) {
					$( '.cf7com-links' ).hide();
					/**
					 * When the hide button is clicked, process it with AJAX
					 * @see CTCTCF7::hide_updated_message()
					 */
					$( '#hide-cf7-update' ).click( function ( e ) {
						e.preventDefault();
						var $that = $( this );
						$that.parents( '.wrap' ).css( { 'cursor': 'wait' } );
						$.ajax( $( this ).attr( 'href' ) + '&ajax=true' )
								// If it works, hide the DIV
								.done( function ( data ) {
									$that.parents( '.wrap' ).slideUp();
								} )
								// If it fails to load using AJAX, redirect to the page
								// to hide it manually
								.fail( function () {
									window.location = $that.attr( 'href' );
								} );
					} );
				} );
			</script>
			<?php
		}
	}

	/**
	 * Add an icon to forms with Constant Contact integration enabled on the Contact Form 7 Edit page.
	 *
	 */
	public function add_enabled_icon() {
		global $pagenow, $plugin_page;

		if ( empty( $plugin_page ) || empty( $pagenow ) ) {
			return;
		}

		if ( $pagenow === 'admin.php' && $plugin_page === 'wpcf7' && ! isset( $_GET['action'] ) && class_exists( 'WPCF7_ContactForm' ) ) {

			// Get the forms
			$forms = WPCF7_ContactForm::find();

			// If there are no forms, return
			if ( empty( $forms ) ) {
				return;
			}

			// Otherwise, loop through and see which ones have settings
			// for Constant Contact integration.
			$activeforms = array();

			foreach ( $forms as &$form ) {
				$cf_id = method_exists( $form, 'id' ) ? $form->id() : $form->id;

				$is_active = get_option( 'cf7_ctct_' . $cf_id );

				if ( ! empty( $is_active ) && ! empty( $is_active['active'] ) ) {
					$activeforms[] = $cf_id;
				}
			}

			// Reset the post data, possibly modified by `WPCF7_ContactForm::find()`.
			wp_reset_postdata();

			// If there are no forms with CTCT integration, get outta here
			if ( empty( $activeforms ) ) {
				return;
			}

			// Otherwise, add the icon to each row with integration.
			?>
			<style>
				.ctct_enabled {
					position: absolute;
					background: url('<?php echo plugins_url('favicon.png',__FILE__); ?>') right top no-repeat;
					height: 16px;
					width: 16px;
					margin-left: 10px;
				}
			</style>
			<script>
				jQuery( document ).ready( function ( $ ) {
					// Convert forms array into JSON array
					$activeforms = $.parseJSON( '<?php echo json_encode($activeforms); ?>' );

					// For each visible forms row
					$( 'table.posts tr' ).each( function () {
						// Get the ID of the row
						id = parseInt( $( '.check-column input', $( this ) ).val() );

						// If the row is in the $activeforms array, add the icon span
						if ( $activeforms.indexOf( id ) >= 0 ) {
							$( 'td a.row-title', $( this ) ).append( '<span class="ctct_enabled" title="Constant Contact integration is enabled for this form."></span>' );
						}
					} );
				} );
			</script>
			<?php
		}
	}

	public function admin_head() {
		global $plugin_page;

		if ( $plugin_page === 'ctct_cf7' ) {
			wp_enqueue_script( 'thickbox' );
		}

		if ( $plugin_page !== 'wpcf7' ) {
			return;
		}

		wp_enqueue_script( 'jquery-ui-tooltip' );
		?>
		<script type="text/javascript">

			jQuery( document ).ready( function ( $ ) {


				$( '#wpcf7-ctct-active' ).change( function () {
					if ( $( this ).is( ':checked' ) ) {
						$( '#wpcf7-ctct-all-fields' ).show();
					} else {
						$( '#wpcf7-ctct-all-fields' ).hide();
					}
				} ).trigger( 'change' );
			} );
		</script>

		<style>
			.half-right,
			.half-left,
			.half-left.error {
				float: left;
				width: 47%;
				margin: 0 1% .5em 0;
			}

			.half-right input,
			.half-left input {

			}

			.half-right,
			.half-right.error {
				float: right;
				width: 47%;
				margin-right: 0!important;
			}

			.ui-tooltip {
				padding: 18px;
				position: absolute;
				z-index: 9999;
				max-width: 300px;
				color: #000;
				text-shadow: 1px 1px 1px #fff;
				font-size: 1.0em;
				-webkit-border-radius: 6px;
				-moz-border-radius: 6px;
				border-radius: 6px;
				-webkit-box-shadow: 0 8px 6px -6px rgba(0, 0, 0, 0.3);
				-moz-box-shadow: 0 8px 6px -6px rgba(0, 0, 0, 0.3);
				box-shadow: 0 8px 6px -6px rgba(0, 0, 0, 0.3);
			}

			body .ui-tooltip {
				border: 4px solid #999;
				background-color: #ededed;
			}

			body .ui-tooltip h6 {
				color: #0e6085;
				font-size: 1.1em;
				font-weight: bold;
				margin: 0 0 3px 0 !important;
				padding: 0 !important;
			}

			.ctctcf7-tooltip {
				display: block;
				background: #eee;
				border-radius: 50px;
				color: #999;
				padding: 5px 10px;
				cursor: help;
				float: right;
				border: 1px solid #ddd;
			}
		</style>
		<?php
	}

	public function plugins_action_links( $links, $file ) {
		if ( $file != plugin_basename( __FILE__ ) ) {
			return $links;
		}

		$settings_link = '<a href="' . admin_url( 'admin.php?page=ctct_cf7' ) . '">' . esc_html( __( 'Settings', 'ctctcf7' ) ) . '</a>';

		array_unshift( $links, $settings_link );

		return $links;

	}

	static function get_includes() {
		$dir = plugin_dir_path( __FILE__ );
		if ( ! class_exists( "CTCTUtility" ) ) {
			include( "{$dir}api/ctctWrapper.php" );
		}
		if ( ! class_exists( "CTCT_SuperClass" ) ) {
			include( "{$dir}api/ctct_cf7_superclass.php" );
		}
	}


	protected static function validateApi() {
		$utility = new CTCTUtility();
		$return  = $utility->httpGet( $utility->getApiPath() . '/ws/customers/' . $utility->getLogin() . '/contacts?email=' . urlencode( 'asdasdasdasdsadsadasdas@asdmgmsdfdaf.com' ) );

		return $return['info']['http_code'] === 200 && empty( $return['error'] );
	}

	public static function get_password() {
		if ( ! empty( $_POST['ctct_cf7'] ) ) {
			return $_POST['ctct_cf7']['password'];
		}
		$settings = get_option( 'ctct_cf7', NULL );
		$value    = ! empty( $settings['password'] ) ? $settings['password'] : NULL;

		return $value;
	}

	public static function get_username() {
		if ( ! empty( $_POST['ctct_cf7'] ) ) {
			return $_POST['ctct_cf7']['username'];
		}
		$settings = get_option( 'ctct_cf7' );
		$value    = ! empty( $settings['username'] ) ? $settings['username'] : NULL;

		return $value;
	}

	public function admin_menu() {
		add_submenu_page( 'wpcf7', __( 'Constant Contact Contact Form 7 Settings' ), __( 'Constant Contact' ), 'manage_options', 'ctct_cf7', array(
				'CTCTCF7',
				'settings_page'
		) );
	}

	public function settings_init() {

		self::get_includes();

		register_setting( 'ctct_cf7', 'ctct_cf7' );

		add_settings_section(
				'ctct_api',
				__( 'Configure your Constant Contact account settings.' ),
				array( $this, 'setting_description' ),
				'ctct_cf7'
		);
		add_settings_field(
				'ctct_cf7_username',
				__( 'Constant Contact Username' ),
				array( $this, 'setting_input_username' ),
				'ctct_cf7',
				'ctct_api'
		);
		add_settings_field(
				'ctct_cf7_password',
				__( 'Constant Contact Password' ),
				array( $this, 'setting_input_password' ),
				'ctct_cf7',
				'ctct_api'
		);
	}

	public function setting_description() {
		echo __( 'Enter the username and password you use to log in to Constant Contact.', 'ctctcf7' );
	}

	public function setting_input_username() {
		echo '<input autocomplete="off" name="ctct_cf7[username]" id="ctct_cf7_username" type="text" value="' . self::get_username() . '" class="text" />';
	}

	public function setting_input_password() {
		echo '<input autocomplete="off" name="ctct_cf7[password]" id="ctct_cf7_password" type="password" value="' . self::get_password() . '" class="password" />';
	}

	/**
	 * Check the status of a plugin.
	 *
	 * @param string $plugin Base plugin path from plugins directory.
	 *
	 * @return int 1 if active; 2 if inactive; 0 if not installed
	 */
	static function get_plugin_status( $location = '' ) {

		$errors = validate_plugin( $location );

		// Plugin is found
		if ( ! is_wp_error( $errors ) ) {
			if ( is_plugin_inactive( $location ) ) {
				return 2;
			}

			return 1;
		} else {
			return false;
		}
	}

	static function settings_page() {
		wp_enqueue_style( 'thickbox' );

		if( file_exists( plugin_dir_path( __FILE__ ) . 'kwsratingbox.php' ) ) {
			include_once( plugin_dir_path( __FILE__ ) . 'kwsratingbox.php' );
		}
		?>
		<div class="wrap">
			<?php kws_show_rating_box( 'Contact Form 7 - Constant Contact Module', 'contact-form-7-newsletter', self::get_version() ); ?>

			<a href="http://katz.si/4w"><img src="<?php echo plugins_url( 'CTCT_horizontal_logo.png', __FILE__ ); ?>"
			                                 width="281" height="47" alt="Constant Contact"
			                                 style="margin-top:1em;"/></a>

			<h2 style="padding-top:0;margin-bottom:.5em;"><?php _e( 'Contact Form 7 Constant Contact', 'ctctcf7' ); ?></h2>

			<form action="options.php" method="post">
				<?php
				$message = '';
				$status  = self::get_plugin_status( 'contact-form-7/wp-contact-form-7.php' );
				switch ( $status ) {
					case 1:
						$message = false;
						break;
					case 2:
						$message = __( 'Contact Form 7 is installed but inactive. Activate Contact Form 7 to use this plugin.', 'ctctcf7' );
						break;
					case 0:
						$message = __( sprintf( 'Contact Form 7 is not installed. <a href="%s" class="thickbox" title="Install Contact Form 7">Install the Contact Form 7 plugin</a> to use this plugin.', admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=contact-form-7&amp;TB_iframe=true&amp;width=600&amp;height=650' ) ), 'ctctcf7' );
						break;
				}

				if ( ! empty( $message ) ) {
					echo sprintf( "<div id='message' class='error'><p>%s</p></div>", $message );
				}

				if ( is_null( self::get_password() ) && is_null( self::get_username() ) ) {
					self::show_signup_message();
				} else if ( is_null( self::get_password() ) ) {
					echo "<div id='message' class='error alignleft'><p>" . __( 'Your password is empty.', 'ctctcf7' ) . "</p></div>";
				} else if ( is_null( self::get_username() ) ) {
					echo "<div id='message' class='error alignleft'><p>" . __( 'Your username is empty.', 'ctctcf7' ) . "</p></div>";
				} else {
					$valid = self::validateApi();
					if ( $valid ) {
						echo wpautop( sprintf( __( '<h3>Now you can integrate with a Contact Form 7 form. %sView integration instructions%s.</h3>', 'ctctcf7' ), '<a href="' . plugins_url( 'help/howto.html', __FILE__ ) . '" target="_blank">', '</a>' ) );

						echo "<div id='message' class='updated inline alignleft'><p>" . __( 'Your username and password seem to be working.', 'ctctcf7' ) . "</p></div>";
					} else {
						echo "<div id='message' class='error alignleft'><p>" . __( 'Your username and password are not configured properly.', 'ctctcf7' ) . "</p></div>";
					}
				}

				echo '<div class="clear"></div>';
				settings_fields( 'ctct_cf7' );
				do_settings_sections( 'ctct_cf7' );
				?>
				<p class="submit"><input class="button-primary" type="submit" name="Submit"
				                         value="<?php _e( 'Submit', 'ctctcf7' ); ?>"/>
			</form>
		</div><!-- .wrap -->
		<?php
	}

	static function show_signup_message() {
		?>
		<style type="text/css">
			.get-em,
			#grow-with-email,
			a#free_trial {
				display:block;
				text-indent:-9999px;
				overflow:hidden;
				float:left;
			}

			a#free_trial:hover {
				background-position: 0px -102px;
			}

			#grow-with-email {
				background-image: url('http://img.constantcontact.com/lp/images/standard/bv2/product_pages/test/grow_with_email_text.png');
			}

			#grow-with-email,
			#grow-with-email a {
				display: block;
				border: none;
				outline: none;
				height: 91px;
				width: 720px;
			}

			.get-em {
				float: left;
				clear: left;
				width: 201px;
				height: 81px;
				background: url('http://img.constantcontact.com/lp/images/standard/bv2/product_pages/test/btn_get_email_white.png') left top no-repeat;
			}

			.get-em:hover {
				background-position: left bottom;
			}

			.learn-more {
				margin-left: 15px !important;
			}

			#enter-account-details {
				width: 100%;
				border-top: 1px solid #ccc;
				margin-top: 1em;
				padding-top: .5em;
			}
		</style>
		<div style="clear:left; float:left;">
			<h2 class="clear" style="font-size:23px; color: #555;"><strong>Hello!</strong> This plugin requires <a
						href="http://katz.si/4i" title="Learn more about Constant Contact">a Constant Contact
					account</a>.</h2>

			<p id="grow-with-email"><a href="http://katz.si/4i"><strong>Grow with Email Marketing. Guaranteed.</strong>
					With Email Marketing, it's easy for you to connect with your customers, and for customers to share
					your message with their networks. And the more customers spread the word about your business, the
					more you grow</a></p>

			<p><a class="get-em" href="http://katz.si/4w">Start Your Free Trial</a></p>

			<h2 class="learn-more alignleft"> or <a href="http://katz.si/4i">Learn More</a></h2>

			<div class="clear"></div>
		</div>

		<h2 class="clear" id="enter-account-details"><?php _e( 'Enter your account details below:', 'ctctcf7' ); ?></h2>
		<?php
		echo wpautop( sprintf( __( '<h3>%sView integration instructions%s.</h3>', 'ctctcf7' ), '<a href="' . plugins_url( 'help/howto.html', __FILE__ ) . '" target="_blank">', '</a>' ) );
	}

	/**
	 * When saving the CF7 form settings, save the CTCT settings, too
	 *
	 * @param $args
	 */
	public function save_form_settings( $args ) {
		$cf_id = method_exists( $args, 'id' ) ? $args->id() : $args->id;
		update_option( 'cf7_ctct_' . $cf_id, $_POST['wpcf7-ctct'] );
	}

	/**
	 * @param $args
	 */
	public function metabox( $args ) {
		include plugin_dir_path( __FILE__ ) . '/metabox.php';
	}


	/**
	 * Handle the form submission
	 *
	 * If no CTCT configurations exist or the `CTCT_SuperClass` class doesn't load,
	 * or there's no mapped `email_address` value, then nothing happens.
	 *
	 * @param WPCF7_ContactForm $obj
	 *
	 * @return WPCF7_ContactForm
	 */
	public function process_submission( $obj ) {

		$cf_id = method_exists( $obj, 'id' ) ? $obj->id() : $obj->id;

		$cf7_ctct = get_option( 'cf7_ctct_' . $cf_id );

		// Let the shortcode functionality work with the data using a filter.
		$cf7_ctct = apply_filters( 'ctctcf7_push', apply_filters( 'ctctcf7_push_form_' . $cf_id, $cf7_ctct, $obj ), $obj );

		if ( empty( $cf7_ctct ) ) {
			return $obj;
		}

		if ( empty( $cf7_ctct['active'] ) || empty( $cf7_ctct['fields'] ) ) {
			return $obj;
		}

		self::get_includes();

		// If it doesn't load for some reason....
		if ( ! class_exists( 'CTCT_SuperClass' ) ) {
			return $obj;
		}

		$contact = array();
		foreach ( $cf7_ctct['fields'] as $key => $field ) {

			if ( empty( $key ) || empty( $field ) ) {
				continue;
			}

			$value = self::get_submitted_value( $field, $obj );

			$contact[ $key ] = self::process_field( $key, $value );

		}

		$contact = self::process_contact( $contact );

		// We need an email address to continue.
		if ( empty( $contact['email_address'] ) || ! is_email( $contact['email_address'] ) ) {
			return $obj;
		}

		// For debug only
		#$contact['email_address'] = str_replace( '@', rand( 0, 10000 ) . '@', $contact['email_address'] ); // REMOVE!!!!!

		$CTCT_SuperClass = new CTCT_SuperClass;

		$contact_exists = $CTCT_SuperClass->CC_ContactsCollection()->searchByEmail( $contact['email_address'] );

		// If there's a field to opt in, and the opt-in field is empty, return.
		if ( ! empty( $cf7_ctct['accept'] ) ) {
			$accept = self::get_submitted_value( $cf7_ctct['accept'], $obj );
			if ( empty( $accept ) ) {
				return $obj;
			}
			$contact['opt_in_source'] = 'ACTION_BY_CONTACT';
		} else {
			// Don't send them a welcome email
			$contact['opt_in_source'] = 'ACTION_BY_CUSTOMER';
		}

		$response = NULL;

		// calculate subscribed lists
		$requested_lists = self::get_submitted_lists();

		// Create a new contact.
		if ( ! $contact_exists ) {
			$expected_response = 201;

			$Contact         = $CTCT_SuperClass->CC_Contact( $contact );
			$ExistingContact = false;

			foreach ( $requested_lists as $list ) {
				$Contact->setLists( $list );
			}

			$Contact->setOptInSource( $contact['opt_in_source'] );

			// Only create contact if there were requested lists
			if ( $Contact->getLists() ) {
				$response = $CTCT_SuperClass->CC_ContactsCollection()->createContact( $Contact, false );
			}

		} // Update existing contact
		else {
			$expected_response = 204;

			$Contact         = false;
			$ExistingContact = $CTCT_SuperClass->CC_ContactsCollection()->listContactDetails( $contact_exists[0][0] );
			$ExistingContact->setOptInSource( $contact['opt_in_source'] );

			// Update the existing contact with the new data
			self::mapMergeVars( $contact, $ExistingContact );

			/**
			 * If you want to replace contact lists instead of updating them, use `__return_false`
			 *
			 * @since 2.1
			 *
			 * @param boolean $update True: Only add lists to existing contacts; False: Replace lists with submission selections
			 */
			if ( apply_filters( 'ctctcf7_update_contact_lists', true ) ) {
				$lists = $ExistingContact->getLists();
			} else {
				$lists = array();
			}

			foreach ( $requested_lists as $list ) {
				$list_to_add = 'https://api.constantcontact.com' . $list;

				if ( ! in_array( $list_to_add, $lists ) ) {
					$lists[] = $list_to_add;
				}
			}

			// Remove existing lists then re-add them
			$ExistingContact->removeLists();

			// Add contact lists
			foreach ( $lists as $list ) {
				$ExistingContact->setLists( $list );
			}

			// Only update contact if there were requested lists
			if ( $ExistingContact->getLists() ) {
				$response = $CTCT_SuperClass->CC_ContactsCollection()->updateContact( $ExistingContact->getId(), $ExistingContact, false );
			}
		}

		if ( empty( $response['info'] ) || ( intval( $response['info']['http_code'] ) !== intval( $expected_response ) ) ) {
			do_action( 'cf7_ctct_failed', $response, $Contact, $ExistingContact );
		} else {
			do_action( 'cf7_ctct_succeeded', $response, $Contact, $ExistingContact );
		}

		return $obj;
	}

	static function get_submitted_lists() {
		$submitted = NULL;
		$lists     = $output = array();
		if ( class_exists( 'WPCF7_Submission' ) ) {
			$submission = WPCF7_Submission::get_instance();
			$submitted  = $submission ? $submission->get_posted_data() : NULL;
		}

		if ( $submitted !== NULL && is_array( $submitted ) ) {
			foreach ( $submitted as $key => $data ) {
				if ( false !== strpos( $key, 'ctct-' ) && ! empty( $data ) && is_array( $data ) ) {
					$lists = array_merge( $lists, $data );
				}
			}
		}

		$all_lists = CTCT_SuperClass::getAvailableLists();
		if ( is_array( $all_lists ) ) {
			foreach ( $all_lists as $list ) {
				if ( in_array( $list['id'], (array) $lists ) ) {
					$output[] = $list['link'];
				}
			}
		}

		return $output;
	}

	/**
	 * @param array $contact Submitted contact details
	 * @param CTCTContact $ExistingContact Existing contact object
	 */
	static private function mapMergeVars( $contact, &$ExistingContact ) {
		if ( ! empty( $contact['first_name'] ) ) {
			$ExistingContact->setFirstName( $contact['first_name'] );
		}
		if ( ! empty( $contact['middle_name'] ) ) {
			$ExistingContact->setMiddleName( $contact['middle_name'] );
		}
		if ( ! empty( $contact['last_name'] ) ) {
			$ExistingContact->setLastName( $contact['last_name'] );
		}
		if ( ! empty( $contact['company_name'] ) ) {
			$ExistingContact->setCompanyName( $contact['company_name'] );
		}
		if ( ! empty( $contact['job_title'] ) ) {
			$ExistingContact->setJobTitle( $contact['job_title'] );
		}
		if ( ! empty( $contact['home_number'] ) ) {
			$ExistingContact->setHomeNumber( $contact['home_number'] );
		}
		if ( ! empty( $contact['work_number'] ) ) {
			$ExistingContact->setWorkNumber( $contact['work_number'] );
		}
		if ( ! empty( $contact['address_line_1'] ) ) {
			$ExistingContact->setAddr1( $contact['address_line_1'] );
		}
		if ( ! empty( $contact['address_line_2'] ) ) {
			$ExistingContact->setAddr2( $contact['address_line_2'] );
		}
		if ( ! empty( $contact['address_line_3'] ) ) {
			$ExistingContact->setAddr3( $contact['address_line_3'] );
		}
		if ( ! empty( $contact['city_name'] ) ) {
			$ExistingContact->setCity( $contact['city_name'] );
		}
		if ( ! empty( $contact['state_code'] ) ) {
			$ExistingContact->setStateCode( $contact['state_code'] );
			$ExistingContact->setStateName( '' );
		}
		if ( ! empty( $contact['state_name'] ) ) {
			$ExistingContact->setStateName( $contact['state_name'] );
			$ExistingContact->setStateCode( '' );
		}
		if ( ! empty( $contact['country_code'] ) ) {
			$ExistingContact->setCountryCode( $contact['country_code'] );
		}
		if ( ! empty( $contact['zip_code'] ) ) {
			$ExistingContact->setPostalCode( $contact['zip_code'] );
		}
		if ( ! empty( $contact['sub_zip_code'] ) ) {
			$ExistingContact->setSubPostalCode( $contact['sub_zip_code'] );
		}
		if ( ! empty( $contact['notes'] ) ) {
			$ExistingContact->setNotes( $contact['notes'] );
		}
		if ( ! empty( $contact['custom_field_1'] ) ) {
			$ExistingContact->setCustomField1( $contact['custom_field_1'] );
		}
		if ( ! empty( $contact['custom_field_2'] ) ) {
			$ExistingContact->setCustomField2( $contact['custom_field_2'] );
		}
		if ( ! empty( $contact['custom_field_3'] ) ) {
			$ExistingContact->setCustomField3( $contact['custom_field_3'] );
		}
		if ( ! empty( $contact['custom_field_4'] ) ) {
			$ExistingContact->setCustomField4( $contact['custom_field_4'] );
		}
		if ( ! empty( $contact['custom_field_5'] ) ) {
			$ExistingContact->setCustomField5( $contact['custom_field_5'] );
		}
		if ( ! empty( $contact['custom_field_6'] ) ) {
			$ExistingContact->setCustomField6( $contact['custom_field_6'] );
		}
		if ( ! empty( $contact['custom_field_7'] ) ) {
			$ExistingContact->setCustomField7( $contact['custom_field_7'] );
		}
		if ( ! empty( $contact['custom_field_8'] ) ) {
			$ExistingContact->setCustomField8( $contact['custom_field_8'] );
		}
		if ( ! empty( $contact['custom_field_9'] ) ) {
			$ExistingContact->setCustomField9( $contact['custom_field_9'] );
		}
		if ( ! empty( $contact['custom_field_10'] ) ) {
			$ExistingContact->setCustomField10( $contact['custom_field_10'] );
		}
		if ( ! empty( $contact['custom_field_11'] ) ) {
			$ExistingContact->setCustomField11( $contact['custom_field_11'] );
		}
		if ( ! empty( $contact['custom_field_12'] ) ) {
			$ExistingContact->setCustomField12( $contact['custom_field_12'] );
		}
		if ( ! empty( $contact['custom_field_13'] ) ) {
			$ExistingContact->setCustomField13( $contact['custom_field_13'] );
		}
		if ( ! empty( $contact['custom_field_14'] ) ) {
			$ExistingContact->setCustomField14( $contact['custom_field_14'] );
		}
		if ( ! empty( $contact['custom_field_15'] ) ) {
			$ExistingContact->setCustomField15( $contact['custom_field_15'] );
		}
	}

	/**
	 * If there's a mapped "full_name" field, parse the name into first, middle, and last name pieces
	 *
	 * @param array $contact
	 *
	 * @return array
	 */
	static public function process_contact( $contact = array() ) {

		// Process the full name tag
		if ( ! empty( $contact['full_name'] ) ) {

			if ( ! function_exists( 'cf7_newsletter_parse_name' ) ) {
				require plugin_dir_path( __FILE__ ) . 'nameparse.php';
			}

			// In case it didn't load for some reason...
			if ( function_exists( 'cf7_newsletter_parse_name' ) ) {

				$name = cf7_newsletter_parse_name( $contact['full_name'] );

				if ( isset( $name['first'] ) ) {
					$contact['first_name'] = $name['first'];
				}

				if ( isset( $name['middle'] ) ) {
					$contact['middle_name'] = $name['middle'];
				}

				if ( isset( $name['last'] ) ) {
					$contact['last_name'] = $name['last'];
				}

				unset( $contact['full_name'] );
			}
		}

		return $contact;
	}

	/**
	 * If there are custom cases for a field, process them here.
	 *
	 * @param  string $key Key from CTCT_SuperClass::listMergeVars()
	 * @param  string $value Passed value
	 *
	 * @return string        return $value
	 */
	static function process_field( $key, $value ) {
		return $value;
	}

	/**
	 * Get the value from the submitted fields
	 *
	 * @param  string $subject The name of the field; ie: [first-name]
	 * @param  array $posted_data The posted data, in array form.
	 *
	 * @return [type]               [description]
	 */
	static function get_submitted_value( $subject, &$obj, $pattern = '/\[\s*([a-zA-Z_][0-9a-zA-Z:._-]*)\s*\]/' ) {

		// new method ( since Contact Form 7 Version 4.0.1 )
		if ( function_exists( 'wpcf7_mail_replace_tags' ) ) {

			$subject  = preg_replace( '/^(?:\[?)(.*)(?:\]?)$/ism', '[$1]', $subject );
			$replaced = wpcf7_mail_replace_tags( $subject );

			return $replaced;

		}

		// Keeping below for back compatibility (??)
		if ( is_callable( array( $obj, 'replace_mail_tags' ) ) ) {

			// Make sure the title is wrapped in []
			$subject = preg_replace( '/^(?:\[?)(.*)(?:\]?)$/ism', '[$1]', $subject );

			$replaced = $obj->replace_mail_tags( $subject );

			return $replaced;
		}


		// Keeping below for back compatibility
		$posted_data = $obj->posted_data;
		if ( preg_match( $pattern, $subject, $matches ) > 0 ) {

			if ( isset( $posted_data[ $matches[1] ] ) ) {
				$submitted = $posted_data[ $matches[1] ];

				if ( is_array( $submitted ) ) {
					$replaced = join( ', ', $submitted );
				} else {
					$replaced = $submitted;
				}

				if ( $html ) {
					$replaced = strip_tags( $replaced );
					$replaced = wptexturize( $replaced );
				}

				$replaced = apply_filters( 'wpcf7_mail_tag_replaced', $replaced, $submitted );

				return stripslashes( $replaced );
			}

			if ( $special = apply_filters( 'wpcf7_special_mail_tags', '', $matches[1] ) ) {
				return $special;
			}

			return $matches[0];
		}

		return $subject;
	}

}

new CTCTCF7;


if(!function_exists('r')) {
	function r( $code, $die = false, $title = '' ) {
		if ( ! empty( $title ) ) {
			echo '<h3>' . $title . '</h3>';
		}
		echo '<pre>';
		echo print_r( $code, true );
		echo '</pre>';
		if ( $die ) {
			die();
		}
	}
}