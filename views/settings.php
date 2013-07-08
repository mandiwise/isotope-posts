<!-- The plugin settings form -->
<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e('Isotope Post Settings', 'isotope-posts-locale') ?></h2>
		<form method="post" action="options.php">
			<?php settings_fields( 'isotope_options' ); ?>
			<?php do_settings_sections( 'isotope-options' ); ?>
			<p class="submit">
            	<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'isotope-posts-locale'); ?>" />
            </p>
		</form>
</div>