<?php

namespace RRZE\Elements\Notice;

defined('ABSPATH') || exit;

class Notice {

    public function __construct() {
        add_shortcode('notice-alert', [$this, 'shortcode_notice']);
        add_shortcode('notice-attention', [$this, 'shortcode_notice']);
        add_shortcode('notice-hinweis', [$this, 'shortcode_notice']);
        add_shortcode('notice-baustelle', [$this, 'shortcode_notice']);
        add_shortcode('notice-plus', [$this, 'shortcode_notice']);
        add_shortcode('notice-minus', [$this, 'shortcode_notice']);
        add_shortcode('notice-question', [$this, 'shortcode_notice']);
        add_shortcode('notice-tipp', [$this, 'shortcode_notice']);
        add_shortcode('notice-video', [$this, 'shortcode_notice']);
        add_shortcode('notice-audio', [$this, 'shortcode_notice']);
        add_shortcode('notice-download', [$this, 'shortcode_notice']);
        add_shortcode('notice-faubox', [$this, 'shortcode_notice']);
        
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function shortcode_notice($atts, $content = '', $tag) {
        extract(shortcode_atts([
            'title' => ''
        ], $atts));
        
        $tag_array = explode('-', $tag);
        
        $type = $tag_array[1];
        $output = '<div class="notice notice-' . $type . '">';
        $output .= (isset($title) && $title != '') ? '<h3>' . $title . '</h3>' : '';
        $output .= '<p>' . do_shortcode($content) . '</p></div>';
        
        return $output;
    }
    
    public function enqueue_scripts() {
        global $post;
        
        $shortcode_tags = [
            'notice-alert',
            'notice-attention',
            'notice-hinweis',
            'notice-baustelle',
            'notice-plus',
            'notice-minus',
            'notice-question',
            'notice-tipp',
            'notice-video',
            'notice-audio',
            'notice-download',
            'notice-faubox'
        ];
        
        foreach ($shortcode_tags as $tag) {
            if (has_shortcode($post->post_content, $tag)) {
                wp_enqueue_style('fontawesome');
                wp_enqueue_style('rrze-elements');
                break;
            }        
        }

    }
    
}
