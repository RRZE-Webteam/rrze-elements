<?php

namespace RRZE\Elements\HiddenText;

use RRZE\Elements\Notice\Notice;

defined('ABSPATH') || exit;

/**
 * [HiddenText description]
 */
class HiddenText
{

    protected $pluginFile;

    /**
     * [__construct description]
     */
    public function __construct($pluginFile)
    {
        add_shortcode('hidden-text', [$this, 'shortcodeHiddenText']);

        $this->pluginFile = $pluginFile;
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
            return (new Notice($this->pluginFile))->shortcodeNotice([], __('Please use a valid date format: Y-m-d H:i:s.', 'rrze-elements'), 'notice-attention') . do_shortcode($content);
        }

        if (($start != '' && $now <= $t_start) || ($end != '' && $now >= $t_end)) {
            $output = '<p>' . do_shortcode($content) . '</p>';
        } else {
            $output = '';
        }
        return $output;
    }
}
