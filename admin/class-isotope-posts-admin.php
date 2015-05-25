<?php
/**
 * Isotope Posts.
 *
 * @package   Isotope_Posts_Admin
 * @author    Mandi Wise <hello@mandiwise.com>
 * @license   GPL-2.0+
 * @link      http://mandiwise.com
 * @copyright 2014 Mandi Wise
 */

class Isotope_Posts_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    2.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    2.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a settings page and menu.
	 *
	 * @since     2.0.0
	 */
	private function __construct() {

		// Call $plugin_slug from public plugin class.
		$plugin = Isotope_Posts::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

      // Add Ajax callbacks for editing and deleting shortcodes.
      add_action( 'wp_ajax_delete_post_loop', array( $this, 'delete_post_loop' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     2.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     2.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
         wp_enqueue_style( 'thickbox' );
			wp_enqueue_style( $this->plugin_slug . '-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Isotope_Posts::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     2.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
         wp_enqueue_script( 'thickbox' );
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Isotope_Posts::VERSION );
         wp_localize_script( $this->plugin_slug . '-admin-script', 'isotope_data', array( 'ajax_nonce' => wp_create_nonce( 'ajax_nonce' ), )
        );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    2.0.0
	 */
	public function add_plugin_admin_menu() {

		// Add a settings page for this plugin to the Settings menu.
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Isotope Posts', $this->plugin_slug ),
			__( 'Isotope Posts', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    2.0.0
	 */
	public function display_plugin_admin_page() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Sorry! You don\'t have sufficient permissions to access this page.', $this->plugin_slug ) );
		}

		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    2.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

   /**
    * Handle request to delete previously saved shortcode options using Ajax.
    *
    * @since    2.0.0
    */
   public function delete_post_loop() {

      // Verify our nonce
      if ( ! ( isset( $_POST['iso_ajax_nonce'] ) && wp_verify_nonce( $_POST['iso_ajax_nonce'], 'ajax_nonce' ) ) )
         die();

      $loop_id = isset( $_POST['loop_id'] ) ? $_POST['loop_id'] : null;
      $isotope_loops = ( get_option('isotope_options') != false ) ? get_option( 'isotope_options' ) : array();

      // Check for the loop ID in the plugin options (deletion occurs in validation callback)
      if ( $loop_id && $isotope_loops && array_key_exists( $loop_id, $isotope_loops ) ) {
         update_option( 'isotope_options', $isotope_loops );
			_e( 'Success! This shortcode has been deleted.', $this->plugin_slug );
   	}
   	die();

   }

}
