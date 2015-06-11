<?php
/**
 * @global object $args
 */

$CTCT_SuperClass = new CTCT_SuperClass;
$cf7_ctct_defaults = array();

$cf_id = method_exists( $args , 'id' ) ? $args->id() : $args->id;

$cf7_ctct = get_option( 'cf7_ctct_'. $cf_id, $cf7_ctct_defaults );

?>
<script>
	jQuery(document).ready(function($) {
		$('.ctctcf7-tooltip').tooltip({
			content: function () {
				return $(this).prop('title');
			}
		});
	});
</script>
<div class="ctctcf7-tooltip" title="<h6><?php _e('Backward Compatibility', 'ctctcf7'); ?></h6><p><?php _e('Starting with Version 2.0 of Contact Form 7 Newsletter plugin, the lists a form sends data to should be defined by generating a tag above &uarr;</p><p>For backward compatibility, <strong>if you don\'t define any forms using a tag above</strong>, your form will continue to send contact data to these lists:', 'ctctcf7'); ?></p><p><strong><?php esc_attr_e("For full instructions, go to the Contact > Constant Contact page and click 'View integration instructions'.", 'ctctcf7'); ?></strong></p>"><?php _e('Where are my lists?', 'ctctcf7'); ?></div>

<a href="http://katz.si/4w" target="_blank"><img src="<?php echo plugins_url('CTCT_horizontal_logo.png', __FILE__); ?>" width="281" height="47" alt="Constant Contact Logo" style="margin-top:.5em;" /></a>

<?php if(self::validateApi()) { ?>
	<div class="mail-field clear" style="padding-bottom:.75em">
		<input type="checkbox" id="wpcf7-ctct-active" name="wpcf7-ctct[active]" value="1"<?php checked((isset($cf7_ctct['active']) && $cf7_ctct['active']==1), true); ?> />
		<label for="wpcf7-ctct-active"><?php echo esc_html( __( 'Send form entries to Constant Contact', 'ctctcf7' ) ); ?></label>
	</div>
<?php } else { ?>
	<div class="mail-field clear">
		<div class="error inline"><p><?php _e(sprintf('The plugin\'s Constant Contact settings are not configured properly. <a href="%s">Go configure them now.', admin_url('admin.php?page=ctct_cf7')), 'ctctcf7'); ?></a></p></div>
	</div>
	<?php return; } ?>


<div class="mail-fields clear" id="wpcf7-ctct-all-fields">

	<!-- Backward Compatibility -->
	<div><?php
		if( !empty( $cf7_ctct['lists'] ) ) :
			foreach((array)$cf7_ctct['lists'] as $list) {
				echo '<input type="hidden" name="wpcf7-ctct[lists][]" value="'.$list.'"  />';
			}
		endif;
		?></div>
	<!-- End Backward Compatibility -->

	<div class="clear ctct-fields">
		<hr style="border:0; border-bottom:1px solid #ccc; padding-top:1em" />
		<div class="clear"></div>
		<?php

		$instructions = __('<h2>Integration Fields</h2>', 'ctctcf7');
		$instructions .= '<p class="howto">';
		$instructions .= __('For each of the Integration Fields below, select the value you would like sent to Constant Contact.', 'ctctcf7');
		$instructions .= '</p>';

		echo $instructions;
		?>

		<?php
		$i = 0;
		foreach($CTCT_SuperClass->listMergeVars() as $var) {
			?>
			<div class="half-<?php if($i % 2 === 0) { echo 'left'; } else { echo 'right'; }?>" style="clear:none;">
				<div class="mail-field">
					<label for="wpcf7-ctct-<?php echo $var['tag']; ?>"><?php echo $var['name']; echo !empty($var['req']) ? _e('&larr; This setting is required.</strong>', 'ctctcf7') : ''; ?></label><br />
					<input type="text" id="wpcf7-ctct-<?php echo isset($var['tag']) ? $var['tag'] : ''; ?>" name="wpcf7-ctct[fields][<?php echo isset($var['tag']) ? $var['tag'] : ''; ?>]" class="wide" size="70" value="<?php echo @esc_attr( isset($cf7_ctct['fields'][$var['tag']]) ? $cf7_ctct['fields'][$var['tag']] : '' ); ?>" <?php if(isset($var['placeholder'])) { echo ' placeholder="Example: '.$var['placeholder'].'"'; } ?> />
				</div>
			</div>

			<?php
			if($i % 2 === 1) { echo '<div class="clear"></div>'; }
			$i++;
		} ?>

		<div class="clear mail-field" style="width:50%;">
			<label for="wpcf7-ctct-accept"><?php echo esc_html( __( 'Opt-In Field', 'ctctcf7' ) ); ?>
				<span class="howto"><strong><?php printf( esc_html__('If you added a %s tag to your Form configuration, this setting is not necessary, and will be ignored.', 'ctctcf7'), '<code>[ctct]</code>' ); ?></strong></span>
				<input type="text" id="wpcf7-ctct-accept" name="wpcf7-ctct[accept]" placeholder="Example: [checkbox-456]" class="wide" size="70" value="<?php echo esc_attr( isset($cf7_ctct['accept']) ? $cf7_ctct['accept'] : '' ); ?>" />
				<span class="howto"><?php _e('If the user should check a box to be added to the lists, enter the checkbox field here. Leave blank to have no opt-in field.', 'ctctcf7'); ?></span>
			</label>
		</div>

	</div>
	<div class="clear"></div>
</div>