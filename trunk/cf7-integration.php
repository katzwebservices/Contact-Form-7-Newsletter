<?php

add_action( 'wpcf7_init', 'wpcf7_ctct_register_service' );

function wpcf7_ctct_register_service() {
	$integration = WPCF7_Integration::get_instance();

	$categories = array(
		'newsletter' => __( 'Constant Contact', 'ctctcf7' ),
	);

	foreach ( $categories as $name => $category ) {
		$integration->add_category( $name, $category );
	}

	$services = array(
		'ctct' => WPCF7_CTCT::get_instance(),
	);

	foreach ( $services as $name => $service ) {
		$integration->add_service( $name, $service );
	}
}


class WPCF7_CTCT extends WPCF7_Service {

	const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

	private static $instance;
	private $sitekeys;

	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private function __construct() {
		$this->sitekeys = WPCF7::get_option( 'ctct' );
	}

	public function get_title() {
		return __( 'Constant Contact', 'contact-form-7' );
	}

	public function is_active() {
		$sitekey = $this->get_sitekey();
		$secret  = $this->get_secret( $sitekey );

		return $sitekey && $secret;
	}

	public function get_categories() {
		return array( 'ctct' );
	}

	public function icon() {
		$icon = sprintf(
			'<img src="%1$s" alt="%2$s" width="%3$d" height="%4$d" class="icon" />',
			wpcf7_plugin_url( 'images/service-icons/recaptcha-72x72.png' ),
			esc_attr( __( 'reCAPTCHA Logo', 'contact-form-7' ) ),
			36, 36 );
		echo $icon;
	}

	public function link() {
		echo sprintf( '<a href="%1$s">%2$s</a>',
			'https://www.google.com/recaptcha/intro/index.html',
			'google.com/recaptcha' );
	}

	public function get_sitekey() {
		if ( empty( $this->sitekeys ) || ! is_array( $this->sitekeys ) ) {
			return false;
		}

		$sitekeys = array_keys( $this->sitekeys );

		return $sitekeys[0];
	}

	public function get_secret( $sitekey ) {
		$sitekeys = (array) $this->sitekeys;

		if ( isset( $sitekeys[ $sitekey ] ) ) {
			return $sitekeys[ $sitekey ];
		} else {
			return false;
		}
	}

	public function verify( $response_token ) {
		$is_human = false;

		if ( empty( $response_token ) ) {
			return $is_human;
		}

		$url     = self::VERIFY_URL;
		$sitekey = $this->get_sitekey();
		$secret  = $this->get_secret( $sitekey );

		$response = wp_safe_remote_post( $url, array(
			'body' => array(
				'secret'   => $secret,
				'response' => $response_token,
				'remoteip' => $_SERVER['REMOTE_ADDR'],
			),
		) );

		if ( 200 != wp_remote_retrieve_response_code( $response ) ) {
			return $is_human;
		}

		$response = wp_remote_retrieve_body( $response );
		$response = json_decode( $response, true );

		$is_human = isset( $response['success'] ) && true == $response['success'];

		return $is_human;
	}

	private function menu_page_url( $args = '' ) {
		$args = wp_parse_args( $args, array() );

		$url = menu_page_url( 'wpcf7-integration', false );
		$url = add_query_arg( array( 'service' => 'recaptcha' ), $url );

		if ( ! empty( $args ) ) {
			$url = add_query_arg( $args, $url );
		}

		return $url;
	}

