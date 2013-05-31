(function ($) {
	"use strict";
	$(function () {
		
		var $container = $('#iso-loop');
		
		$container.imagesLoaded( function(){
			$('#iso-loop').isotope({
				itemSelector : '.iso-post',
				layoutMode : iso_vars.iso_layout,
				getSortData : {
					name : function ( $elem ) {
						return $elem.find('.iso-title').text();
					}
				},
				sortBy : iso_vars.iso_sortby
			});
		});
		
		$('#filters a').click(function(){
			var selector = $(this).attr('data-filter'); 
			$container.isotope({ filter: selector });
			return false;
		});
		
	});
}(jQuery));