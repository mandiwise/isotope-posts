<?php
/*
Plugin Name: Isotope Posts
Plugin URI: http://mandiwise.com/wordpress/isotope-posts/
Description: This plugin allows you to use Metafizzy's Isotope jQuery plugin to display a feed of WordPress posts with a simple shortcode. Works with custom post types and custom taxonomies too.
Version: 1.0
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
		require_once(sprintf("%s/views/admin.php", dirname(__FILE__)));
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
	public function register_admin_scripts() {
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
	
	// - create the isotope shortcode, enqueues the scripts, and queries the db for posts -
	function isotope_loop() {
		
		// - grab the plugin's saved settings -
		$posttype = isotope_option( 'post_type' );
		$cptslug = isotope_option( 'cpt_slug' );
		$menu = isotope_option( 'filter_menu' );
		$filterby = isotope_option( 'filter_by');
		$taxslug = isotope_option( 'tax_slug' );
		$layout = isotope_option( 'layout' );
		$sortby = isotope_option( 'sort_by' );
		
		if ( $posttype == 'post' ) {
			$type = 'post';
		} else {
			$type = $cptslug; 
		}
		
		if ( $filterby == 'category' ) {
			$meta = 'category';
		} elseif ( $filterby == 'post_tag' ) {
			$meta = 'post_tag';
		} else {
			$meta = $taxslug; 
		}
		
		wp_enqueue_script( 'jquery-isotope-script' );
		wp_enqueue_script( 'isotope-posts-plugin-script' );
		wp_localize_script( 'isotope-posts-plugin-script', 'iso_vars', array(
				'iso_layout' => $layout,
				'iso_sortby' => $sortby
			) 
		);
		
		$args = array(
			'post_type' => $type,
			'posts_per_page=' => '-1'
		);
		$isoposts = new WP_Query( $args );

		ob_start();

		// - create the filter menu option is selected -
		if ( $menu == 'yes' ) {			
			$terms = get_terms( $meta );
			$count = count( $terms );
				if ( $count > 0 ){
					echo '<ul id="filters">';
					echo '<li><a href="#" data-filter="*">' . __('See All', 'isotope-posts-locale') . '</a></li>';
					foreach ( $terms as $term ) {
					echo '<li><a href="#" data-filter=".'. $term->slug .'">' . $term->name . '</a></li>';
				}
				echo '</ul>';
				echo '<div style="clear:both;"></div>';
			}
		}	
		?>
			
		<ul id="iso-loop">
		<?php while ($isoposts->have_posts()) : $isoposts->the_post(); ?>
			<li class="<?php foreach( get_the_terms( $isoposts->post->ID, $meta ) as $term ) echo $term->slug.' '; ?>iso-post">
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

	}
	
	// - register the shortcode "[isotope-posts]" -
	function register_shortcode() {
		add_shortcode( 'isotope-posts', array( $this, 'isotope_loop' ) );
	}

} // - end class -

$plugin_name = new IsotopePosts();
