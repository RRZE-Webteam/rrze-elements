<?php

namespace RRZE\Elements;

use RRZE\Elements\Options;
use RRZE\Elements\Settings;

defined('ABSPATH') || exit;

class Main {

    public $options;

    public $settings;

    public function init($plugin_basename) {
        $this->options = new Options();
        $this->settings = new Settings($this);

        remove_filter( 'the_content', 'wpautop' );
        add_filter( 'the_content', 'wpautop' , 12);

        //add_action('admin_menu', array($this->settings, 'admin_settings_page'));
        //add_action('admin_init', array($this->settings, 'admin_settings'));

        add_action('wp_enqueue_scripts', array($this, 'rrze_elements_enqueue_styles'));

        /*add_shortcode('notice-alert', array($this, 'rrze_elements_absatzklasse'));
        add_shortcode('notice-attention', array($this, 'rrze_elements_absatzklasse'));
        add_shortcode('notice-hinweis', array($this, 'rrze_elements_absatzklasse'));
        add_shortcode('notice-baustelle', array($this, 'rrze_elements_absatzklasse'));
        add_shortcode('notice-plus', array($this, 'rrze_elements_absatzklasse'));
        add_shortcode('notice-minus', array($this, 'rrze_elements_absatzklasse'));
        add_shortcode('notice-question', array($this, 'rrze_elements_absatzklasse'));
        add_shortcode('notice-tipp', array($this, 'rrze_elements_absatzklasse'));
        add_shortcode('notice-video', array($this, 'rrze_elements_absatzklasse'));
        add_shortcode('notice-audio', array($this, 'rrze_elements_absatzklasse'));
        add_shortcode('notice-download', array($this, 'rrze_elements_absatzklasse'));
        add_shortcode('notice-faubox', array($this, 'rrze_elements_absatzklasse'));*/

        add_shortcode('timeline', array($this, 'rrze_elements_timeline'));
        add_shortcode('timeline-item', array($this, 'rrze_elements_timeline_item'));
    }

    public static function rrze_elements_enqueue_styles() {
        global $post;
        $plugin_url = plugin_dir_url(dirname(__FILE__));
        if ($post && has_shortcode($post->post_content, 'timeline')) {
            wp_enqueue_style('rrze-timeline', $plugin_url . 'css/style.css');
            wp_enqueue_script('rrze-timeline', $plugin_url . 'js/jquery.timelinr-0.9.6.js', array ( 'jquery' ));
        }
    }

    public function rrze_elements_absatzklasse($atts, $content = null, $tag) {
        add_filter('the_content', 'wpautop', 12);
        extract(shortcode_atts(array("title" => ''), $atts));
        $tag_array = explode('-', $tag);
        $type = $tag_array[1];
        $output = '<div class="notice notice-' . $type . '">';
        $output .= (isset($title) && $title != '') ? '<h3>' . $title . '</h3>' : '';
        $output .= '<p>' . do_shortcode($content) . '</p></div>';
        return $output;
    }

    public function rrze_elements_timeline($atts, $content = null) {
        extract(shortcode_atts(array(
            'orientation' => 'horizontal',
            'speed'=> 'normal',
            'startat' => 1,
            'autoplay' => 'false',
            'autoplaypause' => 3000
        ), $atts));
        static $count = 0;
	$count++;
        $output = '';
        $output .= "<div id=\"timeline_" . $count . "\" class=\"" . $orientation . "\"><ul class=\"issues\">";
        $output .= do_shortcode($content);
        $output .= "</ul>";
        if($orientation == 'horizontal') {
            $output .= "<div class=\"grad_left\"></div>".
                    "<div class=\"grad_right\"></div>";
            $output .= "<a href=\"#\" class=\"prev\"><i class=\"fa fa-angle-left\"></i><span class=\"sr-only\">Previous</span></a>"
                    . "<a href=\"#\" class=\"next\"><i class=\"fa fa-angle-right\"></i><span class=\"sr-only\">Next</span></a>";
        } else {
            $output .= "<div class=\"grad_top\"></div>".
                    "<div class=\"grad_bottom\"></div>";
            $output .= "<a href=\"#\" class=\"next\"><i class=\"fa fa-angle-down\"></i><span class=\"sr-only\">Next</span></a>".
		"<a href=\"#\" class=\"prev\"><i class=\"fa fa-angle-up\"></i><span class=\"sr-only\">Previous</span></a>";
        }
        if ($autoplay == "true") {
            $output .= "<a href=\"#\" class=\"toggle-autoplay\" data-toggle=\"pause\"><i class=\"fa fa-pause\" aria-hidden=\"true\"></i><span class=\"sr-only\">Pause</span></a>";
        }
        $output .= "</div>";
        $output .= "<script>jQuery(function(){jQuery().timelinr({"
                . "orientation: '" . $orientation . "',
                containerDiv: '#timeline_" . $count ."',
                datesSpeed: '" . $speed . "',
                issuesSpeed: '" . $speed . "',
                startAt: " . $startat . ",
                autoPlay: '" . $autoplay . "',
                autoPlayPause: " . $autoplaypause . ""
                . "});});</script>";
        return $output;
    }
    public function rrze_elements_timeline_item($atts, $content = null) {
        extract(shortcode_atts(array(
            'name' => ''
        ), $atts));
        $output = '';
        $output .= "<li name=" . sanitize_title($name) . ">";
        $output .= do_shortcode($content);
        $output .= "</li>";
        return $output;
    }
}
