<?php
/**
 * Plugin Name: Custom Anchor - Benedikt
 * Plugin URI:  https://github.com/BenediktBergmann/WordPress-Anchor-Plugin
 * Description: Adds anchors to every header.
 * Version:     1.0.0
 * Author:      Benedikt Bergmann
 * Author URI:  https://benediktbergmann.eu
 * Text Domain: Anchor-Benedikt 
 * License:     GPL2
 */

	//Solution comes from https://jeroensormani.com/automatically-add-ids-to-your-headings/

	function add_anchors_to_headings( $content ) {

	  $content = preg_replace_callback( '/(\<h[1-6](.*?))\>(.*)(<\/h[1-6]>)/i', function( $matches ) {
	    if ( ! stripos( $matches[0], 'id=' ) ) :
	      $heading_link = '<a href="#' . sanitize_title( $matches[3] ) . '" class="heading-anchor-link"><i class="fas fa-link"></i></a>';
	      $matches[0] = $matches[1] . $matches[2] . ' id="' . sanitize_title( $matches[3] ) . '">' . $heading_link . $matches[3] . $matches[4];
	    endif;

	    return $matches[0];
	  }, $content );

	    return $content;

	}
	add_filter( 'the_content', 'add_anchors_to_headings' );

	function add_anchor_css_file(){
	    wp_enqueue_style( 'anchor-style', plugins_url('/assets/style.css', __FILE__), false, '1.0.0', 'all');
	}
	add_action('wp_enqueue_scripts', "add_anchor_css_file");
?>