<?php

namespace RRZE\Elements\TimeLine;

use RRZE\Elements\Main;

defined('ABSPATH') || exit;

class TimeLine {

    protected $main;

    public function __construct(Main $main) {
        $this->main = $main;
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_shortcode('timeline', [$this, 'shortcode_timeline']);
        add_shortcode('timeline-item', [$this, 'shortcode_timeline_item']);
    }

    public function shortcode_timeline($atts, $content = '') {
        extract(shortcode_atts([
            'orientation' => 'horizontal',
            'speed' => 'normal',
            'startat' => 1,
            'autoplay' => '0',
            'autoplaypause' => 3000,
            'fixedsize' => '1'
        ], $atts));

        $autoplay = filter_var($autoplay, FILTER_VALIDATE_BOOLEAN);
        $autoplay_text = $autoplay ? 'true' : 'false';
        $fixedsize = $fixedsize == '1' ? 'true' : 'false';
        static $timelinr_instance;
        $timelinr_instance++;

        $output = '';
        $output .= "<div id=\"timeline-" . $timelinr_instance . "\" class=\"" . $orientation . "\"><ul class=\"issues\">";
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

        add_action('wp_footer', function() use ($timelinr_instance, $orientation, $speed, $startat, $autoplay_text, $autoplaypause, $fixedsize) {
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

    public function shortcode_timeline_item($atts, $content = '') {
        extract(shortcode_atts([
            'name' => ''
        ], $atts));

        $output = '';
        $output .= "<li name=" . sanitize_title($name) . ">";
        $output .= do_shortcode($content);
        $output .= "</li>";

        wp_enqueue_style('fontawesome');
        wp_enqueue_style('rrze-elements');
        wp_enqueue_script('jquery-timelinr');

        return $output;
    }

    public function enqueue_scripts() {
        wp_register_script('jquery-timelinr', plugins_url('js/jquery.timelinr.min.js', $this->main->plugin_basename), ['jquery']);
    }

}
