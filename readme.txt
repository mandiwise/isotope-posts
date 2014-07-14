=== Isotope Posts ===
Contributors: mandiwise
Tags: isotope, jquery, posts
Requires at least: 3.5.1
Tested up to: 3.9.1
Stable tag: 2.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows you to use Metafizzy's Isotope to display feeds of WordPress posts using simple shortcodes. Works with custom post types and custom taxonomies too.

== Description ==

Isotope Posts is a simple WordPress implementation of Metafizzy's javascript Isotope plugin. Use the plugin settings page to customize Isotope options and create loops of WordPress posts, then implement them on your site using simple shortcodes. Magic.

Some features:

* No need to muck around with javascript or theme files – implement Isotope directly in the WordPress editor with a simple shortcode!
* Includes essential Isotope features, including the option for a filter menu, sorting options, and layout options
* Hide posts from displaying based on category, post tag, or custom taxonomy terms
* Grab the post's featured image include with the excerpt (if one is set)
* Minimal included css makes it easier to customize the look and feel of the loop output for your site

**New in v2**

* Create and save as many Isotope shortcodes as you want to use on multiple pages throughout your site
* Paginate your loop of posts using infinite scrolling (rather than loading them all at once)
* Now uses v2 of the javascript Isotope plugin
* Plays nice with WordPress Multisite

= Are you upgrading from v1.X? =

If you're upgrading to v2+ of Isotope Posts from v1.X, you'll need to run the [Isotope Posts v2 Migrator plugin](https://github.com/mandiwise/isotope-posts-migrator) first. Be sure to deactivate and delete the migrator plugin before attempting to re-activate of Isotope Posts.

*A word of caution...*

This is a soft-launch of v2 of the Isotope Posts plugin. There may still be a few kinks to work out, so please report all bugs in the [Issues](https://github.com/mandiwise/isotope-posts/issues) section of the repo.

= A note re: licensing: =

Metafizzy's javascript Isotope plugin is licensed under MIT and free to use for non-commercial, personal, open-source projects only. Find out more about [commercial licensing](http://isotope.metafizzy.co/license.html) if you plan on using this plugin for commercial purposes.

== Installation ==

1. Extract the `isotope-posts-master.zip` and remove `-master` from the extracted directory name
2. Upload the `isotope-posts` folder and its contents to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to the Settings > Isotope Posts page and adjust the settings as needed.

== Frequently Asked Questions ==

= How do I make this thing work in my theme? =

To implement Isotope Posts on your site, simply head over to the Isotope Posts settings page in your WordPress admin area, create a loop of posts with your preferred options and then save it. Next, grab the unique shortcode for the loop you just saved (e.g. `[isotope-posts id="YOUR_UNIQUE_ID"]`) and embed it in a page in your site.

= What kind of Isotope options are available? =

Isotope Posts takes advantage of a number of the original javascript plugins's options. On the plugin settings page you have the option to:

* Add a filter menu (based on post categories or tags, or a custom taxonomy)
* Arrange your posts descending by date or ascending alphabetically by title
* Choose whether to use display evenly in horizontal rows or masonry-style
* Add pagination to your posts using infinite scrolling

= Does this plugin work with custom post types and taxonomies? =

Absolutely. To use a custom post type, simply pick it from the select menu on the plugin settings page. Same goes for using a custom taxonomy for a filter menu.

= Why do things get weird when I try to add a filter menu and use infinite scrolling? =

As the author of the javascript Isotope plugin has pointed out in the past, using infinite scrolling with filtering can create a very odd user experience and you probably want to avoid doing this.

In my experience, the only time that using filters with infinite scrolling isn't completely weird is when you have a lot of posts in your selected post type AND the terms for the taxonomy you use to create your filter menu are more or less evenly spread across your posts. If you must go down this road, please be sure to test this thoroughly before you unleash it on your users.

= Why does the plugin break when I try to add two Isotope Posts shortcodes on one page? =

Play it safe. While you can implement multiple Isotope Posts shortcodes throughout your site, you'll only want to add one at a time on any given page. Things go a little bit sideways in the javascript when you try to load two Isotope Posts shortcodes on a single page.

= Is it localized? =

Yes, but no translations are available quite yet.

== Screenshots ==

1. List of saved Isotope Post shortcodes
2. Isotope Posts settings modal
3. Sample of featured image and excerpt output

== Changelog ==

= 2.0.2 =
* Fixed issue with settings array saving incorrectly when plugin options did not exist yet
* Remove automatic activation for new blogs in multisite

= 2.0.1 =
* Fixed fatal error on activation

= 2.0 =
* Refactored all plugin code
* Revamped the plugin settings page to allow users to save multiple post loops (this is a breaking change)
* Add ability to paginate posts using infinite scroll

= 1.1.3 =
* Made taxonomy classes conditional on loop items to stop php warning when not using a filter menu (props [@jengalas](https://github.com/jengalas)).

= 1.1.2 =
* Added else condition to handle post loop with no results
* Updated call to admin.js file so it only loads where needed

= 1.1.1 =
* Fixed bug when trying to filter post display by category or post tag.
* Changed filter menu to automatically remove user-excluded terms if the selected menu taxonomy is the same as the limiting taxonomy.
* Reversed "Limiting Taxonomy" logic so that entered terms slugs are excluded from display (as opposed to the only terms displayed).
* More code refactoring.

= 1.1 =
* Added ability to limit post display to specific taxonomy and terms.
* Light code refactoring.

= 1.0 =
* Initial plugin release.

== Credit Roll ==

* Tom McFarlin's time-saving [WordPress Plugin Boilerplate](https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate). Why start from scratch when you don't have to?
* David DeSandro's (Metafizzy) [Isotope](http://isotope.metafizzy.co/index.html) jQuery plugin. Minimum js input, maximum interactive fun.
