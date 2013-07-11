(function ($) {
	"use strict";
	$(function () {

		// - add classes and ids Instagram feed settings form -
		$('body.settings_page_isotope-options table.form-table').addClass('isotope-feed');
		$('table.isotope-feed tr:nth-child(2)').addClass('posttype');
		$('table.isotope-feed tr:nth-child(4)').addClass('limitby');
		$('table.isotope-feed tr:nth-child(5)').addClass('limittax');
		$('table.isotope-feed tr:nth-child(6)').addClass('limitterm');
		$('table.isotope-feed tr:nth-child(8)').addClass('filterby');
		$('table.isotope-feed tr:nth-child(9)').addClass('filtertax');
		
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

		// - hide limit posts by taxonomy options if not needed -
		$('[name="isotope_options[limit_posts]"]').change(function(){
			if (this.value == 'no') {
				$('.limitby').hide().find('select').val('category');
				$('.limitterm').hide().find('input:hidden').val('');
				$('.limittax').hide().find('input:hidden').val('');
			} else {
				$('.limitby').show('300', function() { 
					$('.limitterm').find('input:hidden').val('');
				});
				$('.limitterm').show('300');
			}
		}).change();
		
		// - hide custom taxonomy slug field for limit posts if not needed -
		$('[name="isotope_options[limit_by]"]').change(function(){
			if (this.value == 'category' || this.value == 'post_tag') {
				$('.limittax').hide().find('input:hidden').val('');
			} else {
				$('.limittax').show('300', function() { 
					$('.limittax').find('input:hidden').val('');
				});
			}
		}).change();

		// - hide filter menu options if not needed -
		$('[name="isotope_options[filter_menu]"]').change(function(){
			if (this.value == 'no') {
				$('.filterby').hide().find('select').val('category');
				$('.filtertax').hide().find('input:hidden').val('');
			} else {
				$('.filterby').show('300', function() { 
					$('.filtertax').find('input:hidden').val('');
				});
			}
		}).change();
		
		// - hide custom taxonomy slug field for filter menu if not needed -
		$('[name="isotope_options[filter_by]"]').change(function(){
			if (this.value == 'category' || this.value == 'post_tag') {
				$('.filtertax').hide().find('input:hidden').val('');
			} else {
				$('.filtertax').show('300', function() { 
					$('.filtertax').find('input:hidden').val('');
				});
			}
		}).change();
		
	});
}(jQuery));