	public function load( $action = '' ) {
		if ( 'setup' == $action ) {
			if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
				check_admin_referer( 'wpcf7-recaptcha-setup' );

				$sitekey = isset( $_POST['sitekey'] ) ? trim( $_POST['sitekey'] ) : '';
				$secret  = isset( $_POST['secret'] ) ? trim( $_POST['secret'] ) : '';

				if ( $sitekey && $secret ) {
					WPCF7::update_option( 'recaptcha', array( $sitekey => $secret ) );
					$redirect_to = $this->menu_page_url( array(
						'message' => 'success',
					) );
				} elseif ( '' === $sitekey && '' === $secret ) {
					WPCF7::update_option( 'recaptcha', NULL );
					$redirect_to = $this->menu_page_url( array(
						'message' => 'success',
					) );
				} else {
					$redirect_to = $this->menu_page_url( array(
						'action'  => 'setup',
						'message' => 'invalid',
					) );
				}

				wp_safe_redirect( $redirect_to );
				exit();
			}
		}
	}

	public function admin_notice( $message = '' ) {
		if ( 'invalid' == $message ) {
			echo sprintf(
				'<div class="error notice notice-error is-dismissible"><p><strong>%1$s</strong>: %2$s</p></div>',
				esc_html( __( "ERROR", 'contact-form-7' ) ),
				esc_html( __( "Invalid key values.", 'contact-form-7' ) ) );
		}

		if ( 'success' == $message ) {
			echo sprintf( '<div class="updated notice notice-success is-dismissible"><p>%s</p></div>',
				esc_html( __( 'Settings saved.', 'contact-form-7' ) ) );
		}
	}

	public function display( $action = '' ) {
		?>
		<p><?php echo esc_html( __( "reCAPTCHA is a free service to protect your website from spam and abuse.", 'contact-form-7' ) ); ?></p>

		<?php
		if ( 'setup' == $action ) {
			$this->display_setup();

			return;
		}

		if ( $this->is_active() ) {
			$sitekey = $this->get_sitekey();
			$secret  = $this->get_secret( $sitekey );
			?>
			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row"><?php echo esc_html( __( 'Site Key', 'contact-form-7' ) ); ?></th>
					<td class="code"><?php echo esc_html( $sitekey ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html( __( 'Secret Key', 'contact-form-7' ) ); ?></th>
					<td class="code"><?php echo esc_html( wpcf7_mask_password( $secret ) ); ?></td>
				</tr>
				</tbody>
			</table>

			<p><a href="<?php echo esc_url( $this->menu_page_url( 'action=setup' ) ); ?>"
			      class="button"><?php echo esc_html( __( "Reset Keys", 'contact-form-7' ) ); ?></a></p>

			<?php
		} else {
			?>
			<p><?php echo esc_html( __( "To use reCAPTCHA, you need to install an API key pair.", 'contact-form-7' ) ); ?></p>

			<p><a href="<?php echo esc_url( $this->menu_page_url( 'action=setup' ) ); ?>"
			      class="button"><?php echo esc_html( __( "Configure Keys", 'contact-form-7' ) ); ?></a></p>

			<p><?php echo sprintf( esc_html( __( "For more details, see %s.", 'contact-form-7' ) ), wpcf7_link( __( 'http://contactform7.com/recaptcha/', 'contact-form-7' ), __( 'reCAPTCHA', 'contact-form-7' ) ) ); ?></p>
			<?php
		}
	}

	public function display_setup() {
		?>
		<form method="post" action="<?php echo esc_url( $this->menu_page_url( 'action=setup' ) ); ?>">
			<?php wp_nonce_field( 'wpcf7-recaptcha-setup' ); ?>
			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row"><label
							for="sitekey"><?php echo esc_html( __( 'Site Key', 'contact-form-7' ) ); ?></label></th>
					<td><input type="text" aria-required="true" value="" id="sitekey" name="sitekey"
					           class="regular-text code"/></td>
				</tr>
				<tr>
					<th scope="row"><label
							for="secret"><?php echo esc_html( __( 'Secret Key', 'contact-form-7' ) ); ?></label></th>
					<td><input type="text" aria-required="true" value="" id="secret" name="secret"
					           class="regular-text code"/></td>
				</tr>
				</tbody>
			</table>

			<p class="submit"><input type="submit" class="button button-primary"
			                         value="<?php echo esc_attr( __( 'Save', 'contact-form-7' ) ); ?>" name="submit"/>
			</p>
		</form>
		<?php
	}
}
