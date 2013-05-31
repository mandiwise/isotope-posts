<?php 

if(!class_exists('Isotope_Settings')) { 

	class Isotope_Settings { 
		
		// - array of sections for the plugin -
		private $sections;
		private $checkboxes;
		private $settings;
		
		// - construct the plugin settings object -
		public function __construct() { 
			
			$this->checkboxes = array();
			$this->settings = array();
			$this->isoset_get_settings();
		
			$this->sections['general'] = __( 'Display Settings', 'isotope-posts-locale' );
			
			add_action('admin_menu', array(&$this, 'isoset_add_submenu'));
			add_action('admin_init', array(&$this, 'isoset_admin_init'));  
			
			if ( ! get_option( 'isotope_options' ) )
			$this->isoset_initialize_settings();
			
		}
		
		// - add the description text for the settings page -
		public function isoset_display_section() {
			echo '<p>' . _e('Use these options to customize the output of WordPress posts using Isotope:', 'isotope-posts-locale') . '</p>'; 
		}
		
		// - create the settings fields -
		public function isoset_create_setting( $args = array() ) {
		
			$defaults = array(
				'id'      => 'default_field',
				'title'   => __( 'Default Field', 'isotope-posts-locale' ),
				'desc'    => '',
				'std'     => '',
				'type'    => 'text',
				'section' => 'general',
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
		
			if ( $type == 'checkbox' )
				$this->checkboxes[] = $id;
		
			add_settings_field( $id, $title, array( $this, 'isoset_display_setting' ), 'isotope-options', $section, $field_args );
		}
		
		// - create the HTML output for each possible type of setting -
		public function isoset_display_setting( $args = array() ) {
		
			extract( $args );
			$options = get_option( 'isotope_options' );
		
			if ( ! isset( $options[$id] ) && $type != 'checkbox' )
				$options[$id] = $std;
			elseif ( ! isset( $options[$id] ) )
				$options[$id] = 0;
		
			$field_class = '';
			if ( $class != '' )
				$field_class = ' ' . $class;
		
			switch ( $type ) {
			
				case 'checkbox':
					echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="isotope_options[' . $id . ']" value="1" ' . checked( $options[$id], 1, false ) . ' /> <label for="' . $id . '">' . $desc . '</label>';
					break;
			
				case 'select':
					echo '<select class="select' . $field_class . '" name="isotope_options[' . $id . ']">';
					foreach ( $choices as $value => $label )
						echo '<option value="' . esc_attr( $value ) . '"' . selected( $options[$id], $value, false ) . '>' . $label . '</option>';
					echo '</select>';
				
					if ( $desc != '' )
						echo '<p class="description">' . $desc . '</p>';
					break;
			
				case 'radio':
					$i = 0;
					foreach ( $choices as $value => $label ) {
						echo '<input class="radio' . $field_class . '" type="radio" name="isotope_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr( $value ) . '" ' . checked( $options[$id], $value, false ) . '> <label for="' . $id . $i . '">' . $label . '</label>';
						if ( $i < count( $options ) - 1 )
							echo '<br />';
						$i++;
					}
				
					if ( $desc != '' )
						echo '<p class="description">' . $desc . '</p>';
					break;
			
				case 'textarea':
					echo '<textarea class="' . $field_class . '" id="' . $id . '" name="isotope_options[' . $id . ']" placeholder="' . $std . '" rows="5" cols="30">' . wp_htmledit_pre( $options[$id] ) . '</textarea>';
				
					if ( $desc != '' )
						echo '<p class="description">' . $desc . '</p>';
					break;
			
				case 'password':
					echo '<input class="regular-text' . $field_class . '" type="password" id="' . $id . '" name="isotope_options[' . $id . ']" value="' . esc_attr( $options[$id] ) . '" />';
				
					if ( $desc != '' )
						echo '<p class="description">' . $desc . '</p>';
					break;
			
				case 'text':
				default:
					echo '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="isotope_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" />';
				
					if ( $desc != '' )
						echo '<p class="description">' . $desc . '</p>';
					break;
			
			}
		
		}
		
		// - define all the plugin settings and their defaults -
		public function isoset_get_settings() {
		
			$this->settings['post_type'] = array(
				'section' => 'general',
				'title'   => __( 'Post Type', 'isotope-posts-locale' ),
				'desc'    => __( 'Choose what kind of posts to display with Isotope.', 'isotope-posts-locale' ),
				'type'    => 'select',
				'std'     => 'post',
				'choices' => array(
					'post' => __('WordPress Posts', 'isotope-posts-locale'),
					'cpt' => __('Custom Post Type', 'isotope-posts-locale')
				)
			);
		
			$this->settings['cpt_slug'] = array(
				'title'   => __( 'Custom Post Type Slug', 'isotope-posts-locale' ),
				'desc'    => __( 'Enter the slug of the custom post type you want to use (e.g. movies).', 'isotope-posts-locale' ),
				'std'     => '',
				'type'    => 'text',
				'section' => 'general'
			);
			
			$this->settings['filter_menu'] = array(
				'section' => 'general',
				'title'   => __( 'Add Filter Menu', 'isotope-posts-locale' ),
				'desc'    => __( 'Include a taxonomy-based menu for users to filter posts.', 'isotope-posts-locale' ),
				'type'    => 'select',
				'std'     => 'no',
				'choices' => array(
					'yes' => __('Yes', 'isotope-posts-locale'),
					'no' => __('No', 'isotope-posts-locale')
				)
			);
			
			$this->settings['filter_by'] = array(
				'section' => 'general',
				'title'   => __( 'Filter Taxonomy', 'isotope-posts-locale' ),
				'desc'    => __( 'Choose a taxonomy to filter the posts.', 'isotope-posts-locale' ),
				'type'    => 'select',
				'std'     => 'category',
				'choices' => array(
					'category' => __('Post Categories', 'isotope-posts-locale'),
					'post_tag' => __('Post Tags', 'isotope-posts-locale'),
					'cust_tax' => __('Custom Taxonomy', 'isotope-posts-locale')
				)
			);
			
			$this->settings['tax_slug'] = array(
				'title'   => __( 'Custom Taxonomy Slug', 'isotope-posts-locale' ),
				'desc'    => __( 'Enter the slug of the custom taxonomy you want to use (e.g. genre).', 'isotope-posts-locale' ),
				'std'     => '',
				'type'    => 'text',
				'section' => 'general'
			);
			
			$this->settings['layout'] = array(
				'section' => 'general',
				'title'   => __( 'Layout', 'isotope-posts-locale' ),
				//'desc'    => __( 'This is a description for the drop-down.', 'isotope-posts-locale' ),
				'type'    => 'select',
				'std'     => 'fitRows',
				'choices' => array(
					'fitRows' => __('Left to right in rows', 'isotope-posts-locale'),
					'masonry' => __('Fit masonry-style', 'isotope-posts-locale')
				)
			);
			
			$this->settings['sort_by'] = array(
				'section' => 'general',
				'title'   => __( 'Sorting', 'isotope-posts-locale' ),
				//'desc'    => __( 'This is a description for the drop-down.', 'isotope-posts-locale' ),
				'type'    => 'select',
				'std'     => 'original-order',
				'choices' => array(
					'original-order' => __('Descending by date', 'isotope-posts-locale'),
					'name' => __('Ascending alphabetically', 'isotope-posts-locale')
				)
			);
		
		}
		
		// - initialize the settings to their default values -
		public function isoset_initialize_settings() {
		
			$default_settings = array();
			foreach ( $this->settings as $id => $setting ) {
				$default_settings[$id] = $setting['std'];
			}
		
			update_option( 'isotope_options', $default_settings );
		
		}
		
		// - register the settings and add settings section -
		public function isoset_admin_init() {
		
			register_setting( 'isotope_options', 'isotope_options', array( &$this, 'isoset_validate_settings' ) );
		
			foreach ( $this->sections as $slug => $title ) {
				add_settings_section( $slug, $title, array( &$this, 'isoset_display_section' ), 'isotope-options' );
			}

			$this->isoset_get_settings();

			foreach ( $this->settings as $id => $setting ) {
				$setting['id'] = $id;
				$this->isoset_create_setting( $setting );
		
			}
		}
		
		// - add the submenu page -
		public function isoset_add_submenu() { 
			add_options_page( __('Isotope Posts', 'isotope-posts-locale'),  __('Isotope Posts', 'isotope-posts-locale'), 'manage_options', 'isotope-options', array(&$this, 'isoset_plugin_settings_page') );
		}
		
		// - create the callback for the submenu page and restrict access to it -
		public function isoset_plugin_settings_page() { 
			if(!current_user_can('manage_options')) { 
				wp_die(__( 'Sorry! You don\'t have sufficient permissions to access this page.', 'isotope-posts-locale' )); 
			} 
			
			// - render the settings template - 
			include(sprintf("%s/settings.php", dirname(__FILE__))); 
		}
		
		// - validate the settings input before saving -
		public function isoset_validate_settings( $input ) {
			// - create array for storing the validated options  
			$output = array();  
  
			// - loop through each of the incoming options -
			foreach( $input as $key => $value ) {  
	  
				// - check to see if the current option has a value and then process it -  
				if( isset( $input[$key] ) ) {  
					$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );  
				}
	  
			} 

			if ( $input[ 'post_type' ] == 'cpt' && $input[ 'cpt_slug' ] == '' ) {
				add_settings_error( 'cpt_slug', 'cpt_error', __( 'Please enter a slug for the custom post type you want to display.', 'isotope-posts-locale' ) );
			}

			if ( $input[ 'filter_by' ] == 'cust_tax' && $input[ 'tax_slug' ] == '' ) {
				add_settings_error( 'tax_slug', 'tax_error', __( 'Please enter a slug for the custom taxonomy you want to use for the Filter Menu.', 'isotope-posts-locale' ) );
			}

			return apply_filters( 'isoset_validate_settings', $output, $input ); 
		}
		
	} // - end class Isotope_Settings -
	
} // - end 'if class exists' -

// * Gets the plugin options (to be used in other plugin files) *
	
function isotope_option( $option ) {
	$options = get_option( 'isotope_options' );
	if ( isset( $options[$option] ) )
		return $options[$option];
	else
		return false;
}