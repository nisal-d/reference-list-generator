<?php
/**
 * Plugin Name:       Reference List Generator for Wordpress
 * Description:       This plugin allows you to create references within your website content and to output them as a list using [ref]...[/ref] and [references] shortcodes.
 */

// Hide Admin Bar
add_filter( 'show_admin_bar', '__return_false' );

// Adding Custom CSS
function add_rlg_css(){
    wp_enqueue_style( 'rlg-styles', plugins_url('/styles.css', __FILE__), false, '1.0.0', 'all');
}
add_action('wp_enqueue_scripts', "add_rlg_css");

// [ref] shortcode callback function
function ref_shortcode( $atts = array(), $content = null ) {
    if ( ! empty( $content ) ) {
        $post_id = get_the_ID();

        // Retrieving current list of references for the current post.
        $refs = wp_cache_get( "post_{$post_id}_references" );
        $refs = is_array( $refs ) ? $refs : array();

        // Addin reference to the list.
        $refs[] = $content;
        wp_cache_set( "post_{$post_id}_references", $refs );

        $j = count( $refs );
        return "<a href='#ref-$post_id-$j' id='references-$post_id-$j'><sup>[$j]</sup></a>";
    }

    return '';
}
// Adding [ref] shorcode.
add_shortcode( 'ref', 'ref_shortcode' );

// [references] shortcode callback function
function references_shortcode( $atts = array(), $content = null ) {
    $post_id = get_the_ID();
    $refs = (array) wp_cache_get( "post_{$post_id}_references" );
    $output = '';

    if ( ! empty( $refs ) ) {
        $output = '<h4>References</h4>';
        $output .= '<ol>';

        // Output references to a list.
        foreach ( $refs as $i => $ref ) {
            $j = $i + 1;
            $output .= "<li id='ref-$post_id-$j' class='reference-text'>$ref</li>" . "<a class='go-to-top' href='#references-$post_id-$j'>^</a>" . "<br>";
        }

        $output .= '</ol>';
    }

    return $output;
}

// Adding [references] shortcode.
add_shortcode( 'references' , 'references_shortcode' );
