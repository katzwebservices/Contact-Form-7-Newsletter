<?php

if(!function_exists('kws_show_rating_box')) {

/**
 * Print a rating box that shows the star ratings and an upgrade message if a newer version of the plugin is available.
 *
 * Plugin data is fetched using the `plugins_api()` function, then cached for 2 hours as a transient using the `{$slug}_plugin_info` key.
 *
 * @uses plugins_api() Get the plugin data
 * @param  string $name    The display name of the plugin.
 * @param  string $slug    The WP.org directory repo slug of the plugin
 * @param  string|float|integer $version The version number of the plugin
 * @version 1.0
 */
function kws_show_rating_box($name = '', $slug = '', $version) {
	global $wp_version;

?>
	<div class="<?php echo $slug; ?>-ratingbox alignright" style="padding:9px 0; max-width:400px;">
	<?php
		// Display plugin ratings

		require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

		// Get the cached data
		$api = get_transient( $slug.'_plugin_info' );

		// The cache data doesn't exist or it's expired.
		if (empty($api)) {

			$api = plugins_api( 'plugin_information', array( 'slug' => $slug ) );

			if ( !is_wp_error( $api ) ) {
				// Cache for 2 hours
				set_transient( $slug.'_plugin_info', $api, 60 * 60 * 2 );
			}
		}

		if ( !is_wp_error( $api ) ) {

			if ( !empty( $api->rating ) ) { ?>
			<p><a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/<?php echo $slug; ?>?rate=5#postform" class="button button-secondary"><?php _e( 'Rate this Plugin', 'wpinterspire' ) ?></a> <strong><?php _e( '&larr; Help spread the word!', 'wpinterspire'); ?></strong></p>
				<?php
				if ( !empty( $api->downloaded ) ) {
					echo sprintf( __( 'Downloaded %s times.', 'wpinterspire' ), number_format_i18n( $api->downloaded ) );
				} ?>
				<div class="star-holder" title="<?php echo esc_attr( sprintf( __( '(Average rating based on %s ratings)', 'wpinterspire' ), number_format_i18n( $api->num_ratings ) ) ); ?>">
					<div class="star-rating" style="width: <?php echo esc_attr( $api->rating ) ?>px"></div>
				</div>
				<div><small style="display:block;"><?php
					echo sprintf( __( 'Average rating based on %s ratings.', 'wpinterspire' ), number_format_i18n( $api->num_ratings ) ); ?></small></div>
				<?php
			}
		} // if ( !is_wp_error($api)

		if ( isset( $api->version ) ) {
			if (
				// A newer version is available
				version_compare( $api->version, $version, '>' ) &&

				// And the current version of WordPress supports it.
				version_compare( $api->requires, $wp_version, '<=')
			) {

				$message = sprintf(__( '%sA newer version of %s is available: %s.%s', 'wpinterspire' ), '<a class="thickbox" title="Update '.esc_html( $name ).'" href="'.admin_url('plugin-install.php?tab=plugin-information&plugin='.$slug.'&section=changelog&TB_iframe=true&width=640&height=808').'">', esc_html($name), $api->version, '</a>');

				// Don't use make_notice_box so can be reused in other plugins.
				echo '<div id="message" class="updated">'.wpautop($message).'</div>';

			}
			// There's a newer version available, but the current WP install doesn't support it.
			elseif(version_compare( $api->requires, $wp_version, '>')) {
				echo '<div id="message" class="updated">';
				echo wpautop(sprintf(__('There is a newer version of %s available, but your current version of WordPress does not support it.

					%sUpdate WordPress%s', 'wpinterspire'), $name, '<a class="button button-secondary" href="'.admin_url( 'update-core.php' ).'">', '</a>'));
				echo '</div>';
			}
			else {
				echo wpautop(sprintf( __( 'Version %s (up to date)', 'si-contact-form' ), $version ));
			}
		}   ?>
	</div>
	<?php
}

} // Function exists check
