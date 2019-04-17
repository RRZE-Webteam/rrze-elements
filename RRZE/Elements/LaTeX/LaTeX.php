<?php

namespace RRZE\Elements\LaTeX;

defined('ABSPATH') || exit;

const KATEX_VERSION = '0.10.1';

class LaTeX {

    public function __construct() {
        add_shortcode('latex', [$this, 'shortcode_latex'], 10, 2);

        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);

        add_filter('no_texturize_shortcodes', [$this, 'katex_exempt_wptexturize']);
    }

    public function shortcode_latex($atts, $content = '') {
        $latex_atts = shortcode_atts([
    		'display' => 'false'
    	], $atts );

        $display = $latex_atts['display'] == 'true' ? true : false;

        $output = '';

    	if ($display || strpos($content, '\\displaystyle') === 0) {
    		$output = '<span class="wp-katex-eq katex-display" data-display="true">' . htmlspecialchars(html_entity_decode($content)) . '</span>';
    	} else {
    		$output = '<span class="wp-katex-eq" data-display="false">' . htmlspecialchars(html_entity_decode($content)) . '</span>';
    	}

        wp_enqueue_style('katex');
        wp_enqueue_script('katex');
        add_action('wp_footer', [$this, 'katex_script'], 99);

        return $output;
    }

    public function enqueue_scripts() {
        wp_register_style('katex', plugins_url('LaTeX/assets/katex/' . KATEX_VERSION .'/katex.min.css', dirname(__FILE__)), false, KATEX_VERSION);
		wp_register_script('katex', plugins_url('LaTeX/assets/katex/' . KATEX_VERSION .'/katex.min.js', dirname(__FILE__)), array(), KATEX_VERSION, true);
    }

    public function katex_script() {
        $script = '!function(){"use strict";var e=document.querySelectorAll(".wp-katex-eq");Array.prototype.forEach.call(e,function(e){var t={displayMode:"true"===e.getAttribute("data-display"),throwOnError:!1},r=document.createElement("span");try{katex.render(e.textContent,r,t)}catch(a){r.style.color="red",r.textContent=a.message}e.parentNode.replaceChild(r,e)})}();';
        echo PHP_EOL, '<script>', PHP_EOL, $script, PHP_EOL, '</script>', PHP_EOL;
    }

    function katex_exempt_wptexturize($shortcodes) {
    	$shortcodes[] = 'latex';
    	return $shortcodes;
    }
}
