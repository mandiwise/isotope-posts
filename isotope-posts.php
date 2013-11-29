<?php
/*
Plugin Name: Isotope Posts
Plugin URI: http://mandiwise.com/wordpress/isotope-posts/
Description: This plugin allows you to use Metafizzy's Isotope jQuery plugin to display a feed of WordPress posts with a simple shortcode. Works with custom post types and custom taxonomies too.
Version: 1.1.1
Author: Mandi Wise
Author URI: http://mandiwise.com/
License: GPLv2 or later + MIT
License URI: http://www.gnu.org/licenses/gpl-2.0.html

*/

class IsotopePosts {

	// * Constructor *
		// - initializes the plugin by setting localization, filters, and administration functions -
		
	function __construct() {

		// - load plugin text domain -
		add_action( 'init', array( $this, 'plugin_textdomain' ) );

		// - register admin scripts -
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		// - register site styles and scripts -
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );

		// - add the settings sub-menu -
		require_once( sprintf( '%s/views/admin.php', dirname(__FILE__) ) );
		$Isotope_Settings = new Isotope_Settings();
		
		// - register the shortcode -
		add_action( 'init', array( $this, 'register_shortcode') );

	} // - end constructor -

	// * Loads the plugin text domain for translation * 
	public function plugin_textdomain() {
		$domain = 'isotope-posts-locale';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
        load_textdomain( $domain, WP_LANG_DIR.'/'.$domain.'/'.$domain.'-'.$locale.'.mo' );
        load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	// * Registers and enqueues admin-specific javascript *
	public function register_admin_scripts( $hook ) {
		global $isotope_posts_settings_page;
		if ( $hook != $isotope_posts_settings_page ) 
			return;
			
		wp_enqueue_script( 'isotope-posts-admin-script', plugins_url( 'isotope-posts/js/admin.js' ), array('jquery') );
	}

	//* Registers and enqueues plugin-specific styles 
	public function register_plugin_styles() {
		wp_enqueue_style( 'isotope-posts-plugin-styles', plugins_url( 'isotope-posts/css/display.css' ) );
	}

	// * Registers and enqueues plugin-specific scripts *
	public function register_plugin_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_register_script( 'jquery-isotope-script', plugins_url( 'isotope-posts/js/jquery.isotope.min.js' ), array('jquery') );
		wp_register_script( 'isotope-posts-plugin-script', plugins_url( 'isotope-posts/js/display.js' ), array('jquery') );
	}

	// * Core Functions *
	
	// - create the Isotope shortcode, enqueues the scripts, and queries the db for posts -
	public function isotope_loop() {
		
		// - set the post type to display with Isotope -
		$post_type = isotope_option( 'post_type' );
		$cpt_slug = isotope_option( 'cpt_slug' );
		
		if ( $post_type == 'post' )
			$post_type = 'post';
		else
			$post_type = $cpt_slug;
		
		// - store the taxonomy and terms to limit what posts are displayed, if desired -
		$limit_posts = isotope_option( 'limit_posts' );
		$limit_by = isotope_option( 'limit_by');
		
		if ( $limit_by == 'category' )
			$limit_tax = 'category';
		elseif ( $limit_by == 'post_tag' )
			$limit_tax = 'post_tag';
		elseif ( $limit_by == 'cust_tax' )
			$limit_tax = isotope_option( 'limit_tax' );
		
		$limit_term = isotope_option( 'limit_term' );
		
		// - store the filter menu options -
		$filter_menu = isotope_option( 'filter_menu' );
		$filter_by = isotope_option( 'filter_by');
		$filter_tax = isotope_option( 'filter_tax' );
		
		// - set the taxonomy for the filter menu -
		if ( $filter_by == 'category' )
			$menu_tax = 'category';
		elseif ( $filter_by == 'post_tag' )
			$menu_tax = 'post_tag';
		elseif ( $filter_by == 'cust_tax' )
			$menu_tax = $filter_tax; 
		
		// - set layout options for the posts -
		$layout = isotope_option( 'layout' );
		$sort_by = isotope_option( 'sort_by' );
		
		// - enqueue and localize the Isotope scripts -
		wp_enqueue_script( 'jquery-isotope-script' );
		wp_enqueue_script( 'isotope-posts-plugin-script' );
		wp_localize_script( 'isotope-posts-plugin-script', 'iso_vars', array(
				'iso_layout' => $layout,
				'iso_sortby' => $sort_by
			) 
		);
		
		// - set the WP query args -
		$args = array(
			'post_type' => $post_type,
			'posts_per_page' => '-1'
		);
		
		if ( $limit_posts == 'yes' && taxonomy_exists( $limit_tax ) ) {
			$limited_terms = explode( ',', $limit_term );
			$args['tax_query'] = array(
				array (
					'taxonomy' => $limit_tax,
					'field' => 'slug',
					'terms' => $limited_terms,
					'operator' => 'NOT IN'
				)
			);
		}
		
		$isoposts = new WP_Query( $args );

		ob_start();

		// - create the filter menu if option selected -
		if ( $filter_menu == 'yes' && taxonomy_exists( $menu_tax ) ) {			
			
			// - if the menu taxonomy is the same as the limiting taxonomy, the convert the limited term slugs into IDs for use with "get_terms" -
			if ( $menu_tax == $limit_tax ) {
				global $wpdb;
				$limited_terms = explode( ',', $limit_term );
				$excluded_ids = array();
				foreach( $limited_terms as $limitedterm ) {
					$term_id = $wpdb->get_var( "SELECT term_id FROM $wpdb->terms WHERE slug='$limitedterm'" );
					$excluded_ids[] = $term_id;
				}
				$id_string = implode(' ', $excluded_ids );
			} else {
				$id_string = '';
			}
			
			// - display the menu if there are terms to display -
			$terms = get_terms( $menu_tax, array( 'exclude' => $id_string ) );
			$count = count( $terms );
			if ( $count > 0 ) {
				echo '<ul id="filters">';
				echo '<li><a href="#" data-filter="*">' . __('See All', 'isotope-posts-locale') . '</a></li>';
				foreach ( $terms as $term ) {
					echo '<li><a href="#" data-filter=".'. $term->slug .'">' . $term->name . '</a></li>';
				}
				echo '</ul>';
				echo '<div style="clear:both;"></div>';
			}
		}
		
		// - start the post loop if the post type exists -
		if ( post_type_exists( $post_type ) && $isoposts->have_posts() ) : ?>
			<ul id="iso-loop">
			<?php while ($isoposts->have_posts()) : $isoposts->the_post(); ?>
				<li class="<?php foreach( get_the_terms( $isoposts->post->ID, $menu_tax ) as $term ) echo $term->slug.' '; ?>iso-post">
					<h2 class="iso-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
					<?php
						if ( '' != get_the_post_thumbnail() ) { ?>
							<div class="iso-thumb">
								<a href="<?php the_permalink() ?>"><?php the_post_thumbnail(); ?></a>
							</div>
						<?php }
					?>
					<?php the_excerpt(); ?>
				</li>
			<?php endwhile; ?>
			</ul>
		<?php
		
			wp_reset_postdata();
		
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		
		else: ?>
			<p>Nothing found. Please check back soon!</p>
		<?php endif;

	}
	
	// - register the shortcode "[isotope-posts]" -
	public function register_shortcode() {
		add_shortcode( 'isotope-posts', array( $this, 'isotope_loop' ) );
	}

} // - end class -

$plugin_name = new IsotopePosts();