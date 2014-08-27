(function ($) {
	"use strict";

	$(function () {

		// Grab initial filter if there's a hash on the URL
		var initialFilter = window.location.hash && ( '.' + window.location.hash.substr(1) ) || '*';

		// Initialize Isotope
		var $container = $('#iso-loop').imagesLoaded( function(){
			$container.fadeIn().isotope({
				itemSelector : '.iso-post',
				layoutMode : iso_vars.iso_layout,
				filter : initialFilter
			});
		});

		// Initialize infinite scroll if required
		if( iso_vars.iso_paginate == 'yes' ){
			$container.infinitescroll({
				loading: {
					finishedMsg : iso_vars.finished_message,
					img : iso_vars.loader_gif,
					msgText : "",
					selector : ".iso-posts-loading",
					speed: 0,
				},
				navSelector  : ".iso-pagination",
				nextSelector : ".more-iso-posts a",
				itemSelector : ".iso-post",
				path : function generatePageUrl(currentPageNumber) {
					if ( $('body').hasClass('home') ) {
						return (iso_vars.page_url + 'page/' + currentPageNumber + "/");
					} else {
						return (iso_vars.page_url + currentPageNumber + "/");
					}
				},
			},
				function ( newElements ) {
					var $newElems = $( newElements ).hide();
					$newElems.imagesLoaded(function(){
						$newElems.fadeIn();
						$container.isotope( 'appended', $newElems );
					});
				}
			);
		}

		// Create a helper function to check if posts should be added after filtering
		var needPostsCheck = function(){
			if( iso_vars.iso_paginate == 'yes' ){
				$container.infinitescroll('update', { prefill : true } );

				if( ( $container.height() < $(window).height() ) || ( $container.children(':visible').length == 0 ) ){
					$container.infinitescroll('scroll');
				}
			} else {
				return false;
			}
		}

		// Check if posts are needed for filtered pages when they load
		$(window).load(function(){
			if( location.hash ) {
				needPostsCheck();
			}
		});

		// Set up the click event for filtering
		$('#filters a').click(function(){
			var selector = $(this).attr('data-filter');
			var niceSelector = selector.substr(1);
			location.hash = niceSelector;
			return false;
		});

		// Filter on hashchange and check if posts are needed
		$(window).bind('hashchange', function(){
			var hashFilter = location.hash && ( '.' + location.hash.substr(1) ) || '*';
			$container.isotope({ filter : hashFilter });
			needPostsCheck();
		});

	});

}(jQuery));
