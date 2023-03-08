<?php

namespace RRZE\Elements\Symbols;

defined('ABSPATH') || exit;

/**
 * Displays Symbols (mostly SVGs)
 */
class Symbols {

    public function __construct()  {
	add_shortcode('success', [$this, 'shortcode_ok']);
    add_shortcode('ok', [$this, 'shortcode_ok']);
	add_shortcode('miss', [$this, 'shortcode_miss']);
	add_shortcode('fail', [$this, 'shortcode_miss']);
    }


    public function shortcode_ok($atts)  {
        extract(shortcode_atts([
            'size' => '',
	    'text'  => __('Erfüllt', 'rrze-elements')

        ], $atts));
	

	$text = esc_html($text);
	 
        $size = ($size) ? ' ' . $size . '-btn' : '';
       

	if (!empty($size)) {
	    $class = ' '.$size;
	}
	
	
        $output = '<span class="symbol-check '.$class.'"';
	$output .= '></span>';
	$output .= '<span class="screen-reader-text">'.$text.'</span>';
        wp_enqueue_style('rrze-elements');
        return $output;
    }
    
    public function shortcode_miss($atts)  {
        extract(shortcode_atts([
            'size' => '',
	    'text'  => __('Nicht erfüllt', 'rrze-elements')

        ], $atts));
	$class = '';
        

	$text = esc_html($text);
	 
        $size = ($size) ? ' ' . $size . '-btn' : '';
       
	
	if (!empty($size)) {
	    $class = ' '.$size;
	}
	
	
        $output = '<span class="symbol-miss '.$class.'"';
	$output .= '></span>';
	$output .= '<span class="screen-reader-text">'.$text.'</span>';
        wp_enqueue_style('rrze-elements');
        return $output;
    }
    
}
