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
        /* Für die Abwärtskompatibilität der bereits in FAU-Einrichtungen
         * abwärtskompatiblen Shortcodes noch folgendes: */
        add_shortcode('attention', [$this, 'shortcode_notice']);
        add_shortcode('hinweis', [$this, 'shortcode_notice']);
        add_shortcode('baustelle', [$this, 'shortcode_notice']);
        add_shortcode('plus', [$this, 'shortcode_notice']);
        add_shortcode('minus', [$this, 'shortcode_notice']);
        add_shortcode('question', [$this, 'shortcode_notice']);
    }

    public function shortcode_notice($atts, $content = '', $tag) {
        extract(shortcode_atts([
            'title' => ''
        ], $atts));

        $tag_array = explode('-', $tag);

        if (count($tag_array) > 1) {
            $type = $tag_array[1];
        } else {
            $type = $tag_array[0];
        }
        $output = '<div class="notice notice-' . $type . '">';
        $output .= (isset($title) && $title != '') ? '<h3>' . $title . '</h3>' : '';
        $output .= '<p>' . do_shortcode($content) . '</p></div>';

        wp_enqueue_style('fontawesome');
        wp_enqueue_style('rrze-elements');

        return $output;
    }

}
