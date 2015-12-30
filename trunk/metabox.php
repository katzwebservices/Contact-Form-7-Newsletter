<?php
/**
 * @global object $args
 */

$CTCT_SuperClass = new CTCT_SuperClass;
$cf7_ctct_defaults = array();

$cf_id = method_exists( $args , 'id' ) ? $args->id() : $args->id;

$cf7_ctct = get_option( 'cf7_ctct_'. $cf_id, $cf7_ctct_defaults );

?>
<a href="http://katz.si/4w" target="_blank"><img src="<?php echo plugins_url('CTCT_horizontal_logo.png', __FILE__); ?>" width="281" height="47" alt="Constant Contact Logo" style="margin-top:.5em;" /></a>

<?php if( self::validateApi() ) { ?>
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

		$instructions = '<h3>'.esc_html__('Integration Fields', 'ctctcf7').'</h3>';
		$instructions .= '<p class="howto">';
		$instructions .= esc_html__('For each of the Integration Fields below, select the value you would like sent to Constant Contact.', 'ctctcf7');
		$instructions .= '</p>';

		echo $instructions;
		?>

		<?php
		$i = 0;
		foreach( $CTCT_SuperClass->listMergeVars() as $var ) {
			$tag = isset( $var['tag'] ) ? $var['tag'] : '';
			?>
			<div class="half-<?php if ( $i % 2 === 0 ) {
				echo 'left';
			} else {
				echo 'right';
			} ?>" style="clear:none;">
				<div class="mail-field">
					<label for="wpcf7-ctct-<?php echo esc_attr( $tag ); ?>"><?php echo esc_html( $var['name'] );
						echo ! empty( $var['req'] ) ? ' <strong style="color: #a00;">'. __( '&larr; This setting is required.', 'ctctcf7' ) .'</strong>' : ''; ?></label><br/>
					<input type="text" id="wpcf7-ctct-<?php echo esc_attr( $tag ); ?>"
					       name="wpcf7-ctct[fields][<?php echo esc_attr( $tag ); ?>]"
					       class="wide widefat" size="70"
					       value="<?php
					       $value = isset( $cf7_ctct['fields']["{$tag}"] ) ? $cf7_ctct['fields']["{$tag}"] : '';
					       echo esc_attr( $value ); ?>" <?php if ( isset( $var['placeholder'] ) ) {
						echo ' placeholder="Example: ' . esc_attr( $var['placeholder'] ) . '"';
					} ?> />
				</div>
			</div>

			<?php
			if ( $i % 2 === 1 ) {
				echo '<div class="clear"></div>';
			}
			$i ++;
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