<?php

namespace RRZE\Elements\PullDiv;

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
    }

    /**
     * [shortcodePullLeftRight description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @param  string $tag     [description]
     * @return string          [description]
     */
    public function shortcodePullLeftRight($atts, $content = '', $tag)
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
        $clearafter = $atts['clearafter'] == 'true' ? '<div style="clear: both;"></div>' : '';
        $output = '<aside class="pull-' . $type . $textalign . '">';
        $output .= $atts['title'] ? '<h1>' . $atts['title'] . '</h1>' : '';
        $output .= '<p>' . do_shortcode($content) . '</p></aside>' . $clearafter;

        wp_enqueue_style('rrze-elements');
        return $output;
    }
}
