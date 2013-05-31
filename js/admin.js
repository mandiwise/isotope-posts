(function ($) {
	"use strict";
	$(function () {

		// - add classes and ids Instagram feed settings form -
		$('body.settings_page_isotope-options table.form-table').addClass('isotope-feed');
		$('table.isotope-feed tr:nth-child(2)').addClass('posttype');
		$('table.isotope-feed tr:nth-child(4)').addClass('filterby');
		$('table.isotope-feed tr:nth-child(5)').addClass('taxonomy');
		
		// - hide custom post type field if not needed -
		$('[name="isotope_options[post_type]"]').change(function(){
			if (this.value == 'post') {
				$('.posttype').hide().find('input:hidden').val('');
			} else {
				$('.posttype').show('300', function() { 
					$('.posttype').find('input:hidden').val('');
				});
			}
		}).change();

		// - hide filter menu options if not needed -
		$('[name="isotope_options[filter_menu]"]').change(function(){
			if (this.value == 'no') {
				$('.filterby').hide().find('select').val('category');
				$('.taxonomy').hide().find('input:hidden').val('');
			} else {
				$('.filterby').show('300', function() { 
					$('.taxonomy').find('input:hidden').val('');
				});
			}
		}).change();
		
		
		// - hide custom taxomny field if not needed -
		$('[name="isotope_options[filter_by]"]').change(function(){
			if (this.value == 'category' || this.value == 'post_tag') {
				$('.taxonomy').hide().find('input:hidden').val('');
			} else {
				$('.taxonomy').show('300', function() { 
					$('.taxonomy').find('input:hidden').val('');
				});
			}
		}).change();
		
	});
}(jQuery));