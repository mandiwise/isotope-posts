(function ( $ ) {
	"use strict";

	// Adjust the view of the settings fields depending on what options are selected
	$(function () {

		// add classes and ids Instagram feed settings form
		$('body.settings_page_isotope-posts table.form-table').addClass('loop-options');
		$('table.loop-options tr:nth-child(4)').addClass('limitby');
		$('table.loop-options tr:nth-child(5)').addClass('limitterm');
		$('table.loop-options tr:nth-child(7)').addClass('filterby');
		$('table.loop-options tr:nth-child(9)').addClass('posts-per-page');
		$('table.loop-options tr:nth-child(10)').addClass('finished-message');

		// hide limit posts by taxonomy options if not needed
		$('[name="isotope_options[limit_posts]"]').change(function(){
			if (this.value == 'no') {
				$('.limitby').hide().find('select').val('');
				$('.limitterm').hide().find('input:hidden').val('');
			} else {
				$('.limitby').show('300');
				$('.limitterm').show('300');
			}
		}).change();

		// hide filter menu options if not needed
		$('[name="isotope_options[filter_menu]"]').change(function(){
			if (this.value == 'no') {
				$('.filterby').hide().find('select').val('');
			} else {
				$('.filterby').show('300');
			}
		}).change();

		// hide posts per load page if not needed
		$('[name="isotope_options[pagination]"]').change(function(){
			if (this.value == 'no') {
				$('.posts-per-page').hide().find('select').val('0');
				$('.finished-message').hide().find('input:hidden').val('');
			} else {
				$('.posts-per-page').show('300').find('select').val('10');
				$('.finished-message').show('300');
			}
		}).change();

	});

	// Reset default values in fields when creating a new shortcode
	$(function (){
		$('.iso-loop-add').on('click', function() {

			// special handling for the shortcode ID
			var $shortcodeID = $('[name="isotope_options[shortcode_id]"]');
			$shortcodeID.attr('value', $shortcodeID.attr('placeholder'));

			// populate the remaining fields with defaults
			$('[name="isotope_options[post_type]"]').val('post');
			$('[name="isotope_options[limit_posts]"]').val('no');
			$('[name="isotope_options[limit_by]"]').val('');
			$('[name="isotope_options[limit_term]"]').val('');
			$('[name="isotope_options[filter_menu]"]').val('no');
			$('[name="isotope_options[filter_by]"]').val('');
			$('[name="isotope_options[pagination]"]').val('no');
			$('[name="isotope_options[posts_per_page]"]').val('0');
			$('[name="isotope_options[finished_message]"]').val('');
			$('[name="isotope_options[layout]"]').val('fitRows');
			$('[name="isotope_options[sort_by]"]').val('date');

			// hide these fields initially
			$('.limitby, .limitterm, .limitterm, .posts-per-page, .finished-message').hide();
		});
	});

	// Load an existing a post loop shortcode with ajax to edit it
	$(function (){
		$('.iso-loop-edit').on('click', function() {
			var loopID = $(this).attr('rel');

			$.each(isotope_loops, function (loop, value) {
				if (loop == loopID) {
					$.each(value, function (i, option) {
						$('[name="isotope_options['+i+']"]').val(option);

						if ( i == 'limit_posts' && option == 'yes' ) {
							$('.limitby').show();
							$('.limitterm').show();
						} else if ( i == 'filter_menu' && option == 'yes' ) {
							$('.filterby').show();
						} else if ( i == 'pagination' && option == 'yes' ) {
							$('.posts-per-page').show();
							$('.finished-message').show();
						}
					});
				}
			});
		});
	});

	// Delete an existing post loop shortcode with ajax
	$(function (){
		$('.iso-loop-delete').on('click', function() {
			if ( $(this).length ) {
				$.ajax({
					type: 'post',
					url: window.ajaxurl,
					data: {
						action: 'delete_post_loop',
						save_flag: $(this).data('saveflag'),
						loop_id: $(this).attr('rel'),
						iso_ajax_nonce: window.isotope_data.ajax_nonce,
					},
					success: function( html ) {
						alert( html );
					}
				});

				$(this).parents('tr').hide(300);
			}
			return false;
		});
	});

}(jQuery));
