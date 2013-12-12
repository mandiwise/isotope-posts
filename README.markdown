# Isotope Posts

## Description

Isotope Posts is simple WordPress implementation of Metafizzy's Isotope jQuery plugin. Use the plugin settings page to customize Isotope options and the loop of WordPress posts, then implement on your site using a simple shortcode. Magic.

Some features:

* No need to muck around with javascript or theme files – implement Isotope directly in the WordPress editor with a simple shortcode!
* Includes essential Isotope features, including the option for a filter menu, sorting options, and layout options
* Hide posts from displaying based on category, post tag, or custom taxonomy terms
* Grab the post's featured image include with the excerpt (if one is set)
* Minimal included css makes it easier to customize the look and feel of the loop output for your site

### A note re: licensing

Metafizzy's jQuery Isotope plugin is licensed under MIT and free to use for non-commercial, personal, open-source projects only. Find out more about [commercial licensing](http://isotope.metafizzy.co/docs/license.html) if you plan on using this plugin for commercial purposes.

## Installation

1. Extract the `isotope-posts-master.zip` and remove `-master` from the extracted directory name
2. Upload the `isotope-posts` folder and its contents to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to the Settings > Isotope Posts page and adjust the settings as needed.

## Frequently Asked Questions

### How do I make this thing work in my theme?

All you have to do is add this simple shortcode to a WordPress page: `[isotope-posts]`. For maximum results, add it to a page with a full-width template.

### What kind of Isotope options are available?

Isotope Posts takes advantage of a number of the original jQuery plugins's options. On the plugin settings page you have the option to:

* Add a filter menu (based on post categories or tags, or a custom taxonomy)
* Arrange your posts descending by date or ascending alphabetically by title
* Choose whether to use display evenly in horizontal rows or masonry-style.

### Does this plugin work with custom post types and taxonomies?

Absolutely. To use a custom post type, simply add its slug to the designated field on the plugin settings page. Same goes for using a custom taxonomy for a filter menu.

### Is it localized?

Yes, but no translations are available quite yet.

## Screenshots

1. Isotope Posts settings page
2. Sample of featured image and excerpt output

## Changelog

### 1.1.3
* Made taxonomy classes conditional on loop items to stop php warning when not using a filter menu (props [@jengalas](https://github.com/jengalas)).

### 1.1.2
* Added else condition to handle post loop with no results.
* Updated call to admin.js file so it only loads where needed.

### 1.1.1
* Fixed bug when trying to limit post display by category or post tag.
* Changed filter menu to automatically remove user-excluded terms if the selected menu taxonomy is the same as the limiting taxonomy.
* Reversed "Limiting Taxonomy" logic so that entered terms slugs are excluded from display (as opposed to the only terms displayed).
* More code refactoring.

### 1.1
* Added ability to limit post display to specific taxonomy and terms.
* Light code refactoring.

### 1.0
* Plugin initial release.

## Credit Roll

* Tom McFarlin's time-saving [WordPress Plugin Boilerplate](https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate). Why start from scratch when you don't have to?
* David DeSandro's (Metafizzy) [Isotope](http://isotope.metafizzy.co/index.html) jQuery plugin. Minimum js input, maximum interactive fun.

## Author Information

This plugin was originally created by [Mandi Wise](http://mandiwise.com/).

## License

Copyright (c) 2013, Mandi Wise

Isotope Posts is dual licensed under MIT and the GPL v2 or later.

> This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License, version 2, as published by the Free Software Foundation.

> This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

> You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA