<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Isotope_Posts
 * @author    Mandi Wise <hello@mandiwise.com>
 * @license   GPL-2.0+
 * @link      http://mandiwise.com
 * @copyright 2014 Mandi Wise
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( is_multisite() ) {
	global $wpdb;
	$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
	delete_option( 'isotope_options' );

   if ( $blogs ) {
		foreach( $blogs as $blog ) {
			switch_to_blog( $blog['blog_id'] );
			delete_option( 'isotope_options' );
			restore_current_blog();
		}
	}

} else {
	delete_option( 'isotope_options' );
}
