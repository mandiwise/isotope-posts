<?php
/**
 * Isotope Posts.
 *
 * @package   Isotope Posts
 * @author    Mandi Wise <hello@mandiwise.com>
 * @license   GPL-2.0+
 * @link      http://mandiwise.com
 * @copyright 2014 Mandi Wise
 *
 * @wordpress-plugin
 * Plugin Name:       Isotope Posts
 * Plugin URI:        http://mandiwise.com/wordpress/isotope-posts/
 * Description:       Allows you to use Metafizzy's Isotope to display feeds of WordPress posts using simple shortcodes. Works with custom post types and custom taxonomies too.
 * Version:           2.1
 * Author:            Mandi Wise
 * Author URI:        http://mandiwise.com
 * Text Domain:       isotope-posts
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/mandiwise/isotope-posts
 *
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define the plugin directory
define( 'ISO_DIR', dirname( __FILE__ ) );

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( ISO_DIR . '/public/class-isotope-posts.php' );

register_activation_hook( __FILE__, array( 'Isotope_Posts', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Isotope_Posts', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Isotope_Posts', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() ) {

	require_once( ISO_DIR . '/admin/class-isotope-posts-admin.php' );
	add_action( 'plugins_loaded', array( 'Isotope_Posts_Admin', 'get_instance' ) );

}
