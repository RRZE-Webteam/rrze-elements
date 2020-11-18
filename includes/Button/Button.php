<?php

namespace RRZE\Elements\Button;

defined('ABSPATH') || exit;

/**
 * [Button description]
 */
class Button
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        add_shortcode('button', [$this, 'shortcodeButton']);
    }

    /**
     * [shortcodeButton description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeButton($atts, $content = '')
    {
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

        $style = (in_array($style, ['success', 'info', 'warning', 'danger', 'primary'])) ? ' ' . $style . '-btn' : ' primary-btn';
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
        $width = trim($width);
        $width_full = '';
        $width_px = '';
        if ($width == 'full') {
            $width_full = ' full-btn';
        } elseif (is_numeric($width)) {
            $width_px = 'width:' . $width . 'px; max-width:100%;';
        } elseif (strpos($width, 'px')) {
            $width = $width . 'px';
        }

        if ('' != $color || '' != $font) {
            $style = '';
        }

        $output = '<a' . $target . ' class="standard-btn' . $color_name . $size . $width_full . $style . '" href="' . $link . '" style="' . $font . $color_hex . $width_px . $border_color . '"><span>' . do_shortcode($content) . '</span></a>';

        wp_enqueue_style('fontawesome');
        wp_enqueue_style('rrze-elements');

        return $output;
    }
}
