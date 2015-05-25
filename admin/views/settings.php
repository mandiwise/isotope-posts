<?php

/**
 * Isotope Posts.
 *
 * @package   Isotope_Posts_Settings
 * @author    Mandi Wise <hello@mandiwise.com>
 * @license   GPL-2.0+
 * @link      http://mandiwise.com
 * @copyright 2014 Mandi Wise
 */

if ( !class_exists( 'Isotope_Posts_Settings' ) ) {

	class Isotope_Posts_Settings {

		// Array of sections for the plugin
		private $sections;
		private $checkboxes;
		private $settings;

		// The plugin text domain (unfortunately has to be redefined here...)
		protected $plugin_slug = 'isotope-posts';

		// Construct the plugin settings object
		public function __construct() {

			$this->checkboxes = array();
			$this->settings = array();
			$this->get_settings();

			// Create the settings sections
			$this->sections['loop_options'] = __( 'Isotope Display Options', $this->plugin_slug );

			// Register the settings
			add_action( 'admin_init', array( $this, 'admin_init' ) );

		}

		// Create the settings fields
		public function create_setting( $args = array() ) {

			$defaults = array(
				'id'      => 'default_field',
				'title'   => __( 'Default Field' ),
				'desc'    => '',
				'std'     => '',
				'type'    => 'text',
				'section' => 'loop_options',
				'choices' => array(),
				'class'   => ''
			);

			extract( wp_parse_args( $args, $defaults ) );

			$field_args = array(
				'type'      => $type,
				'id'        => $id,
				'desc'      => $desc,
				'std'       => $std,
				'choices'   => $choices,
				'label_for' => $id,
				'class'     => $class
			);

			if ( $type == 'checkbox' ) {
				$this->checkboxes[] = $id;
			}

			add_settings_field( $id, $title, array( $this, 'display_setting' ), 'isotope-options', $section, $field_args );
		}

		// Create the HTML output for each possible type of setting
		public function display_setting( $args = array() ) {

			extract( $args );
			$options = get_option( 'isotope_options' );

			if ( ! isset( $options[$id] ) && $type != 'checkbox' ) {
				$options[$id] = $std;
			} elseif ( ! isset( $options[$id] ) ) {
				$options[$id] = 0;
			}

			$field_class = '';
			if ( $class != '' ) {
				$field_class = ' ' . $class;
			}

			switch ( $type ) {

				case 'checkbox':
					echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="isotope_options[' . $id . ']" value="1" ' . checked( $options[$id], 1, false ) . ' /> <label for="' . $id . '">' . $desc . '</label>';
					break;

				case 'select':
					echo '<select class="select' . $field_class . '" name="isotope_options[' . $id . ']">';
					foreach ( $choices as $value => $label )
						echo '<option value="' . esc_attr( $value ) . '"' . selected( $options[$id], $value, false ) . '>' . $label . '</option>';
					echo '</select>';

					if ( $desc != '' ) {
						echo '<p class="description">' . $desc . '</p>';
					}
					break;

				case 'radio':
					$i = 0;
					foreach ( $choices as $value => $label ) {
						echo '<input class="radio' . $field_class . '" type="radio" name="isotope_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr( $value ) . '" ' . checked( $options[$id], $value, false ) . '> <label for="' . $id . $i . '">' . $label . '</label>';
						if ( $i < count( $options ) - 1 ) {
							echo '<br />';
						}
						$i++;
					}

					if ( $desc != '' ) {
						echo '<p class="description">' . $desc . '</p>';
					}
					break;

				case 'textarea':
					echo '<textarea class="' . $field_class . '" id="' . $id . '" name="isotope_options[' . $id . ']" placeholder="' . $std . '" rows="5" cols="30">' . wp_htmledit_pre( $options[$id] ) . '</textarea>';

					if ( $desc != '' ) {
						echo '<p class="description">' . $desc . '</p>';
					}
					break;

				case 'password':
					echo '<input class="regular-text' . $field_class . '" type="password" id="' . $id . '" name="isotope_options[' . $id . ']" value="' . esc_attr( $options[$id] ) . '" />';

					if ( $desc != '' ) {
						echo '<p class="description">' . $desc . '</p>';
					}
					break;

				case 'text':
				default:
					echo '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="isotope_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" />';

					if ( $desc != '' ) {
						echo '<p class="description">' . $desc . '</p>';
					}
					break;
			}

		}

		// Define all settings for this plugin and their defaults
		public function get_settings() {

			/*
			 * Build some helpful arrays to get things started.
			 */

         // Create a random placeholder ID for the loop
         $random_id = 'loop-' . rand( 100, 999 );

         // Create key/value pairs of the numbers 1 to 50
         $numbers = array_combine( range( 1, 50 ), range( 1, 50 ) );
         $number_choices = array_merge( array( '0' => '--' ), $numbers );

			// Shared parameters for get_post_types() and get_taxonomies()
			$args = array( 'public' => true, '_builtin' => false );
			$output = 'objects';
			$operator = 'and';

			// Create the array of post types
			$post_types = get_post_types( $args, $output, $operator );
			$builtin_post = array( 'post' => __( 'Posts', $this->plugin_slug ) );
			$cpt_array = array();

			foreach ( $post_types as $post_type ) {
				$cpt_array[ $post_type->name ] = $post_type->label;
			}
			$post_type_choices = array_merge( $builtin_post, $cpt_array );

			// Create the array of taxonomies
			$taxonomies = get_taxonomies( $args, $output, $operator );
			$builtin_tax = array(
            '' => '-- Select a taxonomy --',
            'category' => __( 'Post Categories', $this->plugin_slug ),
            'post_tag' => __( 'Post Tags', $this->plugin_slug )
         );
			$ctax_array = array();

			foreach ( $taxonomies as $taxonomy ) {
				$ctax_array[ $taxonomy->name ] = $taxonomy->label;
			}
			$taxonomy_choices = array_merge( $builtin_tax, $ctax_array );

			/*
			 * And now, the actual settings.
			 */

         // Create a unique ID for the shortcode
         $this->settings['shortcode_id'] = array(
            'section' => 'loop_options',
            'title'   => __( 'Shortcode ID', $this->plugin_slug ),
            'desc'    => __( 'Enter a unique ID for this post loop (no spaces). A random ID will be assigned if one isn\'t provided.', $this->plugin_slug ),
            'type'    => 'text',
            'std'     => $random_id
         );

			// What post type to display?
			$this->settings['post_type'] = array(
				'section' => 'loop_options',
				'title'   => __( 'Post Type', $this->plugin_slug ),
				'desc'    => __( 'Choose what kind of posts to display with Isotope.', $this->plugin_slug ),
				'type'    => 'select',
				'std'     => 'post',
				'choices' => $post_type_choices
			);

			// Limit by taxonomy term?
			$this->settings['limit_posts'] = array(
				'section' => 'loop_options',
				'title'   => __( 'Limit Post Display', $this->plugin_slug ),
				'desc'    => __( 'Only show posts that have a specific category, tag, or taxonomy term?', $this->plugin_slug ),
				'type'    => 'select',
				'std'     => 'no',
				'choices' => array(
					'no'  => __( 'No, include all the posts from the above post type', $this->plugin_slug ),
					'yes' => __( 'Yes, limit the posts by specific taxomony term(s)', $this->plugin_slug )
				)
			);

			$this->settings['limit_by'] = array(
				'section' => 'loop_options',
				'title'   => __( 'Limiting Taxonomy', $this->plugin_slug ),
				'desc'    => __( 'Choose a taxonomy to limit what posts are displayed.', $this->plugin_slug ),
				'type'    => 'select',
				'std'     => '',
				'choices' => $taxonomy_choices
			);

			$this->settings['limit_term'] = array(
				'section' => 'loop_options',
				'title'   => __( 'Term Slug(s)', $this->plugin_slug ),
				'desc'    => __( 'Enter the taxonomy term slugs for posts that should NOT be displayed. Seperate multiple terms with commas (e.g. "comedy, thriller").', $this->plugin_slug ),
				'type'    => 'text',
            'std'     => ''
			);

			// Show a filter menu?
			$this->settings['filter_menu'] = array(
				'section' => 'loop_options',
				'title'   => __( 'Add Filter Menu', $this->plugin_slug ),
				'desc'    => __( 'Include a taxonomy-based menu for users to filter posts?', $this->plugin_slug ),
				'type'    => 'select',
				'std'     => 'no',
				'choices' => array(
					'no'  => __( 'No', $this->plugin_slug ),
					'yes' => __( 'Yes', $this->plugin_slug )
				)
			);

			$this->settings['filter_by'] = array(
				'section' => 'loop_options',
				'title'   => __( 'Filter Menu Taxonomy', $this->plugin_slug ),
				'desc'    => __( 'Choose a taxonomy for the menu that will allow users to filter the posts.', $this->plugin_slug ),
				'type'    => 'select',
				'std'     => '',
				'choices' => $taxonomy_choices
			);

         // Paginate with infinite scrolling?
         $this->settings['pagination'] = array(
            'section' => 'loop_options',
            'title'   => __( 'Pagination', $this->plugin_slug ),
            'desc'    => __( 'Paginate this loop and add more posts with infinite scroll?', $this->plugin_slug ),
            'type'    => 'select',
            'std'     => 'no',
            'choices' => array(
               'no' => __( 'No, display all available posts at once', $this->plugin_slug ),
               'yes' => __( 'Yes, paginate the post loop with infinite scroll', $this->plugin_slug )
            )
         );

         $this->settings['posts_per_page'] = array(
            'section' => 'loop_options',
            'title'   => __( 'Posts Per Load', $this->plugin_slug ),
            'desc'    => __( 'How many posts should be loaded at a time?', $this->plugin_slug ),
            'type'    => 'select',
            'std'     => '0',
            'choices' => $number_choices
         );

         $this->settings['finished_message'] = array(
            'section' => 'loop_options',
            'title'   => __( 'Finished Message', $this->plugin_slug ),
            'desc'    => __( 'Display a message when all of the posts have loaded?', $this->plugin_slug ),
            'type'    => 'text',
            'std'     => ''
         );

			// Customize isotope display?
			$this->settings['layout'] = array(
				'section' => 'loop_options',
				'title'   => __( 'Layout', $this->plugin_slug ),
				//'desc'    => __( 'This is a description for the drop-down.', $this->plugin_slug ),
				'type'    => 'select',
				'std'     => 'fitRows',
				'choices' => array(
					'fitRows' => __( 'Left to right in rows', $this->plugin_slug ),
					'masonry' => __( 'Fit masonry-style', $this->plugin_slug )
				)
			);

			$this->settings['sort_by'] = array(
				'section' => 'loop_options',
				'title'   => __( 'Sort Order', $this->plugin_slug ),
				//'desc'    => __( 'This is a description for the drop-down.', $this->plugin_slug ),
				'type'    => 'select',
				'std'     => 'date',
				'choices' => array(
					'date' => __( 'Descending by publish date', $this->plugin_slug ),
					'name' => __( 'Ascending alphabetically', $this->plugin_slug )
				)
			);

		}

		// Callback for the loops options section
		public function display_loop_options_section() {
         echo '<p>' . __( 'Use these options to customize the output of WordPress posts using an Isotope Post shortcode:', $this->plugin_slug ) . '</p>';
		}

		// Callback for future sections that don't have descriptions
		public function display_section() {
			// The default echos nothing for the description
		}

		public function admin_init() {

         register_setting( 'isotope_options', 'isotope_options', array( $this, 'validate_settings' ) );

			foreach ( $this->sections as $slug => $title ) {
				if ( $slug == 'loop_options' ) {
					add_settings_section( $slug, $title, array( $this, 'display_loop_options_section' ), 'isotope-options' );
				} else {
					add_settings_section( $slug, $title, array( $this, 'display_section' ), 'isotope-options' );
				}
			}

			$this->get_settings();

			foreach ( $this->settings as $id => $setting ) {
				$setting['id'] = $id;
				$this->create_setting( $setting );

			}
		}

		// Validate the settings input before saving
		public function validate_settings( $input = array() ) {

         $save_flag = isset( $_POST['save_flag'] ) ? $_POST['save_flag'] : null;
         $loop_id = isset( $_POST['loop_id'] ) ? $_POST['loop_id'] : null;

         // Check if any options already exist
         $isotope_loops = ( get_option( 'isotope_options' ) != false ) ? get_option( 'isotope_options' ) : array();

         if ( $save_flag == 'delete' ) {

            // Unset the array key for the deleted loop
            if ( array_key_exists( $loop_id, $isotope_loops ) ) {
               unset( $isotope_loops[$loop_id] );
            }

            $output = $isotope_loops;
            return $output;

         } else {

            // Create array for storing the validated options
            $input = $input ? $input : array();

   			// Loop through each of the incoming options
   			foreach( $input as $key => $value ) {

   				if ( isset( $input[$key] ) ) {
   					$input[$key] = strip_tags( stripslashes( $input[$key] ) );
   				}

               // Format the shortcode ID slug, or create a random ID if one isn't provided
               if ( $input[$key] == $input['shortcode_id'] ) {
                  $value = preg_replace( '/[^A-Za-z0-9\-]/', '', $value );
               }

               $input[$key] = apply_filters( 'validate_settings', $value, $key );
   			}

            // Add settings error if the user wants to limit the post display but doesn't pick a limiting taxonomy
            if ( $input['limit_posts'] == 'yes' && $input['limit_by'] == '' ) {
               add_settings_error( 'limit_by', 'filter_error', __( 'Please choose a taxonomy that contains the terms to limit the post display.', $this->plugin_slug ) );
            }

   			// Add error if the user wants to limit the post display by taxonomy terms but no terms are entered
   			if ( $input['limit_posts'] == 'yes' && $input['limit_term'] == '' ) {
   				add_settings_error( 'limit_term', 'filter_error', __( 'Please enter at least one term slug to limit what posts are displayed.', $this->plugin_slug ) );
   			}

            // Add settings error if the user wants to add a filter menu but doesn't pick a taxonomy
            if ( $input['filter_menu'] == 'yes' && $input['filter_by'] == '' ) {
               add_settings_error( 'filter_by', 'filter_error', __( 'Please choose a taxonomy that will be used to create the filter menu.', $this->plugin_slug ) );
            }

            // Add error if the user wants pagainate posts but they don't pick a "posts per page" number
            if ( $input['pagination'] == 'yes' && $input['posts_per_page'] == '0' ) {
               add_settings_error( 'posts_per_page', 'filter_error', __( 'Please select how many posts to load at a time with infinite scroll.', $this->plugin_slug ) );
            }

            // Unset the array key if the shortcode ID already exists to remove the old settings
            if ( array_key_exists( $input['shortcode_id'], $isotope_loops ) ) {
               unset( $isotope_loops[$input['shortcode_id']] );
            }

            // Merge our new settings with the existing
            $output = array_merge( $isotope_loops, array( $input['shortcode_id'] => $input ) );

   			return $output;

         } // end check for $save_flag
		}

	} // end class Isotope_Posts_Settings

} // end 'if class exists'

/**
 * Get the plugin options (to be used in other plugin files).
 *
 * @since    1.0.0
 */

function isotope_option( $option ) {

	$options = get_option( 'isotope_options' );

	if ( isset( $options[$option] ) ) {
		return $options[$option];
	} else {
		return false;
	}

}
