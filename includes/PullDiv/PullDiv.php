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
        extract(shortcode_atts([
            'title' => '',
            'align' => ''
        ], $atts));

        $tag_array = explode('-', $tag);
        $type = $tag_array[1];

        $textalign = in_array($align, ['left', 'right']) ? 'align-' . $align : '';
        $output = '<aside class="pull-' . $type . ' ' . $textalign . '">';
        $output .= $title ? '<h1>' . $title . '</h1>' : '';
        $output .= '<p>' . do_shortcode($content) . '</p></aside>';

        wp_enqueue_style('rrze-elements');
        return $output;
    }
}
