<?php

/**
 * Class WPRPT
 *
 * The main functionality of this plugin.  Adds all of the necessary actions
 * and filters for achieving random post thumbnails.
 */
class WPRPT {

	/**
	 * @var WPRPT
	 */
	private static $instance = null;


	/**
	 * Constructor. :)
	 */
	function __construct() {

		$this->init_options_page();

		if ( ! is_admin() || wp_doing_ajax() ) {
			add_filter( 'post_thumbnail_id', array($this, 'set_post_thumbnail_id') );
			add_filter( 'get_post_metadata', array($this, 'filter_get_post_metadata') , 10, 4);
			add_filter( 'wprpt_all_images', array($this, 'add_global_images') );
			add_filter( 'wprpt_all_images', array($this, 'add_images_based_on_post_type') );
			add_filter( 'wprpt_all_images', array($this, 'add_images_based_on_taxonomy') );
			add_filter( 'wprpt_all_images', array($this, 'exclude_taxonomy_terms') );
		}

		add_filter( 'plugin_action_links_' . WPRPT_PLUGIN_BASENAME, array($this, 'add_plugin_action_links') );

	}


	/**
	 * Initializes the class
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return WPRPT
	 */
	static function init() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new WPRPT;
		}
		return self::$instance;

	}


	/**
	 * Start up our options page class
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return null
	 */
	function init_options_page() {

		global $WPRPT_Options;
		$WPRPT_Options = new WPRPT_Options();
		$WPRPT_Options->hooks();

	}


	/**
	 * Overrides the ID of the post thumbnail if one doesn't already exist.
	 *
	 * @since 1.0.0
	 *
	 * @param $post_id int
	 * @return mixed
	 */
	function set_post_thumbnail_id($thumbnail_id) {

		// If the post already has a thumbnail, get out now
		if ( ! empty($thumbnail_id) )
			return $thumbnail_id;

		$selected_post_types = wprpt_get_post_types();

		// Get out if this isn't a valid selected post type
		if ( is_array($selected_post_types) && ! in_array( get_post_type(), $selected_post_types ) ) {
			return $thumbnail_id;
		}

		// Grab a random image and return the ID
		$image_id = wprpt_get_random_image();

		return !empty($image_id) ? $image_id : $thumbnail_id;

	}


	/**
	 * Add a filter to modify get_post_metadata() so we can add a filter on the
	 * post thumbnail ID. So, now there's a new filter 'post_thumbnail_id'.
	 *
	 * @see https://gist.github.com/westonruter/5808015
	 *
	 * @since 1.0.0
	 *
	 * @param $value (null|array|string)
	 * @param $object_id (int|array|string)
	 * @param $meta_key (string|array|string)
	 * @param $single (string|array|string)
	 * @return string
	 */
	function filter_get_post_metadata( $value, $post_id, $meta_key, $single ) {

		// We want to pass the actual _thumbnail_id into the filter, so requires recursion
		static $is_recursing = false;

		// Only filter if we're not recursing and if it is a post thumbnail ID
		if ( ! $is_recursing && $meta_key === '_thumbnail_id' ) {
			$is_recursing = true; // prevent this conditional when get_post_thumbnail_id() is called
			$value = get_post_thumbnail_id( $post_id );
			$is_recursing = false;
			$value = apply_filters( 'post_thumbnail_id', $value, $post_id ); // yay!
			if ( ! $single ) {
				$value = array( $value );
			}
		}

		return $value;

	}


	/**
	 * Filters the array of all possible random images and adds any that are
	 * specified globally for the current post type.
	 *
	 * @since 2.0.0
	 *
	 * @param $all_images array
	 * @return array
	 */
	function add_global_images($all_images) {

		$global_images = wprpt_get_global_images();
		$global_post_types = wprpt_get_option( 'selected_post_types' );

		// If images were uploaded and post types were selected
		if ( ! empty($global_images) && !empty($global_post_types) ) {
			$post_id = get_the_ID();
			$current_post_type = get_post_type( $post_id );

			// If the current post type is in the list of selected post types
			if ( in_array( $current_post_type, $global_post_types ) ) {
				$all_images += $global_images;
			}
		}

		return $all_images;

	}


	/**
	 * Filters the array of all possible random images and adds any that are
	 * specific to the current post type.
	 *
	 * @since 2.0.0
	 *
	 * @param $all_images array
	 * @return array
	 */
	function add_images_based_on_post_type( $all_images ) {

		$post_id = get_the_ID();
		$post_type = get_post_type( $post_id );

		if ( !empty($post_type) ) {
			$post_type_images = wprpt_get_post_type_images( $post_type );

			// If we have some images for the current post type, add them
			if ( !empty($post_type_images) ) {
				$all_images += $post_type_images;
			}
		}

		return $all_images;

	}


	/**
	 * Filters the array of all possible random images and adds any that are
	 * specific to the current taxonomy term.
	 *
	 * @since 2.0.0
	 *
	 * @param $all_images array
	 * @return array
	 */
	function add_images_based_on_taxonomy( $all_images ) {

		$post_id = get_the_ID();
		$taxonomies = get_the_taxonomies( $post_id );

		if ( !empty($taxonomies) ) {

			// Loop through each taxonomy for the current post
			foreach( $taxonomies as $slug => $taxonomy ) {
				$terms = wp_get_post_terms( $post_id, $slug );

				// Loop through each term the post is tagged in
				foreach( $terms as $term ) {
					$taxonomy_term_images = wprpt_get_taxonomy_term_images( $slug, $term );

					// If we have some term images for the post taxonomy term, add them
					// to the list of images
					if ( !empty($taxonomy_term_images) ) {
						$all_images += $taxonomy_term_images;
					}
				}

			}
		}

		return $all_images;

	}


	/**
	 * Filters the array of all possible random images and removes the images
	 * for any excluded taxonomy terms of the given post.
	 *
	 * @since 2.4.0
	 *
	 * @param $all_images array
	 * @return array
	 */
	function exclude_taxonomy_terms( $all_images ) {

		$post_id = get_the_ID();
		$taxonomies = get_the_taxonomies( $post_id );

		if ( !empty($taxonomies) ) {

			// Loop through each taxonomy for the current post
			foreach( $taxonomies as $slug => $taxonomy ) {
				$terms = wp_get_post_terms( $post_id, $slug );
				$excluded_terms = wprpt_get_option( "taxonomy_{$slug}_excluded_terms" );

				// If the term is in the list of excluded terms, remove all of
				// the random images to 'disable' the random image functionality
				// for these terms.
				foreach( $terms as $term ) {
					if ( !empty($excluded_terms) && in_array($term->slug, $excluded_terms ) ) {
						$all_images = array();
					}
				}

			}
		}

		return $all_images;

	}


	/**
	 * Adds custom links to the plugin action links on the Plugins page (Edit,
	 * Delete, Deactivate, etc).
	 *
	 * @since 2.0.3
	 *
	 * @param $links
	 * @return array
	 */
	function add_plugin_action_links( $links ) {
		global $WPRPT_Options;

		$new_links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=' . $WPRPT_Options->key ) ) .'">Settings</a>';

		return $new_links + $links;
	}

}