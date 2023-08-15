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
        $content = shortcode_unautop(trim($content));

        $defaults = [
            'orientation' => 'horizontal',
            'speed' => 'slow',
            'startat' => 1,
            'autoplay' => '0',
            'autoplaypause' => 5000,
            'fixedsize' => '1',
            'datewidth' => 'normal',
            'start-end' => 'false',
        ];
        $args = shortcode_atts($defaults, $atts);
        $autoplay = filter_var($args['autoplay'], FILTER_VALIDATE_BOOLEAN);
        $autoplay_text = $autoplay ? 'true' : 'false';
        $autoplaypause = intval($args['autoplaypause']);
        $fixedsize = $args['fixedsize'] == '1' ? 'true' : 'false';
        $datewidth = $args['datewidth'] == 'large' ? 'large' : 'normal';
        $speed = esc_attr($args['speed']);
        $startat = intval($args['startat']);
        $orientation = esc_attr($args['orientation']);
        $startend = filter_var($args['start-end'], FILTER_VALIDATE_BOOLEAN);
        static $timelinr_instance;
        $timelinr_instance++;

        $output = '';
        $output .= "<div id=\"timeline-" . $timelinr_instance . "\" class=\"$orientation date-$datewidth\">";
        if ($autoplay || $startend) {
            $output .= '<div class="timeline-nav">';
            if ($startend) {
                $output .= "<a href=\"#\" class=\"to-start\" data-toggle=\"pause\" title=\"Zum ersten Eintrag springen\">". do_shortcode('[icon icon="step-backward"]') . "<span class=\"sr-only\">Zum ersten Eintrag springen</span></a> <a href=\"#\" class=\"to-end\" data-toggle=\"pause\">". do_shortcode('[icon icon="step-forward"]') . "<span class=\"sr-only\">Zum letzten Eintrag springen</span></a>";
            }
            if ($autoplay) {
                $output .= "<a href=\"#\" class=\"toggle-autoplay\" data-toggle=\"pause\">". do_shortcode('[icon icon="pause"]') . "<span class=\"sr-only\">Pause</span></a> ";
            }
            $output .= '</div>';
        }
        $output .= "<div class=\"dotted-line\"></div>";
        $output .= "<ul class=\"issues\">" . do_shortcode($content) . "</ul>";

        if ($orientation == 'horizontal') {
            $output .= "<div class=\"grad_left\"></div>" .
                    "<div class=\"grad_right\"></div>";
            $output .= "<div><a href=\"#\" class=\"prev\">". do_shortcode('[icon icon="angle-left"]') . "<span class=\"sr-only\">Previous</span></a></div>"
                    . "<div><a href=\"#\" class=\"next\">". do_shortcode('[icon icon="angle-right"]') . "<span class=\"sr-only\">Next</span></a></div>";
        } else {
            $output .= "<div class=\"grad_top\"></div>" .
                    "<div class=\"grad_bottom\"></div>";
            $output .= "<a href=\"#\" class=\"next\">". do_shortcode('[icon icon="angle-down"]') . "<span class=\"sr-only\">Next</span></a>" .
                    "<a href=\"#\" class=\"prev\">". do_shortcode('[icon icon="angle-up"]') . "<span class=\"sr-only\">Previous</span></a>";
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

        return wpautop($output);
    }

    /**
     * [shortcodeTimelineItem description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeTimelineItem($atts, $content = '')
    {
        $content = shortcode_unautop(trim($content));

        extract(shortcode_atts([
            'name' => ''
        ], $atts));

        $output = '';
        $output .= "<li data-date=" . $name . " name=" . sanitize_title($name) . ">";
        $output .= wpautop(do_shortcode($content));
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
