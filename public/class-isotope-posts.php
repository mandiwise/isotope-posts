<?php
/**
 * Isotope Posts.
 *
 * @package   Isotope_Posts
 * @author    Mandi Wise <hello@mandiwise.com>
 * @license   GPL-2.0+
 * @link      http://mandiwise.com
 * @copyright 2014 Mandi Wise
 */

class Isotope_Posts {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   2.0.0
	 *
	 * @var     string
	 */
	const VERSION = '2.1';

	/**
	 * Unique identifier for the plugin.
	 *
	 * @since    2.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'isotope-posts';

	/**
	 * Instance of this class.
	 *
	 * @since    2.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     2.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Load public-facing stylesheet.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

      // Initiate the plugin settings class so we can use what's saved in those options.
      require_once( ISO_DIR . '/admin/views/settings.php' );
      $Isotope_Posts_Settings = new Isotope_Posts_Settings();

		// Register the shortcode
		add_action( 'init', array( $this, 'register_shortcode') );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    2.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
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
	 * Fired when the plugin is activated.
	 *
	 * @since    2.0.0
	 *
	 * @param    boolean    $network_wide
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    2.0.0
	 *
	 * @param    boolean    $network_wide
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_deactivate();
				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    2.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    2.0.0
	 */
	private static function single_activate() {
		// Option needs to be initially added here to fix a bug that should be patched in WP 4.0
		add_option( 'isotope_options' );
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    2.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    2.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles() {

		wp_register_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );

		global $post;

		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'isotope-posts') ) {
			wp_enqueue_style( $this->plugin_slug . '-plugin-styles' );
		}
	}

	/**
	 * Create the Isotope loop of posts and enqueue the scripts.
	 *
	 * @since    2.0.0
	 */
	public function isotope_loop( $atts ) {

      extract( shortcode_atts( array(
         'id' => '',
			'load_css' => 'false',
      ), $atts ) );

      /*
       * Grab the stored options.
       */

      // Get Isotope opptions by loop id.
      $loop_id = isotope_option( $id );
		$shortcode_id = $loop_id['shortcode_id'];

      // Set the post type to display with Isotope.
      $post_type = $loop_id['post_type'];

      // Set the taxonomy and terms to limit what posts are displayed, if desired.
		$limit_posts = $loop_id['limit_posts'];
      $limit_by = !empty( $loop_id['limit_by'] ) ? $loop_id['limit_by'] : null;
      $limit_term = !empty( $loop_id['limit_term'] ) ? $loop_id['limit_term'] : null;

      // Set the filter menu options.
		$filter_menu = $loop_id['filter_menu'];
      $filter_by = !empty( $loop_id['filter_by'] ) ? $loop_id['filter_by'] : null;

      // Set pagination options for the post loop.
      $pagination = $loop_id['pagination'];
      $posts_per_page = ( $pagination == 'yes' && $loop_id['posts_per_page'] != 0 ) ? absint( $loop_id['posts_per_page'] ) : -1;
      $finished_message = !empty( $loop_id['finished_message'] ) ? $loop_id['finished_message'] : '';

      // Set layout options for the posts.
		$layout = $loop_id['layout'];
		$sort_by = $loop_id['sort_by'];
      $order = ( $sort_by == 'date' ) ? 'DESC' : 'ASC';

      // Get the current page url.
      $page_url = get_permalink();

      // Enqueue and localize the Isotope styles and scripts
		if ( $load_css == 'true' ) {
			wp_enqueue_style( $this->plugin_slug . '-plugin-styles' );
		}

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( $this->plugin_slug . '-isotope-script', plugins_url( 'assets/js/isotope.pkgd.min.js', __FILE__ ), array(), '2.0.0' );
		wp_enqueue_script( $this->plugin_slug . '-imagesloaded-script', plugins_url( 'assets/js/imagesloaded.pkgd.min.js', __FILE__ ), array( 'jquery' ), '3.1.8' );
		wp_enqueue_script( $this->plugin_slug . '-infinitescroll-script', plugins_url( 'assets/js/jquery.infinitescroll.min.js', __FILE__ ), array( 'jquery' ), '2.0.2' );
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );

		wp_localize_script( $this->plugin_slug . '-plugin-script', 'iso_vars', array(
            'loader_gif' => plugins_url( 'public/assets/images/ajax-loader.gif' , dirname(__FILE__) ),
            'finished_message' => $finished_message,
            'page_url' => $page_url,
            'iso_paginate' => $pagination,
				'iso_layout' => $layout,
			)
		);

      /*
       * Set the WP query args for the post loop.
       */

      if ( get_query_var( 'paged' ) ) {
         $paged = get_query_var( 'paged' );
      } elseif ( get_query_var( 'page' ) ) {
         $paged = get_query_var( 'page' );
      } else {
         $paged = 1;
      }

      // Set the post type and order args.
		$args = array(
			'post_type' => $post_type,
         'paged' => $paged,
         'posts_per_page' => $posts_per_page,
         'orderby' => $sort_by,
         'order' => $order,
		);

      // Set the limiting taxonomy args.
		if ( $limit_posts == 'yes' && taxonomy_exists( $limit_by ) ) {
			$limited_terms = explode( ',', $limit_term );
			$args['tax_query'] = array(
				array (
					'taxonomy' => $limit_by,
					'field' => 'slug',
					'terms' => $limited_terms,
					'operator' => 'NOT IN',
				)
			);
		}

		$isoposts = new WP_Query( $args );

      /*
       * Now, generate the loop output.
       */
      ob_start();

		// Create the filter menu if option selected.
		if ( $filter_menu == 'yes' && taxonomy_exists( $filter_by ) ) {

			// If the menu taxonomy is the same as the limiting taxonomy, the convert the limited term slugs into IDs.
			if ( $filter_by == $limit_by ) {
				$limited_terms = explode( ',', $limit_term );
				$excluded_ids = array();

            foreach( $limited_terms as $term ) {
					$term_id = get_term_by( 'slug', $term, $limit_by )->term_id;
					$excluded_ids[] = $term_id;
				}
				$id_string = implode( ',', $excluded_ids );

			} else {
				$id_string = '';
			}

			// Display the menu if there are terms to display.
			$terms = get_terms( $filter_by, array( 'exclude' => $id_string ) );
			$count = count( $terms );

         if ( $count > 0 ) {
				echo '<ul id="filters">';
				echo '<li><a href="#" data-filter="*">' . __('See All', 'isotope-posts-locale') . '</a></li>';

            foreach ( $terms as $term ) {
					echo '<li><a href="#'. $term->slug .'" data-filter=".'. $term->slug .'">' . $term->name . '</a></li>';
				}
				echo '</ul>';
			}

		} // end if filter_menu

		// Start the post loop if the post type exists.
		if ( post_type_exists( $post_type ) && $isoposts->have_posts() ) : ?>

         <div class="iso-container">
   			<ul id="iso-loop">
   			<?php while ( $isoposts->have_posts() ) : $isoposts->the_post(); ?>
   				<li class="<?php if ( $filter_menu == 'yes' && taxonomy_exists( $filter_by ) ) {
						$terms = get_the_terms( $isoposts->post->ID, $filter_by );
						if ( ! empty( $terms ) ) {
	                  foreach( $terms as $term ) {
	                     echo $term->slug.' ';
	                  }
						}
               } ?>iso-post">
						<?php
							do_action( "before_isotope_title" );
							do_action( "before_isotope_title_{$shortcode_id}" );
						?>
   					<h2 class="iso-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
						<?php
							do_action( "before_isotope_content" );
							do_action( "before_isotope_content_{$shortcode_id}" );
						?>
   					<?php
   						if ( '' != get_the_post_thumbnail() ) { ?>
   							<div class="iso-thumb">
   								<a href="<?php the_permalink() ?>"><?php the_post_thumbnail(); ?></a>
   							</div>
   						<?php }
   					?>
   					<?php the_excerpt(); ?>
						<?php
							do_action( "after_isotope_content" );
							do_action( "after_isotope_content_{$shortcode_id}" );
						?>
   				</li>
   			<?php endwhile; ?>
   			</ul>
         </div>

         <div class="iso-posts-loading"></div>
         <nav role="navigation" class="iso-pagination">
            <span class="more-iso-posts"><?php echo get_next_posts_link( 'More Posts', $isoposts->max_num_pages ); ?></span>
         </nav>

		<?php
         // Reset the post loop.
			wp_reset_postdata();

			$content = ob_get_contents();
			ob_end_clean();
			return $content;

		else : ?>
			<p>Nothing found. Please check back soon!</p>

		<?php endif; // end post loop

	}

   /**
    * Register the shortcode "[isotope-posts]".
    *
    * @since    2.0.0
    */
	public function register_shortcode() {
		add_shortcode( 'isotope-posts', array( $this, 'isotope_loop' ) );
	}

}
