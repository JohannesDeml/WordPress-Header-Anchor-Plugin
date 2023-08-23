<?php
/**
 * Plugin Name: Auto Anchor
 * Plugin URI:  https://github.com/BenediktBergmann/WordPress-Anchor-Plugin
 * Description: Adds anchors to every header in a MS doc look.
 * Version:     1.2.5
 * Author:      Benedikt Bergmann
 * Author URI:  https://benediktbergmann.eu
 * Text Domain: Auto-Anchor 
 * License:     GPL3
 */

	class AutoAnchor_addAnchorClass{
		public $inputintern;
		public $addedIDs;

		function __construct($input)
		{
		    $this->inputintern = $input;
		    $this->addedIDs = array();
		}

		function custom_callback($matches) {            
		    $id = '';
            
			$matches[1] = str_replace($matches[2], "", $matches[1]);

			if(stripos($matches[0], 'id=')){
				$array = array();
				preg_match( '/id="([^"]*)"/i', $matches[0], $array ) ;
				$id = strtolower($array[1]);

				$matches[2] = str_replace($array[0], "", $matches[2]);

			}
			else{
				$id = strtolower($matches[3]);
				//Replacing space with underscore
				$id = preg_replace('/\s+/', '_', $id);
				//Deleting special characters
				$id = str_replace(array('!', '?', '.', ',', '\\', '/', '<', '>', '(', ')', '[', ']', '{', '}'), '', $id);
				//Deleting umlaut
				$id = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $id);
			}

			$idWithoutIdentifier = $id;
			$idnumber = 1;
			if(substr_count(strtolower($this->inputintern), strtolower('id="' . $id . '"')) > 1){
				while(stripos(strtolower($this->inputintern), strtolower('id="' . $id . '"')) || in_array ( $id, $this->addedIDs )){
					if($idnumber != 1){
						$id = $idWithoutIdentifier . '-' . $idnumber;
					}
					$idnumber++;
				}
			}
			
			
			if($id != ''){
				array_push($this->addedIDs, $id);
				$heading_link = '<a href="#' . $id . '" class="heading-anchor-link"><i class="fas fa-link"></i></a>';
				$matches[0] = $matches[1] . ' id="' . $id . '" '. $matches[2]. '>' . $heading_link . $matches[3] . $matches[4];
			}

			return $matches[0];
		}
	}

	//Initial Solution comes from https://jeroensormani.com/automatically-add-ids-to-your-headings/

	function AutoAnchor_add_anchors_to_headings( $content ) {

		$instance = new AutoAnchor_addAnchorClass($content);

		$content = preg_replace_callback('/(\<h[2-6](.*?))\>(.*)(<\/h[2-6]>)/i', array($instance, 'custom_callback'), $content );

		return $content;

	}
	add_filter( 'the_content', 'AutoAnchor_add_anchors_to_headings' );

	function AutoAnchor_add_anchor_css_file(){
	    wp_enqueue_style( 'anchor-style', plugins_url('/assets/style.css', __FILE__), false, '1.0.0', 'all');
	}
	add_action('wp_enqueue_scripts', "AutoAnchor_add_anchor_css_file");

	function AutoAnchor_plugin_has_required_plugin() {
	    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'font-awesome/index.php' ) ) {
	        add_action( 'admin_notices', 'AutoAnchor_child_plugin_notice' );

	        deactivate_plugins( plugin_basename( __FILE__ ) ); 

	        if ( isset( $_GET['activate'] ) ) {
	            unset( $_GET['activate'] );
	        }
	    }
	}
	add_action( 'admin_init', 'AutoAnchor_plugin_has_required_plugin' );

	function AutoAnchor_child_plugin_notice(){
	    ?><div class="error"><p>Sorry, but "Auto Anchor" Plugin requires the "<a href="https://wordpress.org/plugins/font-awesome/" target="_blank">Font Awesome</a>" plugin to be installed and active.</p></div><?php
	}
?>
