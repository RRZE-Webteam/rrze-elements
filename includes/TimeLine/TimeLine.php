<?php

namespace RRZE\Elements\TimeLine;

defined('ABSPATH') || exit;

use RRZE\Elements\Main;

/**
 * [TimeLine description]
 */
class TimeLine
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_shortcode('timeline', [$this, 'shortcodeTimeline']);
        add_shortcode('timeline-item', [$this, 'shortcodeTimelineItem']);
    }

    /**
     * [shortcodeTimeline description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeTimeline($atts, $content = '')
    {
        extract(shortcode_atts([
            'orientation' => 'horizontal',
            'speed' => 'normal',
            'startat' => 1,
            'autoplay' => '0',
            'autoplaypause' => 3000,
            'fixedsize' => '1',
            'datewidth' => 'normal'
        ], $atts));

        $autoplay = filter_var($autoplay, FILTER_VALIDATE_BOOLEAN);
        $autoplay_text = $autoplay ? 'true' : 'false';
        $fixedsize = $fixedsize == '1' ? 'true' : 'false';
        $datewidth = $datewidth == 'large' ? 'large' : 'normal';
        static $timelinr_instance;
        $timelinr_instance++;

        $output = '';
        $output .= "<div id=\"timeline-" . $timelinr_instance . "\" class=\"$orientation date-$datewidth\"><div class=\"dotted-line\"></div><ul class=\"issues\">";
        $output .= do_shortcode($content);
        $output .= "</ul>";

        if ($orientation == 'horizontal') {
            $output .= "<div class=\"grad_left\"></div>" .
                    "<div class=\"grad_right\"></div>";
            $output .= "<a href=\"#\" class=\"prev\"><i class=\"fa fa-angle-left\"></i><span class=\"sr-only\">Previous</span></a>"
                    . "<a href=\"#\" class=\"next\"><i class=\"fa fa-angle-right\"></i><span class=\"sr-only\">Next</span></a>";
        } else {
            $output .= "<div class=\"grad_top\"></div>" .
                    "<div class=\"grad_bottom\"></div>";
            $output .= "<a href=\"#\" class=\"next\"><i class=\"fa fa-angle-down\"></i><span class=\"sr-only\">Next</span></a>" .
                    "<a href=\"#\" class=\"prev\"><i class=\"fa fa-angle-up\"></i><span class=\"sr-only\">Previous</span></a>";
        }

        if ($autoplay) {
            $output .= "<a href=\"#\" class=\"toggle-autoplay\" data-toggle=\"pause\"><i class=\"fa fa-pause\" aria-hidden=\"true\"></i><span class=\"sr-only\">Pause</span></a>";
        }

        $output .= "</div>";

        add_action('wp_footer', function () use ($timelinr_instance, $orientation, $speed, $startat, $autoplay_text, $autoplaypause, $fixedsize) {
            $config = "jQuery(document).ready(function() {jQuery().timelinr({"
                . "orientation: '" . $orientation . "',"
                . "containerDiv: '#timeline-" . $timelinr_instance . "',"
                . "datesSpeed: '" . $speed . "',"
                . "issuesSpeed: '" . $speed . "',"
                . "startAt: " . $startat . ","
                . "autoPlay: '" . $autoplay_text . "',"
                . "autoPlayPause: " . $autoplaypause . ","
                . "fixedSize: '" . $fixedsize . "'"
                . "});});";
            echo '<script type="text/javascript">' . $config . '</script>';
        });

        return $output;
    }

    /**
     * [shortcodeTimelineItem description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeTimelineItem($atts, $content = '')
    {
        extract(shortcode_atts([
            'name' => ''
        ], $atts));

        $output = '';
        $output .= "<li data-date=" . $name . " name=" . sanitize_title($name) . ">";
        $output .= do_shortcode($content);
        $output .= "</li>";

        wp_enqueue_style('fontawesome');
        wp_enqueue_style('rrze-elements');
        wp_enqueue_script('rrze-timelinr');

        return $output;
    }

    /**
     * [enqueueScripts description]
     * @return void
     */
    public function enqueueScripts()
    {
        wp_register_script(
            'rrze-timelinr',
            plugins_url('assets/js/rrze-timelinr.min.js', plugin_basename(__FILE__)),
            ['jquery'],
            '1.0.0'
        );
    }
}
