<?php

namespace RRZE\Elements\PullDiv;

defined('ABSPATH') || exit;

class PullDiv {

    public function __construct() {
        add_shortcode('pull-left', [$this, 'shortcode_pull_left_right']);
        add_shortcode('pull-right', [$this, 'shortcode_pull_left_right']);        
    }

    public function shortcode_pull_left_right($atts, $content = '', $tag) {
        extract(shortcode_atts([
            'title' => '',
            'align' => ''
        ], $atts));
        
        $tag_array = explode('-', $tag);
        $type = $tag_array[1];
        
        $textalign = in_array($align, ['left', 'right']) ? 'align-' . $align : '';
        $output = '<aside class="pull-' . $type . ' ' . $textalign . '">';
        $output .= (isset($title) && $title != '') ? '<h1>' . $title . '</h1>' : '';
        $output .= '<p>' . do_shortcode($content) . '</p></aside>';
        
        return $output;
    }

}
