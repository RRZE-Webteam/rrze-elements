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
        $atts = shortcode_atts(
            array(
                'link' => '#',
                'target' => '',
                'color' => '',
                'border_color' => '',
                'size' => '',
                'width' => '',
                'style' => '',
                'font' => '',
                'title' => '',
                'aria-label' => '',
            ), $atts );

        $arialabel = $atts['aria-label'] != '' ? ' aria-label="' . sanitize_text_field($atts['aria-label']) . '"' : '';
        /*if ($arialabel != '' && stripos($arialabel, $content) === false) {
            return do_shortcode('[alert style="danger"]' . __('<strong>Button shortcode error</strong>: The button text ("' . $content .  '") has to be included in the aria label ("' . sanitize_text_field($atts['aria-label']) .  '"). ' , 'rrze-elements') . '[/alert]');
        }*/
        $style = (in_array($atts['style'], ['success', 'info', 'warning', 'danger', 'primary', 'ghost'])) ? ' ' . $atts['style'] . '-btn' : ' primary-btn';
        $color_hex = '';
        $color_name = '';

        if ((substr($atts['color'], 0, 1) == '#') && (in_array(strlen($atts['color']), [4, 7]))) {
            $color_name = '';
            $color_hex = 'background-color:' . $atts['color'] . ';';
            $style = 'X';
        }

        if (in_array($atts['color'], ['red', 'yellow', 'blue', 'green', 'grey', 'black'])) {
            $color_name = ' ' . $atts['color'] . '-btn';
            $style = 'Y';
        }

        $border_color = ((substr($atts['border_color'], 0, 1) == '#') && (in_array(strlen($atts['border_color']), [4, 7]))) ? ' border:1px solid' . $atts['border_color'] . ';' : '';

        $size = ($atts['size']) ? ' ' . $atts['size'] . '-btn' : '';
        $target = ($atts['target'] == 'blank') ? ' target="_blank"' : '';
        $link = esc_url($atts['link']);
        switch ($atts['font']) {
            case 'dark':
                $fontColor = ' color: #1a1a1a;';
                break;
            case 'light':
                $fontColor = ' color: #fff;';
                break;
            default:
                $fontColor = '';
        }
        $title = $atts['title'] != '' ? ' title="' . sanitize_text_field($atts['title']) . '"' : '';
        $width = trim($atts['width']);
        $width_full = '';
        $width_px = '';
        if ($width == 'full') {
            $width_full = ' full-btn';
        } elseif (is_numeric($width)) {
            $width_px = 'width:' . $width . 'px; max-width:100%;';
        } elseif (strpos($width, 'px')) {
            $width = $width . 'px';
        }

        if ('' != $atts['color'] || '' != $fontColor) {
            $style = '';
        }

        $output = '<a' . $target . $title . $arialabel . ' class="standard-btn' . $color_name . $size . $width_full . $style . '" href="' . $link . '" style="' . $fontColor . $color_hex . $width_px . $border_color . '"><span>' . do_shortcode($content) . '</span></a>';

        wp_enqueue_style('fontawesome');
        wp_enqueue_style('rrze-elements');

        return $output;
    }
}
