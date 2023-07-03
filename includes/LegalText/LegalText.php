<?php

namespace RRZE\Elements\LegalText;

defined('ABSPATH') || exit;

/**
 * [LegalText description]
 */
class LegalText
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        add_shortcode('legal-text', [$this, 'legalText']);
    }

    /**
     * [shortcodePullLeftRight description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @param  string $tag     [description]
     * @return string          [description]
     */
    public function legalText($atts, $content = '', $tag = '')
    {
        $atts = shortcode_atts([
            'brackets' => '',
        ], $atts);
        $classes = ['legal-text'];
        if ($atts['brackets'] == 'square') {
            $classes[] = 'square-brackets';
        }
        wp_enqueue_style('rrze-elements');
        return '<div class="' . implode(' ', $classes) . '">' . $content . '</div>';
    }
}
