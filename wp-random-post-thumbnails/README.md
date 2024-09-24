# WP Random Post Thumbnails

This plugin allows you to upload a set of images via a settings page to be shown for posts without images. The images will be chosen at random on page load for any posts without a featured image (meaning each time the page is reloaded another random image will be chosen). Useful if your theme shows thumbnails for the posts, and you don't want any posts without thumbnails.

**UPDATE:** Now you can select images specifically for certain post types as well as taxonomy terms.

## Installation

1. Upload the `wp-random-thumbnails` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the plugin by going to Settings > Random Thumbnails.
4. That's it! :)

## Frequently Asked Questions

None yet!

## Changelog

[ 2.3.0 ]

* Changing the doing_ajax constant to the new Wordpress function.
* Fixing a php foreach loop warning in template-tags.php

[ 2.2.2 ]

* Making sure to add the front end filters when performing Ajax requests.

[ 2.2.1 ]

* Fixing a minor PHP notice.

[ 2.2.0 ]

* Updating CMB2 to the latest version.
* Fixing an issue in the newer CMB2 versions where the metabox tabs were being added as top level menu pages.
* Outputting CSS on our custom admin page to override some CMB2 styles in the metabox tabs.
* Updating the plugin description in an effort to make it clearer as to what exactly the plugin does.
* Verifying that the plugin works in Wordpress 4.8.2.

[ 2.1.4 ]

* Fixing a PHP error on activation in PHP 7.

[ 2.1.3 ]

* Fixing a minor PHP warning with WP_DEBUG on.

[ 2.1.2 ]

* Testing to make sure the plugin works in Wordpress 4.7.

[ 2.1.1 ]

* Fixing a minor PHP notice with WP_DEBUG on.

[ 2.1.0 ]

* Fixing an issue with terms in other languages not saving properly in the options page.

[ 2.0.8 ]

* Fixing another minor PHP warning.

[ 2.0.7 ]

* Fixing minor PHP warning when no images are selected.

[ 2.0.6 ]

* Checking whether the term is set before comparing it's value (fixes a PHP notice).

[ 2.0.5 ]

* Fixing a minor PHP warning.

[ 2.0.4 ]

* Fixing a minor PHP warning.

[ 2.0.3 ]

* Adding a 'Settings' link into the plugin action links.

[ 2.0.2 ]

* Fixing issue with $this not being allowed in anonymous functions on older PHP versions.

[ 2.0.1 ]

* Removing the old cmb folder that somehow didn't get removed during the 2.0.0 update.

[ 2.0.0 ]

* Adding support for specifying images for any taxonomy terms.
* Adding support for specifying images specific to post types.
* Code cleanup.
* Updating the options page to make it more organized by using tabs and metaboxes.

[ 1.3.3.1 ]

* Updating some comments and code spacing. No functionality changes.

[ 1.3.3 ]

* Updating the tested up to version.
* Adding a donate link in case anyone would like to be so kind as to donate. :)

[ 1.3.1 ]

* Updating some comments and spacing. No actual functionality changes.

[ 1.3.0 ]

* Fixing PHP warnings that occurred when installing the plugin and not configuring any settings.
* Moving the CMB script inclusion to the main plugin file.

[ 1.2.1 ]

* Fixing an issue when a user doesn't upload any images through the settings page.

[ 1.2.0 ]

* Fixing a fatal error on the options page.
* Removing an unncecessary tag directory.

[ 1.0.0 ] 

* Initial release.