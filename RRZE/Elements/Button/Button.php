<?php

namespace RRZE\Elements\Button;

defined('ABSPATH') || exit;

class Button {

    public function __construct() {
        add_shortcode('button', [$this, 'shortcode_button']);
        
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function shortcode_button($atts, $content = '') {
        extract(shortcode_atts([
            'link' => '#',
            'target' => '',
            'color' => '',
            'border_color' => '',
            'size' => '',
            'width' => '',
            'style' => '',
            'font' => '',
        ], $atts));

        $style = (in_array($style, ['success', 'info', 'warning', 'danger', 'primary'])) ? ' ' . $style . '-btn' : '';
        $color_hex = '';
        $color_name = '';
        
        if ((substr($color, 0, 1) == '#') && (in_array(strlen($color), [4, 7]))) {
            $color_name = '';
            $color_hex = 'background-color:' . $color . ';';
            $style = 'X';
        }
        
        if (in_array($color, ['red', 'yellow', 'blue', 'green', 'grey', 'black'])) {
            $color_name = ' ' . $color . '-btn';
            $style = 'Y';
        }
        
        $border_color = ((substr($border_color, 0, 1) == '#') && (in_array(strlen($border_color), [4, 7]))) ? ' border:1px solid' . $border_color . ';' : '';

        $size = ($size) ? ' ' . $size . '-btn' : '';
        $target = ($target == 'blank') ? ' target="_blank"' : '';
        $link = esc_url($link);
        $font = ($font == 'dark') ? ' color: #1a1a1a;' : '';
        
        if ($width == 'full') {
            $width_full = ' full-btn';
            $width_px = '';
        } elseif (is_numeric($width)) {
            $width_px = 'width:' . $width . 'px; max-width:100%;"';
            $width_full = '';
        } else {
            $width_px = '';
            $width_full = '';
        }
        
        if ('' != $color || '' != $font) {
            $style = '';
        }

        $out = '<a' . $target . ' class="standard-btn' . $color_name . $size . $width_full . $style . '" href="' . $link . '" style="' . $font . $color_hex . $width_px . $border_color . '"><span>' . do_shortcode($content) . '</span></a>';

        return $out;
    }

    public function enqueue_scripts() {
        global $post;
        
        $shortcode_tags = ['button'];
        
        foreach ($shortcode_tags as $tag) {
            if (has_shortcode($post->post_content, $tag)) {
                wp_enqueue_style('fontawesome');
                wp_enqueue_style('rrze-elements');
                break;
            }        
        }

    }
    
}
