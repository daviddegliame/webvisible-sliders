<?php
/*
Plugin Name: Webvisible Sliders
Description: plein de petites slides pour tous les jours
Version: 01 02 2015
Author: Degliame David
Author URI: http://degliame.fr/
*/

defined('ABSPATH') || exit('Cheatin\'uh?');	// chargement direct interdit

add_action( 'after_setup_theme', 'webvisible_slider_setup' );
if ( ! function_exists( 'webvisible_slider_setup' ) )
{
    add_image_size('webvisible_sliders', 800, 500, array('center','center') );
	function webvisible_slider_setup() 
	{
		// custom post type pour les slides du carousel d'acceuil
		$slide_args = 
		array(
				'public'           => true,
				'query_var'        => 'homeslides',
				'supports'         => array('title', 'excerpt', 'thumbnail', 'custom-fields', 'page-attributes'),
		
				'labels'           => 
				array(
					'name'          => 'Home Carousel',
					'singular_name' => 'Slide',
					'add_new'       => 'Ajouter Slide',
					'add_new_item'  => 'Ajouter Slide',
					'edit_item'     => 'Editer Slide',
					'new_item'      => 'Nouvelle Slide',
					'view_item'     => 'Voir Slide',
					'search_items'  => 'Search Slides',
					'not_found'     => 'No Slides Found'
					 )
			 );
			
		register_post_type( 'home_slides', $slide_args );
	}
}


add_action( 'wp_enqueue_scripts', 'webvisible_slider_load_js' );

function webvisible_slider_load_js() 
{
 
//	wp_register_script( 'jquery1-7-1-min', plugins_url('/js/libs/jquery-1.7.1.min.js', __FILE__) ); 
	wp_register_script( 'jquery1-8-2-min', 'http://code.jquery.com/jquery-1.8.2.min.js');
	wp_register_script( 'slides-min-jquery', plugins_url('/js/jquery.carouFredSel-6.1.0-packed.js', __FILE__), array('jquery1-8-2-min') );
	wp_register_script( 'home-carousel-conf', plugins_url('/js/slides_conf.js', __FILE__), array('slides-min-jquery') );
}

add_action('wp_enqueue_scripts','webvisible_slider_load_css');

function webvisible_slider_load_css()
{
	wp_register_style('webvisible_slider_style', plugins_url('css/style-slider-caroufredsel.css',  __FILE__) );
	wp_enqueue_style('webvisible_slider_style');
}

// Creer un carousel via shortcode 
add_shortcode( 'home_slider', 'webvisible_slider_home_slider' );
function webvisible_slider_home_slider() 
{
	$args_slides = array (
		'post_type'              => 'home_slides',
		'post_status'            => 'publish',
		'posts_per_page'         => '-1',
		'order'                  => 'DESC',
		'orderby'                => 'menu_order',
		'cache_results'          => true,
	);
	$query_slides = new wp_query($args_slides);
	
	if ($query_slides->have_posts()) :
    
    
        while ($query_slides->have_posts()) : $query_slides->the_post(); 
            $large[] .= get_the_post_thumbnail($post->ID ,'full');
            $thumb[] .= get_the_post_thumbnail($post->ID ,'thumbnail');
        endwhile;
    
		$output = '
		<div id="inner">
			
				<div id="carousel">'."\r\n".implode("\r\n", $large ).'
				</div> <!-- #carousel -->
			

			<div id="pager-wrapper">
				<div id="pager">'."\r\n".implode("\r\n", $thumb ).'
				</div> <!-- #pager -->	
			</div> <!-- #pager-wrapper -->
		</div> <!-- #inner --> ';
	endif;
	
	
	wp_reset_postdata();
	wp_enqueue_script('home-carousel-conf');
	
	return $output;
}

