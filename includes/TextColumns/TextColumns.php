<?php

namespace RRZE\Elements\TextColumns;

use function RRZE\Elements\Config\calculateContrastColor;

defined('ABSPATH') || exit;

class TextColumns {

    public function __construct()
    {
        add_shortcode('text-columns', [$this, 'shortcodeTextColumns']);
    }

    public function shortcodeTextColumns($atts, $content = null) {
        $defaults = array(
            'number' => '2',
            'width' => '240',
            'rule' => 'true',
            'rule-color' => 'var(--color-ContentBorders, #C3C3CB)',
            'background-color' => '',
            'border-color' => '',
            'font' => 'dark',
            'style' => '',
        );
        $args = shortcode_atts($defaults, $atts);
        $count = absint($args['number']);
        $width = absint($args['width']);
        $ruleColor = esc_attr($args['rule-color']);
        $backgroundColor = esc_attr($args['background-color']);
        $borderColor = esc_attr($args['border-color']);
        $style = (in_array($args['style'], array('success', 'info', 'warning', 'danger', 'example'))) ? ' alert-' . $args['style'] : '';

        $classesArr = ['elements-textcolumns'];
        $stylesArr = [
            "column-count: $count;",
            "column-width: $width\px;",
        ];
        if ($args['rule'] == 'true') {
            $stylesArr[] = "column-rule: 1px solid $ruleColor";
        }
        if (in_array($args['style'], array('success', 'info', 'warning', 'danger', 'example'))) {
            array_push($classesArr, 'alert', 'alert-' . $args['style']);
        } else {
            if ((substr($backgroundColor, 0, 1) == '#') && (in_array(strlen($backgroundColor), [4, 7]))) {
                $stylesArr[] = "background-color: $backgroundColor;";
                if (calculateContrastColor($backgroundColor) == '#ffffff') {
                    $classesArr[] = 'font-light';
                }
            }
            if ($borderColor != '') {
                $stylesArr[] = "border: 1px solid $borderColor";
            }
            if ($backgroundColor != '' || $borderColor != '') {
                $stylesArr[] = 'padding: .8em';
            }
        }

        wp_enqueue_style('rrze-elements');
        return '<div class="' . implode(' ', $classesArr) . '" ' . 'style="' . implode('; ', $stylesArr) . '">' . do_shortcode($content) . '</div>';
    }

}