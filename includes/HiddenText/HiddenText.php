<?php

namespace RRZE\Elements\HiddenText;

defined('ABSPATH') || exit;

/**
 * [HiddenText description]
 */
class HiddenText
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        add_shortcode('hidden-text', [$this, 'shortcodeHiddenText']);
    }

    /**
     * [shortcodeHiddenText description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeHiddenText($atts, $content = '')
    {
        extract(shortcode_atts([
            'start' => '',
            'end' => ''
        ], $atts));

        $now = current_time('timestamp');

        $t_start = $start != '' ? strtotime($start, $now) : $now;
        $t_end = $end != '' ? strtotime($end, $now) : $now;

        if ($t_start === false || $t_end === false) {
            return do_shortcode('[notice-attention]' . __('Please use a valid date format: Y-m-d H:i:s.', 'rrze-elements') . '[/notice-attention]' . $content);
        }

        if (($start != '' && $now <= $t_start) || ($end != '' && $now >= $t_end)) {
            $output = '<p>' . do_shortcode($content) . '</p>';
        } else {
            $output = '';
        }
        return $output;
    }
}
