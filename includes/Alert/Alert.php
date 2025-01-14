<?php

namespace RRZE\Elements\Alert;

use function RRZE\Elements\Config\calculateContrastColor;

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
        add_shortcode('inner_alert', [$this, 'shortcodeAlert']);
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
            'font' => 'dark',
            'title' => '',
        ], $atts));

        $classesArr = ['alert', 'clearfix', 'clear'];
        $stylesArr = [];

        if (in_array($style, array('success', 'info', 'warning', 'danger', 'example', 'shadow', 'shadow-hover'))) {
            $classesArr[] = 'alert-' . $style;
        }

        if (!in_array($style, array('success', 'info', 'warning', 'danger', 'example'))) {
            if ((substr($color, 0, 1) == '#') && (in_array(strlen($color), [4, 7]))) {
                $stylesArr[] = 'background-color:' . $color;
                if (calculateContrastColor($color) == '#ffffff') {
                    $classesArr[] = 'font-light';
                }
            }
            if ((substr($border_color, 0, 1) == '#') && (in_array(strlen($border_color), [4, 7]))) {
                $stylesArr[] = ' border:1px solid ' . $border_color;
            }
        }

        $isExample = str_contains($style, 'example');
		$titleTag = '';

        $output = '';
		if ($isExample) {
			if ($title != '') {
				$titleTag = ' title="' . esc_attr($title) . '"';
			}
			$output .= '<div style="overflow: hidden;">';
		}
		$output .= '<div class="rrze-elements ' . implode(' ', $classesArr) . '" style="' . implode('; ', $stylesArr) . '" ' . $titleTag . '>' . do_shortcode(shortcode_unautop($content)) . '</div>';
	    if ($isExample) {
		    $output .= '</div>';
		}

        wp_enqueue_style('rrze-elements');

        return wpautop($output, false);
    }
}
