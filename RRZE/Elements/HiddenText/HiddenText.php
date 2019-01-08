<?php

namespace RRZE\Elements\HiddenText;

defined('ABSPATH') || exit;

class HiddenText {

    public function __construct() {
        add_shortcode('hidden-text', [$this, 'shortcode_hidden_text']);
    }

    public function shortcode_hidden_text($atts, $content = '') {
        extract(shortcode_atts([
            'start' => '',
            'end' => ''
        ], $atts));

        $now = strtotime(current_time("Y-m-d H:i:s"));
        if ($start == '') {
            $start = $now;
        } else {
            $start = strtotime($start);
        }
        if ($end == '') {
            $end = $now;
        } else {
            $end = strtotime($end);
        }

        if (!$start || !$end) {
            return do_shortcode('[notice-attention]' . __('Please use a valid date format: Y-m-d H:i:s.', 'rrze-elements') . '[/notice-attention]' . $content);
        }

        if ($now < $start || $now > $end) {
            $output = '<p>' . do_shortcode($content) . '</p>';
        } else {
            $output = '';
        }
        return $output;
    }

}
