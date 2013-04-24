<?php
/*
Plugin Name: Contact Form 7 - Constant Contact Module
Plugin URI: http://www.katzwebservices.com
Description: Add the power of Constant Contact to Contact Form 7
Author: Katz Web Services, Inc.
Author URI: http://www.katzwebservices.com
Version: 1.1
*/

/*  Copyright 2012 Katz Web Services, Inc. (email: info@katzwebservices.com)

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

class CTCTCF7 {

	function __construct() {

		add_action('admin_init', array('CTCTCF7', 'settings_init'));
		add_action('admin_head', array('CTCTCF7', 'admin_head'));
		add_filter('plugin_action_links', array('CTCTCF7', 'plugins_action_links'), 10, 2 );
		add_action('admin_menu', array('CTCTCF7', 'admin_menu'));
		add_action('wpcf7_after_save', array('CTCTCF7', 'save_form_settings'));
		add_action('wpcf7_admin_before_subsubsub', array('CTCTCF7', 'add_meta_box' ));
		add_action('wpcf7_admin_after_mail_2', array('CTCTCF7', 'show_ctct_metabox' ));

		// CF7 Processing
		add_action( 'wpcf7_mail_sent', array('CTCTCF7', 'process_submission' ));
	}

	function admin_head() {
		global $plugin_page;

		if($plugin_page === 'ctct_cf7') { wp_enqueue_script('thickbox'); }

		if($plugin_page !== 'wpcf7') { return; }

		?>
		<script type="text/javascript">

		jQuery(document).ready(function($) {
			$('#wpcf7-ctct-active').change(function() {
				if($(this).is(':checked')) {
					$('#wpcf7-ctct-all-fields').show();
				} else {
					$('#wpcf7-ctct-all-fields').hide();
				}
			}).trigger('change');
		});
	</script>
	<?php
	}

	function plugins_action_links( $links, $file ) {
		if ( $file != plugin_basename( __FILE__ ) )
			return $links;

		$settings_link = '<a href="' . admin_url('admin.php?page=ctct_cf7') . '">' . esc_html( __( 'Settings', 'wpcf7' ) ) . '</a>';

		array_unshift( $links, $settings_link );

		return $links;

	}

	function get_includes() {
		if(!class_exists("CTCTUtility")) { require_once("api/ctctWrapper.php"); }
		if(!class_exists("CTCT_SuperClass")) { require_once("api/ctct_cf7_superclass.php"); }
	}


	private static function validateApi() {
		$utility = new CTCTUtility();
		$return = $utility->httpGet($utility->getApiPath() . '/ws/customers/'. $utility->getLogin() .'/contacts?email=' . urlencode('asdasdasdasdsadsadasdas@asdmgmsdfdaf.com'));
		return $return['info']['http_code'] === 200 && empty($return['error']);
	}

	function get_password() {
		if(isset($_POST['ctct_cf7'])) { return $_POST['ctct_cf7']['password']; }
		$settings = get_option('ctct_cf7');
		$value = !empty($settings['password']) ? $settings['password'] : NULL;
		return $value;
	}

	function get_username() {
		if(isset($_POST['ctct_cf7'])) { return $_POST['ctct_cf7']['username']; }
		$settings = get_option('ctct_cf7');
		$value = !empty($settings['username']) ? $settings['username'] : NULL;
		return $value;
	}

	function admin_menu() {
		add_submenu_page( 'wpcf7', __('Constant Contact Contact Form 7 Settings'), __('Constant Contact'), 'manage_options', 'ctct_cf7', array('CTCTCF7', 'settings_page'));
	}

	function settings_init() {

		self::get_includes();

		register_setting('ctct_cf7', 'ctct_cf7');

		add_settings_section(
			'ctct_api',
			__('Configure your Constant Contact account settings.'),
			array('CTCTCF7', 'setting_description'),
			'ctct_cf7'
		);
		add_settings_field(
			'ctct_cf7_username',
			__('Constant Contact Username'),
			array('CTCTCF7', 'setting_input_username'),
			'ctct_cf7',
			'ctct_api'
		);
		add_settings_field(
			'ctct_cf7_password',
			__('Constant Contact Password'),
			array('CTCTCF7', 'setting_input_password'),
			'ctct_cf7',
			'ctct_api'
		);
	}

	function setting_description() {
		echo 'Enter the username and password you use to log in to Constant Contact.';
	}

	function setting_input_username() {
		echo '<input autocomplete="off" name="ctct_cf7[username]" id="ctct_cf7_username" type="text" value="'.self::get_username().'" class="text" />';
	}

	function setting_input_password() {
		echo '<input autocomplete="off" name="ctct_cf7[password]" id="ctct_cf7_password" type="password" value="'.self::get_password().'" class="password" />';
	}

	/**
	 * Check the status of a plugin.
	 *
	 * @param string $plugin Base plugin path from plugins directory.
	 * @return int 1 if active; 2 if inactive; 0 if not installed
	 */
	function get_plugin_status($location = '') {

		$errors = validate_plugin($location);

		// Plugin is found
		if(!is_wp_error($errors)) {
			if(is_plugin_inactive($location)) {
				return 2;
			}
			return 1;
		}
		else {
			return false;
		}
	}

	function settings_page() {

?>
	<div class="wrap">
		<img src="<?php echo plugins_url('CTCT_horizontal_logo.png', __FILE__); ?>" width="281" height="47" alt="Constant Contact" style="margin-top:1em;" />
		<h2 style="padding-top:0;margin-bottom:.5em;"><?php _e('Contact Form 7 Module', 'wpcf7'); ?></h2>
		<form action="options.php" method="post">
			<?php
				$valid = self::validateApi();

				$message = '';
				$status = self::get_plugin_status('contact-form-7/wp-contact-form-7.php');
				switch($status) {
					case 1: $message = false; break;
					case 2: $message = __('Contact Form 7 is installed but inactive. Activate Contact Form 7 to use this plugin.', 'wpcf7'); break;
					case 0: $message = __(sprintf('Contact Form 7 is not installed. <a href="%s" class="thickbox" title="Install Contact Form 7">Install the Contact Form 7 plugin</a> to use this plugin.', admin_url('plugin-install.php?tab=plugin-information&amp;plugin=contact-form-7&amp;TB_iframe=true&amp;width=600&amp;height=550')), 'wpcf7'); break;
				}

				if(!empty($message)) {
					echo sprintf("<div id='message' class='error'><p>%s</p></div>", $message);
				}

				if(is_null(self::get_password()) && is_null(self::get_username())) {
					self::show_signup_message();
				} elseif($valid) {
					echo "<div id='message' class='updated'><p>".__('Your username and password seem to be working.', 'wpcf7')."</p></div>";
				} elseif(is_null(self::get_password())) {
					echo "<div id='message' class='error'><p>".__('Your password is empty.', 'wpcf7')."</p></div>";
				} elseif(is_null(self::get_username())) {
					echo "<div id='message' class='error'><p>".__('Your username is empty.', 'wpcf7')."</p></div>";
				} else {
					echo "<div id='message' class='error'><p>".__('Your username and password are not configured properly.', 'wpcf7')."</p></div>";
				}
				settings_fields('ctct_cf7');
				do_settings_sections('ctct_cf7');
			?>
			<p class="submit"><input class="button-primary" type="submit" name="Submit" value="<?php _e('Submit', 'wpcf7'); ?>" />
		</form>
	</div><!-- .wrap -->
	<?php
	}

	function show_signup_message() {
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
			background-image: url(http://img.constantcontact.com/lp/images/standard/bv2/product_pages/test/grow_with_email_text.png);
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
		.get-em:hover { background-position: left bottom; }
		.learn-more { margin-left: 15px!important; }
		#enter-account-details {
			width:100%; border-top:1px solid #ccc; margin-top:1em; padding-top:.5em;
		}
	</style>
	<div style="clear:left; float:left;">
		<h2 class="clear" style="font-size:23px; color: #555;"><strong>Hello!</strong> This plugin requires <a href="http://katz.si/4i" title="Learn more about Constant Contact">a Constant Contact account</a>.</h2>
		<p id="grow-with-email"><a href="http://katz.si/4i"><strong>Grow with Email Marketing. Guaranteed.</strong> With Email Marketing, it's easy for you to connect with your customers, and for customers to share your message with their networks. And the more customers spread the word about your business, the more you grow</a></p>
		<p><a class="get-em" href="http://katz.si/4w">Start Your Free Trial</a></p>
		<h2 class="learn-more alignleft"> or <a href="http://katz.si/4i">Learn More</a></h2>
		<div class="clear"></div>
	</div>

	<h2 class="clear" id="enter-account-details"><?php _e('Enter your account details below:', 'wpcf7'); ?></h2>
	<?php
	}

	function save_form_settings($args) {
		update_option( 'cf7_ctct_'.$args->id, $_POST['wpcf7-ctct'] );
	}

	function add_meta_box() {
		if ( wpcf7_admin_has_edit_cap() ) {
		add_meta_box( 'cf7ctctdiv', __( 'Constant Contact', 'ctctcf7' ),
			array('CTCTCF7', 'metabox'), 'cfseven', 'cf7_ctct', 'core',
			array(
				'id' => 'ctctcf7',
				'name' => 'cf7_ctct',
				'use' => __( 'Use Constant Contact', 'ctctcf7' ) ) );
		}
	}

	function show_ctct_metabox($cf){
		do_meta_boxes( 'cfseven', 'cf7_ctct', $cf );
	}

	function metabox($args) {
		$cf7_ctct_defaults = array();
		$cf7_ctct = get_option( 'cf7_ctct_'.$args->id, $cf7_ctct_defaults );
	?>

	<img src="<?php echo plugins_url('CTCT_horizontal_logo.png', __FILE__); ?>" width="281" height="47" alt="Constant Contact Logo" style="margin-top:.5em;" />
	<span class="alignright"><a href="<?php echo add_query_arg(array('cache' => rand(1,20000))); ?>#cf7ctctdiv"><?php _e('Refresh Lists', 'ctctcf7'); ?></a></span>

<?php if(self::validateApi()) { ?>
<div class="mail-field clear" style="padding-bottom:.75em">
	<input type="checkbox" id="wpcf7-ctct-active" name="wpcf7-ctct[active]" value="1"<?php checked((isset($cf7_ctct['active']) && $cf7_ctct['active']==1), true); ?> />
	<label for="wpcf7-ctct-active"><?php echo esc_html( __( 'Integrate Form With Constant Contact', 'wpcf7' ) ); ?></label>
</div>
	<?php } else { ?>
<div class="mail-field clear">
	<p class="error"><?php _e(sprintf('The plugin\'s Constant Contact settings are not configured properly. <a href="%s">Go configure them now.', admin_url('admin.php?page=ctct_cf7')), 'wpcf7'); ?></a></p>
</div>
	<?php return; } ?>


<div class="mail-fields clear" id="wpcf7-ctct-all-fields">

	<div class="half-left">
		<div class="mail-field">
			<label for="wpcf7-ctct-list"><strong><?php echo esc_html( __( 'Lists:', 'wpcf7' ) ); ?></strong></label><br />
			<ul style="columns:15em; -webkit-columns:15em; -moz-columns:15em;">
			<?php
			$lists = CTCT_SuperClass::getAvailableLists();
			foreach($lists as $list) {
				echo '
				<li>
					<label for="wpcf7-ctct-list-'.$list['id'].'">
						<input type="checkbox" name="wpcf7-ctct[lists][]" id="wpcf7-ctct-list-'.$list['id'].'" value="'.$list['link'].'" '.@checked((is_array($list) && in_array($list['link'], (array)$cf7_ctct['lists'])), true, false).' />
					'.$list['name'].'
					</label>
				</li>';
			}
			?>
			</ul>
		</div>
	</div>

	<div class="half-right">
		<div class="mail-field">
			<label for="wpcf7-ctct-accept"><?php echo esc_html( __( 'Opt-In Field:', 'wpcf7' ) ); ?> <span class="description"><?php _e('If the user should check a box to be added to the lists, enter the checkbox field here. Leave blank to have no required field.', 'wpcf7'); ?></span></label><br />
			<input type="text" id="wpcf7-ctct-accept" name="wpcf7-ctct[accept]" placeholder="Example: [checkbox-456]" class="wide" size="70" value="<?php echo esc_attr( isset($cf7_ctct['accept']) ? $cf7_ctct['accept'] : '' ); ?>" />
		</div>
	</div>

	<div class="clear ctct-fields">
		<hr style="border:0; border-bottom:1px solid #ccc; padding-top:1em" />
		<?php
			echo '<h2 style="margin-bottom:0;">'.__('How to Integrate your form with Constant Contact', 'wpcf7').'</h2>';
			echo wpautop(__('<div class="alignleft" style="width:30%; margin-right:2%; max-width:350px;">
	<h4 style="font-size:1.2em;">If your form looks like this:</h4>
<pre class="code">Your email: [email* your-email]
First name: [text first-name]
Middle name: [text middle-name]
Last name: [text last-name]
Job Title: [text job-title]
Phone: [text phone]
Work Phone: [text work-phone]</pre>
</div>
<div class="alignleft" style="width:30%; margin-right:4%; max-width:350px;">
	<h4 style="font-size:1.2em;">You will want to enter the following into the fields below:</h4>
	<ul>
		<li>Email Address: <code>[your-email]</code></li>
		<li>First Name: <code>[first-name]</code></li>
		<li>Middle Name: <code>[middle-name]</code></li>
		<li>Last Name: <code>[last-name]</code></li>
		<li>Phone: <code>[phone]</code></li>
		<li>Work Phone: <code>[work-phone]</code></li>
	</ul>
</div>
<div class="alignleft" style="max-width:450px;">
	<h4 style="font-size:1.2em;">Here is how an this form would look filled out:</h4>
	<a href="http://s.wordpress.org/extend/plugins/contact-form-7-newsletter/screenshot-2.jpg" target="_blank"><img src="http://s.wordpress.org/extend/plugins/contact-form-7-newsletter/screenshot-2.jpg" style="max-width:530px" /><span class="howto">Click to view larger</span></a>
</div>
<div class="clear"></div>
<h2>Integration Fields</h2>
', 'wpcf7'));
			$i = 0;
			foreach(CTCT_SuperClass::listMergeVars() as $var) {
		?>
			<div class="half-<?php if($i % 2 === 0) { echo 'left'; } else { echo 'right'; }?>" style="clear:none;">
				<div class="mail-field">
				<label for="wpcf7-ctct-<?php echo $var['tag']; ?>"><?php echo $var['name']; echo !empty($var['req']) ? _e(' <strong>&larr; This setting is required.</strong>', 'wpcf7') : ''; ?></label><br />
				<input type="text" id="wpcf7-ctct-<?php echo isset($var['tag']) ? $var['tag'] : ''; ?>" name="wpcf7-ctct[fields][<?php echo isset($var['tag']) ? $var['tag'] : ''; ?>]" class="wide" size="70" value="<?php echo @esc_attr( isset($cf7_ctct['fields'][$var['tag']]) ? $cf7_ctct['fields'][$var['tag']] : '' ); ?>" <?php if(isset($var['placeholder'])) { echo ' placeholder="Example: '.$var['placeholder'].'"'; } ?> />
				</div>
			</div>

		<?php
			if($i % 2 === 1) { echo '<div class="clear"></div>'; }
			$i++;
		 } ?>

	</div>
	<div class="clear"></div>
</div>
<?php
	}


	function process_submission($obj) {

		$cf7_ctct = get_option( 'cf7_ctct_'.$obj->id );

		if(empty($cf7_ctct)) { return $obj; }

		$subscribe = true;

		if(empty($cf7_ctct['active']) || empty($cf7_ctct['fields']) || empty($cf7_ctct['lists'])) { return $obj; }

		self::get_includes();

		// If it doesn't load for some reason....
		if(!class_exists('CTCT_SuperClass')) { return $obj; }

		$contact = array();
		foreach($cf7_ctct['fields'] as $key => $field) {
			$value = self::get_submitted_value($field, $obj);
			$contact[$key] = self::process_field($key, $value);
		}

		// If there's a field to opt in, and the opt-in field is empty, return.
		if(!empty($cf7_ctct['accept'])) {
			$accept = self::get_submitted_value($cf7_ctct['accept'], $obj );
			if(empty($accept)) { return $obj; }
			$action = 'ACTION_BY_CONTACT';
		} else {
			// Don't send them a welcome email
			$action = 'ACTION_BY_CUSTOMER';
		}

		$contact = self::process_contact($contact);

		// For debug only
		#$contact['email_address'] = rand(0,10000).$contact['email_address']; // REMOVE!!!!!

		$contact_exists = CTCT_SuperClass::CC_ContactsCollection()->searchByEmail($contact['email_address']);

		if(!$contact_exists) {
			$expected_response = 201;

			$Contact = CTCT_SuperClass::CC_Contact($contact);
			$ExistingContact = false;

			foreach((array)$cf7_ctct['lists'] as $list) {
				$Contact->setLists($list);
			}

			$response = CTCT_SuperClass::CC_ContactsCollection()->createContact($Contact, false);

		} else {
			$expected_response = 204;

			$Contact = false;
			$ExistingContact = CTCT_SuperClass::CC_ContactsCollection()->listContactDetails($contact_exists[0][0]);

			// Update the existing contact with the new data
			self::mapMergeVars($contact, $ExistingContact);

			// Update Lists
			$lists = $ExistingContact->getLists();

			foreach((array)$cf7_ctct['lists'] as $list) {
				$lists[] = 'http://api.constantcontact.com'.$list;
			}

			$set_lists = array();
			foreach($lists as $list) {
				if(!in_array($list, $set_lists)) { $set_lists[] = $list; }
			}

			foreach($set_lists as $list) {
				$ExistingContact->setLists($list);
			}

			$response = CTCT_SuperClass::CC_ContactsCollection()->updateContact($ExistingContact->getId(), $ExistingContact, false);
		}

		if(floatval($response['info']['http_code']) !== floatval($expected_response)) {
			do_action('cf7_ctct_failed', $response, $Contact, $ExistingContact);
		}

		return $obj;
	}

	public function mapMergeVars($contact, &$ExistingContact) {
		if(!empty($contact['first_name'])) { $ExistingContact->setFirstName($contact['first_name']); }
		if(!empty($contact['middle_name'])) { $ExistingContact->setMiddleName($contact['middle_name']); }
		if(!empty($contact['last_name'])) { $ExistingContact->setLastName($contact['last_name']); }
		if(!empty($contact['company_name'])) { $ExistingContact->setCompanyName($contact['company_name']); }
		if(!empty($contact['job_title'])) { $ExistingContact->setJobTitle($contact['job_title']); }
		if(!empty($contact['home_number'])) { $ExistingContact->setHomeNumber($contact['home_number']); }
		if(!empty($contact['work_number'])) { $ExistingContact->setWorkNumber($contact['work_number']); }
		if(!empty($contact['address_line_1'])) { $ExistingContact->setAddr1($contact['address_line_1']); }
		if(!empty($contact['address_line_2'])) { $ExistingContact->setAddr2($contact['address_line_2']); }
		if(!empty($contact['address_line_3'])) { $ExistingContact->setAddr3($contact['address_line_3']); }
		if(!empty($contact['city_name'])) { $ExistingContact->setCity($contact['city_name']); }
		if(!empty($contact['state_code'])) {
			$ExistingContact->setStateCode($contact['state_code']);
			$ExistingContact->setStateName('');
		}
		if(!empty($contact['state_name'])) {
			$ExistingContact->setStateName($contact['state_name']);
			$ExistingContact->setStateCode('');
		}
		if(!empty($contact['country_code'])) { $ExistingContact->setCountryCode($contact['country_code']); }
		if(!empty($contact['zip_code'])) { $ExistingContact->setPostalCode($contact['zip_code']); }
		if(!empty($contact['sub_zip_code'])) { $ExistingContact->setSubPostalCode($contact['sub_zip_code']); }
		if(!empty($contact['notes'])) { $ExistingContact->setNotes($contact['notes']); }
		if(!empty($contact['custom_field_1'])) { $ExistingContact->setCustomField1($contact['custom_field_1']); }
		if(!empty($contact['custom_field_2'])) { $ExistingContact->setCustomField2($contact['custom_field_2']); }
		if(!empty($contact['custom_field_3'])) { $ExistingContact->setCustomField3($contact['custom_field_3']); }
		if(!empty($contact['custom_field_4'])) { $ExistingContact->setCustomField4($contact['custom_field_4']); }
		if(!empty($contact['custom_field_5'])) { $ExistingContact->setCustomField5($contact['custom_field_5']); }
		if(!empty($contact['custom_field_6'])) { $ExistingContact->setCustomField6($contact['custom_field_6']); }
		if(!empty($contact['custom_field_7'])) { $ExistingContact->setCustomField7($contact['custom_field_7']); }
		if(!empty($contact['custom_field_8'])) { $ExistingContact->setCustomField8($contact['custom_field_8']); }
		if(!empty($contact['custom_field_9'])) { $ExistingContact->setCustomField9($contact['custom_field_9']); }
		if(!empty($contact['custom_field_10'])) { $ExistingContact->setCustomField10($contact['custom_field_10']); }
		if(!empty($contact['custom_field_11'])) { $ExistingContact->setCustomField11($contact['custom_field_11']); }
		if(!empty($contact['custom_field_12'])) { $ExistingContact->setCustomField12($contact['custom_field_12']); }
		if(!empty($contact['custom_field_13'])) { $ExistingContact->setCustomField13($contact['custom_field_13']); }
		if(!empty($contact['custom_field_14'])) { $ExistingContact->setCustomField14($contact['custom_field_14']); }
		if(!empty($contact['custom_field_15'])) { $ExistingContact->setCustomField15($contact['custom_field_15']); }
	}

	public function process_contact($contact = array()) {

		// Process the full name tag
		if(!empty($contact['full_name'])) {

			@include_once(plugin_dir_path(__FILE__).'nameparse.php');

			// In case it didn't load for some reason...
			if(function_exists('cf7_newsletter_parse_name')) {

				$name = cf7_newsletter_parse_name($contact['full_name']);

				if(isset($name['first'])) { $contact['first_name'] = $name['first']; }

				if(isset($name['middle'])) { $contact['middle_name'] = $name['middle']; }

				if(isset($name['last'])) { $contact['last_name'] = $name['last']; }

				unset($contact['full_name']);
			}
		}

		return $contact;
	}
	/**
	 * If there are custom cases for a field, process them here.
	 * @param  string $key   Key from CTCT_SuperClass::listMergeVars()
	 * @param  string $value Passed value
	 * @return string        return $value
	 */
	function process_field($key, $value) {
		return $value;
	}

	/**
	 * Get the value from the submitted fields
	 * @param  string  $subject     The name of the field; ie: [first-name]
	 * @param  array  $posted_data The posted data, in array form.
	 * @return [type]               [description]
	 */
	function get_submitted_value($subject, $obj, $pattern = '/\[\s*([a-zA-Z_][0-9a-zA-Z:._-]*)\s*\]/') {

		if(is_callable(array($obj, 'replace_mail_tags'))) {
			return $obj->replace_mail_tags($subject);
		}

		// Keeping below for back compatibility
		$posted_data = $obj->posted_data;
		if( preg_match($pattern,$subject,$matches) > 0)
		{

			if ( isset( $posted_data[$matches[1]] ) ) {
				$submitted = $posted_data[$matches[1]];

				if ( is_array( $submitted ) )
					$replaced = join( ', ', $submitted );
				else
					$replaced = $submitted;

				if ( $html ) {
					$replaced = strip_tags( $replaced );
					$replaced = wptexturize( $replaced );
				}

				$replaced = apply_filters( 'wpcf7_mail_tag_replaced', $replaced, $submitted );

				return stripslashes( $replaced );
			}

			if ( $special = apply_filters( 'wpcf7_special_mail_tags', '', $matches[1] ) )
				return $special;

			return $matches[0];
		}
		return $subject;
	}

}

$CTCTCF7 = new CTCTCF7();
