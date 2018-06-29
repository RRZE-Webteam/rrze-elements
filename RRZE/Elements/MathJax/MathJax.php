<?php

namespace RRZE\Elements\MathJax;

defined('ABSPATH') || exit;

class MathJax {

    public function __construct() {
        add_shortcode('mathjax', [$this, 'shortcode_mathjax']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function shortcode_mathjax($atts) {
        return '';
    }
    
    public function enqueue_scripts() {
        global $post;

        if (has_shortcode($post->post_content, 'mathjax')) {
            $mathjax_file = WP_CONTENT_DIR . '/cdn/mathjax/2.7.4/MathJax.js';

            if (!file_exists($mathjax_file)) {
                $cdn = '//cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-MML-AM_CHTML,Safe.js';
            } else {
                $cdn = get_site_url(NULL, 'wp-content/cdn/mathjax/2.7.4') . '/MathJax.js?config=TeX-MML-AM_CHTML,Safe.js';
            }

            if (wp_register_script('mathjax', $cdn, [], '2.7.4', TRUE)) {
                wp_enqueue_script('mathjax');
                add_action('wp_head', [$this, 'mathjax_config']);
            }
        }
    }

    public function mathjax_config() {
        $config = "MathJax.Hub.Config({tex2jax: {inlineMath: [['$','$'], ['\\\\(','\\\\)']], processEscapes: true}});\n";
        echo "\n<script type='text/x-mathjax-config'>\n" . $config . "</script>\n";
    }

}
