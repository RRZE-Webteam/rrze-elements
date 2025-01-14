<?php

namespace RRZE\Elements\Button;

defined('ABSPATH') || exit;

use RRZE\Elements\Alert\Alert;

use function RRZE\Elements\Config\calculateContrastColor;

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
            return (new Alert())->shortcodeAlert(['style' => 'danger'], __('<strong>Button shortcode error</strong>: The button text ("' . $content .  '") has to be included in the aria label ("' . sanitize_text_field($atts['aria-label']) .  '"). ' , 'rrze-elements'));
        }*/
        $classesArr = ['rrze-elements', 'standard-btn'];
        $stylesArr = [];
        if (in_array($atts['style'], ['success', 'info', 'warning', 'danger', 'primary', 'ghost'])) {
            $classesArr[] = $atts['style'] . '-btn';
        } else if ((substr($atts['color'], 0, 1) == '#') && (in_array(strlen($atts['color']), [4, 7]))) {
            $stylesArr[] = 'background-color:' . $atts['color'];
            $classesArr[] = (calculateContrastColor($atts['color']) == '#000000' ? 'font-dark' : 'font-light');
        } else {
            $classesArr[] = 'primary-btn';
        }

        if (in_array($atts['color'], ['red', 'yellow', 'blue', 'green', 'grey', 'black'])) {
            $classesArr[] = $atts['color'] . '-btn';
        }

        if (((substr($atts['border_color'], 0, 1) == '#') && (in_array(strlen($atts['border_color']), [4, 7])))) {
            $stylesArr[] = 'border:1px solid ' . $atts['border_color'];
        }

        if ($atts['size']) {
            $classesArr[] = $atts['size'] . '-btn';
        }
        $target = ($atts['target'] == 'blank') ? ' target="_blank"' : '';
        $link = esc_url($atts['link']);

        $title = $atts['title'] != '' ? ' title="' . sanitize_text_field($atts['title']) . '"' : '';
        $width = trim($atts['width']);
        if ($width == 'full') {
            $classesArr[] = 'full-btn';
        } elseif (is_numeric($width)) {
            $stylesArr[] = 'width:' . $width . 'px; max-width:100%;';
        } elseif (strpos($width, 'px')) {
            $stylesArr[] = 'width:' . $width . '; max-width:100%;';
        }

        $output = '<a' . $target . $title . $arialabel . ' class="' . implode(' ', $classesArr) . '" href="' . $link . '" style="' . implode('; ', $stylesArr) . '"><span>' . do_shortcode($content) . '</span></a>';

        wp_enqueue_style('rrze-elements');

        return $output;
    }
}
