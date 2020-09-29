<?php
/**
 * Plugin Name: Auto Anchor
 * Plugin URI:  https://github.com/BenediktBergmann/WordPress-Anchor-Plugin
 * Description: Adds anchors to every header in a MS doc look.
 * Version:     1.0.0
 * Author:      Benedikt Bergmann
 * Author URI:  https://benediktbergmann.eu
 * Text Domain: Auto-Anchor 
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
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

	add_action( 'admin_init', 'plugin_has_required_plugin' );
	function plugin_has_required_plugin() {
	    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'font-awesome/index.php' ) ) {
	        add_action( 'admin_notices', 'child_plugin_notice' );

	        deactivate_plugins( plugin_basename( __FILE__ ) ); 

	        if ( isset( $_GET['activate'] ) ) {
	            unset( $_GET['activate'] );
	        }
	    }
	}

	function child_plugin_notice(){
	    ?><div class="error"><p>Sorry, but "Auto Anchor" Plugin requires the "<a href="https://wordpress.org/plugins/font-awesome/" target="_blank">Font Awesome</a>" plugin to be installed and active.</p></div><?php
	}
?>