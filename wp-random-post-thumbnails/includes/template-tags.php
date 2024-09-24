<?php
/**
 * @file template-tags.php
 *
 * Helper functions/template tags that can be used within template files.
 */


/**
 * Wrapper function around cmb_get_option
 *
 * @since 1.0.0
 *
 * @param  string  $key Options array key
 * @return mixed        Option value
 */
function wprpt_get_option( $key = '' ) {

    global $WPRPT_Options;
    return cmb2_get_option( $WPRPT_Options->key, $key );

}


/**
 * Wrapper function around cmb_update_option
 *
 * @since 2.1.0
 *
 * @param  string  $key Options array key
 * @return mixed        Option value
 */
function wprpt_update_option( $key = '', $value, $single = true ) {

	global $WPRPT_Options;
	return cmb2_update_option( $WPRPT_Options->key, $key, $value, $single );

}


/**
 * Returns an array containing 1 image in the format [attachment id] => [image URL]
 *
 * @since 1.0.0
 *
 * @param none
 * @return array|bool
 */
function wprpt_get_random_image() {

	$all_images = apply_filters( 'wprpt_all_images', array() );

	// Get out if the user didn't upload any random images
	if ( ! is_array($all_images) || empty($all_images) )
		return false;

	// If there is only 1 image, return that single image
	if ( count($all_images) === 1 && !empty($all_images[0]) )
		return $all_images[0];

	// Grab a random image and return it
    $random_image = array_rand($all_images, 1);
    return $random_image;

}


/**
 * Returns the post types that should have a random image generated as the
 * post thumbnail.
 *
 * @since 1.0.0
 * 
 * @param none
 * @return mixed
 */
function wprpt_get_post_types() {

    $post_types = wprpt_get_option( 'selected_post_types' );
    return $post_types;

}


/**
 * Returns a list of 'public' post types, which excludes things like nav items
 * and revisions.
 *
 * @since 2.0.0
 *
 * @param none
 * @return array
 */
function wprpt_get_public_post_types() {

	$all_post_types = get_post_types('', 'names');
	$public_post_types = array();

	foreach($all_post_types as $index => $post_type) {
		$post_type_object = get_post_type_object( $post_type );

		if ( ! in_array( $post_type, array( 'attachment', 'nav_menu_item', 'revision' ) ) ) {
			$public_post_types[ $post_type ] = $post_type_object;
		}
	}

	return $public_post_types;

}


/**
 * Returns an array of terms for the given taxonomy in the format of slug => name.
 *
 * @since 2.0.0
 *
 * @param $taxonomy
 * @return array
 */
function wprpt_get_all_terms_for_taxonomy( $taxonomy ) {

	$terms = get_terms(
		array(
			'taxonomy' => $taxonomy,
			'hide_empty' => false,
		)
	);

	$all_terms = array( '' => 'Please select' );

	foreach($terms as $term) {
		$all_terms[$term->term_id] = $term->name;
	}

	return $all_terms;

}


/**
 * Returns an array of all global images uploaded by the user.
 *
 * @since 2.0.0
 *
 * @param none
 * @return array
 */
function wprpt_get_global_images() {

	$images = array();
	$global_images = wprpt_get_option( "images" );

	if ( !empty( $global_images) ) {
		foreach( $global_images as $attachment_id => $image_src ) {
			$images[ $attachment_id ] = $image_src;
		}
	}

	return !empty($images) ? $images : array();

}


/**
 * Given a post type slug, we return the images uploaded by the user
 * for that post type.
 *
 * @since 2.0.0
 *
 * @param $post_type string
 * @return array
 */
function wprpt_get_post_type_images( $post_type ) {

	$images = array();
	$post_type_images = wprpt_get_option( "{$post_type}_images" );

	if ( !empty( $post_type_images) ) {
		foreach( $post_type_images as $attachment_id => $image_src ) {
			$images[ $attachment_id ] = $image_src;
		}
	}

	return !empty($images) ? $images : array();

}


/**
 * Given a taxonomy and term slug, we return the images uploaded by the user
 * for that term.
 *
 * This function was updated in version 2.1.0 to check for both the term ID
 * and the term slug. The database values were changed to start saving the
 * term ID instead of the slug. We can't guarantee that everyone will go
 * and resave their settings page, so instead of breaking the functionality,
 * we're just going to make sure it still selects the correct images for now.
 * We can remove the additional check in future versions of the plugin.
 *
 * @todo Remove term_slug check in future version of the plugin.
 *
 * @since 2.0.0
 *
 * @param $taxonomy string
 * @param $term_slug string
 * @return array
 */
function wprpt_get_taxonomy_term_images( $taxonomy, $term_obj ) {

	$images = array();
	$taxonomy_images = wprpt_get_option( "taxonomy_{$taxonomy}_term_images" );

	if ( ! empty( $taxonomy_images ) ) {
		foreach( $taxonomy_images as $term_image_set ) {

			if ( ! empty($term_image_set['term']) ) {

				if (
					intval($term_image_set['term']) === $term_obj->term_id
					|| $term_image_set['term'] === $term_obj->term_slug
				) {
					$taxonomy_term_images = ! empty( $term_image_set['images'] ) ? $term_image_set['images'] : array();

					if ( ! empty($taxonomy_term_images) ) {
						foreach( $taxonomy_term_images as $attachment_id => $image_src ) {
							$images[ $attachment_id ] = $image_src;
						}
					}
				}

			}
		}
	}

	return !empty($images) ? $images : array();

}