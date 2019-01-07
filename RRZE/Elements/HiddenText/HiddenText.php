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

        $now = new \DateTime(date("Y-m-d H:i:s"));
        $now->setTimezone(new \DateTimeZone('Europe/Berlin'));
        if ($start == '') {
            $start = $now;
        } else {
            $start = new \DateTime($start, new \DateTimeZone('Europe/Berlin'));
        }
        if ($end == '') {
            $end = $now;
        } else {
            $end = new \DateTime($end, new \DateTimeZone('Europe/Berlin'));
        }
        if ($now < $start || $now > $end) {
            $output = '<p>' . do_shortcode($content) . '</p>';
        } else {
            $output = '';
        }
        return $output;
    }

}
