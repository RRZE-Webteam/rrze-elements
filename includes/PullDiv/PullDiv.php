<?php

namespace RRZE\Elements\PullDiv;

use RRZE\Elements\Helper;

defined('ABSPATH') || exit;

/**
 * [PullDiv description]
 */
class PullDiv
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        add_shortcode('pull-left', [$this, 'shortcodePullLeftRight']);
        add_shortcode('pull-right', [$this, 'shortcodePullLeftRight']);
        add_shortcode('limit-width', [$this, 'shortcodeLimitWidth']);
    }

    /**
     * [shortcodePullLeftRight description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @param  string $tag     [description]
     * @return string          [description]
     */
    public function shortcodePullLeftRight($atts, $content = '', $tag = '')
    {
        $atts = shortcode_atts( [
            'title' => '',
            'align' => '',
            'clearafter' => 'false'
        ], $atts);
        array_walk($atts, 'sanitize_text_field');

        $tag_array = explode('-', $tag);
        $type = $tag_array[1];

        $textalign = in_array($atts['align'], ['left', 'right']) ? ' align-' . $atts['align'] : '';
        $clearafter = Helper::shortcode_boolean($atts['clearafter']) == true ? '<div style="clear: both;"></div>' : '';
        $output = '<aside class="rrze-elements pull-' . $type . $textalign . '">';
        $output .= $atts['title'] ? '<h1>' . $atts['title'] . '</h1>' : '';
        $output .= '<p>' . do_shortcode(wpautop($content)) . '</p></aside>' . $clearafter;

        wp_enqueue_style('rrze-elements');
        return $output;
    }

    public function shortcodeLimitWidth($atts, $content = '', $tag = '') {
        $atts = shortcode_atts([
            'width' => '60ch',
            'align' => 'center',
        ], $atts);
        array_walk($atts, 'sanitize_text_field');
        switch ($atts['align']) {
            case 'left':
                $margin = 'margin-right: auto;';
                break;
            case 'right':
                $margin = 'margin-left: auto;';
                break;
            case 'center':
            default:
                $margin = 'margin: 0 auto;';
        }

        $output = '<div class="rrze-elements limit-width" style="max-width: min(' . $atts['width'] . ', 100%); ' . $margin . '">' . do_shortcode($content) . '</div>';

        wp_enqueue_style('rrze-elements');
        return wpautop($output, false);
    }
}
