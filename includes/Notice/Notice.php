<?php

namespace RRZE\Elements\Notice;

defined('ABSPATH') || exit;

/**
 * [Notice description]
 */
class Notice
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        add_shortcode('notice-alert', [$this, 'shortcodeNotice']);
        add_shortcode('notice-attention', [$this, 'shortcodeNotice']);
        add_shortcode('notice-hinweis', [$this, 'shortcodeNotice']);
        add_shortcode('notice-baustelle', [$this, 'shortcodeNotice']);
        add_shortcode('notice-plus', [$this, 'shortcodeNotice']);
        add_shortcode('notice-minus', [$this, 'shortcodeNotice']);
        add_shortcode('notice-question', [$this, 'shortcodeNotice']);
        add_shortcode('notice-tipp', [$this, 'shortcodeNotice']);
        add_shortcode('notice-video', [$this, 'shortcodeNotice']);
        add_shortcode('notice-audio', [$this, 'shortcodeNotice']);
        add_shortcode('notice-download', [$this, 'shortcodeNotice']);
        add_shortcode('notice-faubox', [$this, 'shortcodeNotice']);
        /* Für die Abwärtskompatibilität der bereits in FAU-Einrichtungen
         * abwärtskompatiblen Shortcodes noch folgendes: */
        add_shortcode('attention', [$this, 'shortcodeNotice']);
        add_shortcode('hinweis', [$this, 'shortcodeNotice']);
        add_shortcode('baustelle', [$this, 'shortcodeNotice']);
        add_shortcode('plus', [$this, 'shortcodeNotice']);
        add_shortcode('minus', [$this, 'shortcodeNotice']);
        add_shortcode('question', [$this, 'shortcodeNotice']);
    }

    /**
     * [shortcodeNotice description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @param  string $tag     [description]
     * @return string          [description]
     */
    public function shortcodeNotice($atts, $content = '', $tag = '')
    {
        extract(shortcode_atts([
            'title' => '',
            'hstart' => '3'
        ], $atts));

        $tag_array = explode('-', $tag);

        if (count($tag_array) > 1) {
            $type = $tag_array[1];
        } else {
            $type = $tag_array[0];
        }
        $class = ($title == '' ? ' no-title' : '');
        $hstart = intval($hstart);

        $output = '<div class="notice notice-' . $type . $class . '">';
        if (isset($title) && $title != '') {
            $output .= "<h$hstart>" . $title . "</h$hstart>";
        }
        $output .= '<p>' . do_shortcode($content) . '</p></div>';

        wp_enqueue_style('fontawesome');
        wp_enqueue_style('rrze-elements');

        return $output;
    }
}
