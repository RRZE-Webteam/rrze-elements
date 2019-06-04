<?php

namespace RRZE\Elements\Alert;

defined('ABSPATH') || exit;

/**
 * [Alert description]
 */
class Alert
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        add_shortcode('alert', [$this, 'shortcodeAlert']);
    }

    /**
     * [shortcodeAlert description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeAlert($atts, $content = '')
    {
        extract(shortcode_atts([
            'style' => '',
            'color' => '',
            'border_color' => '',
            'font' => 'dark'
        ], $atts));

        $style = (in_array($style, array('success', 'info', 'warning', 'danger'))) ? ' alert-' . $style : '';
        $font = ($font == 'light') ? ' light' : '';
        $color = ((substr($color, 0, 1) == '#') && (in_array(strlen($color), [4, 7]))) ? 'background-color:' . $color . ';' : '';
        $border_color = ((substr($border_color, 0, 1) == '#') && (in_array(strlen($border_color), [4, 7]))) ? ' border:1px solid' . $border_color . ';' : '';

        if ('' != $color || '' != $border_color || '' != $font) {
            $style = '';
        }

        $output = '<div class="alert' . $style . $font . '" style="' . $color . $border_color . '">' . do_shortcode(($content)) . '</div>';

        wp_enqueue_style('fontawesome');
        wp_enqueue_style('rrze-elements');

        return $output;
    }
}
