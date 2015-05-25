<?php
/**
 * Represents the view for the administration dashboard.
 *
 * @package   Isotope_Posts_Admin
 * @author    Mandi Wise <hello@mandiwise.com>
 * @license   GPL-2.0+
 * @link      http://mandiwise.com
 * @copyright 2014 Mandi Wise
 */
?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

   <?php
      // Check for existing loops to display
      $isotope_loops = ( get_option('isotope_options') != false ) ? get_option( 'isotope_options' ) : array();
   ?>

   <div id="isotope-loops">

      <h3><?php _e( 'Isotope Post Shortcodes', $this->plugin_slug ); ?></h3>

      <?php if ( $isotope_loops ) : ?>

         <p><?php _e( 'Here are the Isotope Posts shortcodes you\'ve saved so far:', $this->plugin_slug ); ?></p>

         <table class="widefat">
            <thead>
               <tr>
                  <th><?php _e( 'Post Type', $this->plugin_slug ); ?></th>
                  <th><?php _e( 'Filter Menu', $this->plugin_slug ); ?></th>
                  <th><?php _e( 'Excluded Terms', $this->plugin_slug ); ?></th>
                  <th><?php _e( 'Layout / Order', $this->plugin_slug ); ?></th>
                  <th><?php _e( 'Shortcode', $this->plugin_slug ); ?></th>
                  <th>&nbsp;</th>
               </tr>
            </thead>
            <tbody>
            <?php foreach( $isotope_loops as $isotope_loop ) : ?>
               <?php
                  // Get the post loop's options
                  $shortcode_id = $isotope_loop['shortcode_id'];
                  $post_type = $isotope_loop['post_type'] ? get_post_type_object( $isotope_loop['post_type'] )->label : __( 'N/A', $this->plugin_slug );
                  $limit_by = !empty( $isotope_loop['filter_by'] ) ? get_taxonomy( $isotope_loop['filter_by'] )->label : __( 'N/A', $this->plugin_slug );
                  $limit_term = !empty( $isotope_loop['limit_term'] ) ? $isotope_loop['limit_term'] : __( 'N/A', $this->plugin_slug );
                  $layout = $isotope_loop['layout'] == 'fitRows' ? __( 'In Rows', $this->plugin_slug ) : __( 'Masonry', $this->plugin_slug );
                  $sort_by = $isotope_loop['sort_by'] == 'date' ? __( 'Date', $this->plugin_slug ) : __( 'Alpha', $this->plugin_slug );
               ?>
               <tr>
                  <td><?php echo $post_type; ?></td>
                  <td><?php echo $limit_by; ?></td>
                  <td style="width:20%;"><?php echo $limit_term; ?></td>
                  <td><?php echo $layout; ?> / <?php echo $sort_by; ?></td>
                  <td><input type='text' readonly='readonly' size='25' value='[isotope-posts id="<?php echo $shortcode_id; ?>"]'></td>
                  <td>
                     <a href="#TB_inline?width=600&height=550&inlineId=isotope-display-options" class="iso-loop-edit thickbox" rel="<?php echo $shortcode_id; ?>" data-saveflag="edit"><?php _e( 'Edit', $this->plugin_slug ); ?></a> /
                     <a href="javascript:;" class="iso-loop-delete" rel="<?php echo $shortcode_id; ?>" data-saveflag="delete"><?php _e( 'Delete', $this->plugin_slug ); ?></a>
                  </td>
               </tr>
            <?php endforeach; ?>
            </tbody>
         </table>

      <?php else : ?>

         <p><?php _e( 'Looks like you haven\'t created any shortcodes to display post loops yet.', $this->plugin_slug ); ?></p>

      <?php endif; ?>

      <p><a href="#TB_inline?width=600&height=550&inlineId=isotope-display-options" class="button-primary iso-loop-add thickbox" data-saveflag="add"><?php _e( 'Create a Shortcode', $this->plugin_slug ); ?></a></p>

   </div>

   <div id="isotope-display-options" style="display:none;">

      <form method="post" action="options.php">
         <?php settings_fields( 'isotope_options' ); ?>
         <?php do_settings_sections( 'isotope-options' ); ?>
         <p class="submit">
            <input type="submit" name="isotope_submit" class="button-primary" value="<?php _e( 'Save Shortcode', $this->plugin_slug ); ?>" />
         </p>
      </form>

   </div>

   <?php echo sprintf('<script type="text/javascript">/* <![CDATA[ */ var isotope_loops = %s; /* ]]> */</script>', json_encode( $isotope_loops ) ); ?>

</div>
