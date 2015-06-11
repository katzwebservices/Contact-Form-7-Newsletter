<div class="control-box" style="height: 100%">
	<?php

	if(!self::validateApi()) {
		?>
		<div id="wpcf7-tg-pane-ctct">
			<form action="">
				<div class="error inline"><p><?php _e(sprintf('The plugin\'s Constant Contact settings are not configured properly. <a href="%s">Go configure them now.', admin_url('admin.php?page=ctct_cf7')), 'ctctcf7'); ?></a></p></div>
			</form>
		</div>
		<?php
		return;
	} ?>
	<div id="wpcf7-tg-pane-ctct">
		<form action="#">
			<style>
				#contact-form-editor-tabs #ctctcf7-tab a {
					background: url('<?php echo plugins_url('favicon.png',__FILE__); ?>') 5px center no-repeat;
					padding-left: 25px;
				}
				.js .postbox .tg-pane h3 {
					cursor: default;
				}
				#wpcf7-tg-pane-ctct h3 small {
					padding-right: .5em;
				}
				.ctctcf7_subscribe_list ul {
					-webkit-columns:15em;
					-moz-columns:15em;
					columns:15em;
					overflow:auto;
				}
			</style>

			<h4><?php _e('After inserting this Tag, you will need to define the Integration Fields in the "Constant Contact" tab.', "wpcf7" ); ?></h4>
			<div>
				<input type="hidden" name="name" class="tg-name" value="" />
				<input class="option" name="type" type="hidden" />
			</div>
			<h3><?php _e('How should users subscribe?', 'ctctcf7'); ?></h3>
			<ul>
				<li><label><input id="subscribe_type_hidden" type="radio" value="hidden" name="subscribe_type_radio" class="option" onchange="jQuery(document).trigger('ctct_change_type');" /> <?php _e('Hidden (subscribe without asking)', 'ctctcf7'); ?></label></li>
				<li><label><input id="subscribe_type_single" type="radio" value="single" name="subscribe_type_radio" class="option" onchange="jQuery(document).trigger('ctct_change_type');" /> <?php _e('Opt-in checkbox', 'ctctcf7'); ?></label></li>
				<li><label><input id="subscribe_type_dropdown" type="radio" value="dropdown" name="subscribe_type_radio" class="option" onchange="jQuery(document).trigger('ctct_change_type');" /> <?php _e('Drop-down select lists', 'ctctcf7'); ?></label></li>
				<li><label><input id="subscribe_type_checkboxes" type="radio" value="checkboxes" name="subscribe_type_radio" class="option" onchange="jQuery(document).trigger('ctct_change_type');" /> <?php _e('Checkboxes select lists', 'ctctcf7'); ?></label></li>
			</ul>

			<div class="subscribe_type_hidden" style="display:none;">
				<h3><?php _e('Sign users up for the following lists:', 'ctctcf7'); echo $this->get_refresh_lists_link(); ?></h3>
			</div>

			<div class="subscribe_type_checkboxes" style="display:none;">
				<h3><?php _e('Allow users to pick from the following lists:', 'ctctcf7'); echo $this->get_refresh_lists_link(); ?></h3>
			</div>

			<div class="subscribe_type_single" style="display:none;">
				<h3><label for="ctctcf7_single_checkbox_label_editor"><?php _e('Label for the opt-in checkbox', 'ctctcf7'); ?></label></h3>
				<p><input type="text" data-default="<?php _e('Sign me up for your newsletter', 'ctctcf7'); ?>" id="ctctcf7_single_checkbox_label_editor" value="" class="widefat urlencode" data-target="#ctctcf7_single_checkbox_label" /></p>
				<input class="option" id="ctctcf7_single_checkbox_label" name="label" type="hidden" />
				<h3><?php _e('When users opt-in, sign them up for the following lists:', 'ctctcf7'); echo $this->get_refresh_lists_link(); ?></h3>
			</div>

			<div class="subscribe_type_dropdown" style="display:none;">
				<h3><label for=""><?php _e('First option in the dropdown', 'ctctcf7'); ?></label></h3>
				<p><input type="text" data-default="<?php _e('Select a newsletter', 'ctctcf7'); ?>" id="" value="" class="widefat urlencode" data-target="#ctctcf7_dropdown_first_option" /></p>
				<input class="option" id="ctctcf7_dropdown_first_option" name="first" type="hidden" />
				<h3><?php _e('Include the following lists in the dropdown:', 'ctctcf7'); echo $this->get_refresh_lists_link(); ?></h3>
			</div>

			<div class="ctctcf7_subscribe_list" style="display:none;">
				<ul class="clear">
					<?php
					$lists = CTCT_SuperClass::getAvailableLists();

					foreach($lists as $list) {
						echo '
						<li>
							<label>
								<input type="checkbox" class="option" name="\''.str_replace("'", '&amp;#39;', $list['name']).'::#'.$list['id'].'\'" value="'.$list['link'].'" '.@checked((is_array($list) && in_array($list['link'], (array)$cf7_ctct['lists'])), true, false).' />
							'.$list['name'].'
							</label>
						</li>';
					}
					?>
				</ul>
				<div class="clear"></div>
			</div>
			<div id="ctctcf7-tg-tags" style="display:none;">
				<h4 class="description"><?php _e('Select list(s) above to generate the form code.', 'ctctcf7'); ?></h4>
			</div>
		</form>

		<div class="insert-box hidden" style="height: 150px; padding-left: 15px; padding-right: 15px;">
			<div class="tg-tag clear"><?php echo __( "Insert this tag into the Form. There should only be one of these tags per form.", 'ctctcf7' ); ?><br /><input type="text" name="ctct" class="tag code" readonly="readonly" onfocus="this.select()" /></div>

			<div class="tg-mail-tag clear"><?php echo esc_html( __( "and add this code to the Mail tab.", 'ctctcf7' ) ); ?><br /><span class="arrow">&#11015;</span>&nbsp;<input type="text" class="mail-tag code" readonly="readonly" onfocus="this.select()" /></div>

			<div class="submitbox">
				<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
			</div>
		</div>
	</div>
</div>