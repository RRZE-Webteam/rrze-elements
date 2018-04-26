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

        remove_filter('the_content', 'wpautop');
        add_filter('the_content', 'wpautop', 12);

//add_action('admin_menu', array($this->settings, 'admin_settings_page'));
//add_action('admin_init', array($this->settings, 'admin_settings'));

        add_action('wp_enqueue_scripts', array($this, 'rrze_elements_enqueue_styles'));

        add_shortcode('notice-alert', array($this, 'rrze_elements_absatzklasse'));
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
        add_shortcode('notice-faubox', array($this, 'rrze_elements_absatzklasse'));

        add_shortcode('timeline', array($this, 'rrze_elements_timeline'));
        add_shortcode('timeline-item', array($this, 'rrze_elements_timeline_item'));

        add_shortcode('collapsibles', array($this, 'rrze_elements_collapsibles'));
        add_shortcode('accordion', array($this, 'rrze_elements_collapsibles'));
        add_shortcode('accordionsub', array($this, 'rrze_elements_collapsibles'));
        add_shortcode('collapse', array($this, 'rrze_elements_collapse'));
        add_shortcode('accordion-item', array($this, 'rrze_elements_collapse'));

        add_shortcode('pull-left', array($this, 'rrze_elements_pull_left_right'));
        add_shortcode('pull-right', array($this, 'rrze_elements_pull_left_right'));

        add_shortcode('content-slider', array($this, 'rrze_elements_content_slider'));
    }

    public static function rrze_elements_enqueue_styles() {
        if (is_404())
            return;
        global $post;
        $plugin_url = plugin_dir_url(dirname(__FILE__));
        if ($post && has_shortcode($post->post_content, 'timeline')
                || has_shortcode($post->post_content, 'notice')
                || has_shortcode($post->post_content, 'notice-attention')
                || has_shortcode($post->post_content, 'notice-hinweis')
                || has_shortcode($post->post_content, 'notice-baustelle')
                || has_shortcode($post->post_content, 'notice-plus')
                || has_shortcode($post->post_content, 'notice-minus')
                || has_shortcode($post->post_content, 'notice-question')
                || has_shortcode($post->post_content, 'notice-tipp')
                || has_shortcode($post->post_content, 'notice-video')
                || has_shortcode($post->post_content, 'notice-audio')
                || has_shortcode($post->post_content, 'notice-download')
                || has_shortcode($post->post_content, 'notice-faubox')) {
            wp_enqueue_style('rrze-elements', $plugin_url . 'css/style.css');
            wp_enqueue_script('rrze-timeline', $plugin_url . 'js/jquery.timelinr-0.9.6.js', array('jquery'));
        }
        if ($post && (has_shortcode($post->post_content, 'collapsibles')
                || has_shortcode($post->post_content, 'accordion')
                || has_shortcode($post->post_content, 'accordionsub')
                || has_shortcode($post->post_content, 'collapse')
                || has_shortcode($post->post_content, 'accordion'))) {
            //wp_enqueue_style('rrze-accordions', $plugin_url . 'css/style.css');
            if ($this->checkThemes() === false) {
                //if($this->checkRRZETheme() === false) {
                //wp_enqueue_style( 'rrze-accordions' );
                wp_enqueue_script('rrze-accordions');
                wp_localize_script('rrze-accordions', 'accordionToggle', array(
                    'expand_all' => __('Alle öffnen', 'rrze-2015'),
                    'collapse_all' => __('Alle schließen', 'rrze-2015'),
                ));
                //}
               // wp_enqueue_script('rrze-accordions', $plugin_url . 'js/accordion.js', array('jquery'));
            }
            wp_enqueue_script('rrze-accordions', $plugin_url . 'js/accordion.js', array('jquery'));
        }

        if ($post && (has_shortcode($post->post_content, 'content-slider'))) {
            wp_enqueue_style('elements', $plugin_url . 'css/style.css');
            wp_enqueue_script('jquery-flexslider', $plugin_url . 'js/jquery.flexslider-min.js', array('jquery'), '2.2.0', true);
            wp_enqueue_script('flexslider', $plugin_url . 'js/flexslider.js', array(), false, true);
        }
    }

    /* ---------------------------------------------------------------------------------- */
    /* Absatzklassen Shortcodes
      /*----------------------------------------------------------------------------------- */

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

    /* ---------------------------------------------------------------------------------- */
    /* Timeline
      /*----------------------------------------------------------------------------------- */

    public function rrze_elements_timeline($atts, $content = null) {
        extract(shortcode_atts(array(
            'orientation' => 'horizontal',
            'speed' => 'normal',
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
        if ($autoplay == "true") {
            $output .= "<a href=\"#\" class=\"toggle-autoplay\" data-toggle=\"pause\"><i class=\"fa fa-pause\" aria-hidden=\"true\"></i><span class=\"sr-only\">Pause</span></a>";
        }
        $output .= "</div>";
        $output .= "<script>jQuery(function(){jQuery().timelinr({"
                . "orientation: '" . $orientation . "',
                containerDiv: '#timeline_" . $count . "',
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

    /* ----------------------------------------------------------------------------------- */
    /* Accordion Shortcodes
      /*----------------------------------------------------------------------------------- */

    function rrze_elements_collapsibles($atts, $content = null) {

        add_filter('the_content', 'wpautop', 12);
        if (isset($GLOBALS['collapsibles_count']))
            $GLOBALS['collapsibles_count'] ++;
        else
            $GLOBALS['collapsibles_count'] = 0;

        $defaults = array('expand-all-link' => 'false');
        $args = shortcode_atts($defaults, $atts);
        $expand = esc_attr($args['expand-all-link']);

        $output = '';

        // if( count($tab_titles) ){
        $output .= '<div class="accordion" id="accordion-' . $GLOBALS['collapsibles_count'] . '">';
        if ($expand == "true") {
            $output .= '<p class="textalign-right small"><small><a href="#" class="expand-all" data-status="closed">' . __('Alle öffnen', 'rrze-2015') . '</a></small></p>';
        }
        $output .= do_shortcode($content);
        $output .= '</div>';
        // } else {
        //   $output .= do_shortcode( $content );
        //  }

        return $output;
    }

    public function rrze_elements_collapse($atts, $content = null) {

        if (!isset($GLOBALS['current_collapse']))
            $GLOBALS['current_collapse'] = 0;
        else
            $GLOBALS['current_collapse'] ++;

        $defaults = array('title' => 'Tab', 'color' => '', 'id' => '', 'load' => '', 'name' => '');
        extract(shortcode_atts($defaults, $atts));

        $addclass = '';

        $title = esc_attr($title);
        $color = $color ? ' ' . esc_attr($color) : '';
        $load = $load ? ' ' . esc_attr($load) : '';
        $name = $name ? ' name="' . esc_attr($name) . '"' : '';

        if (!empty($load)) {
            $addclass .= " " . $load;
        }

        $id = intval($id) ? intval($id) : 0;
        if ($id < 1) {
            $id = $GLOBALS['current_collapse'];
        }

        $output = '<div class="accordion-group' . $color . '">';
        $output .= '<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-' . $GLOBALS['collapsibles_count'] . '" href="#collapse_' . $id . '">' . $title . '</a></div>' . "\n";
        $output .= '<div id="collapse_' . $id . '" class="accordion-body' . $addclass . '"' . $name . '>';
        $output .= '<div class="accordion-inner clearfix">' . "\n";
        $output .= do_shortcode($content);
        $output .= '</div>';
        $output .= '</div></div>';


        return $output;
    }

    /* ---------------------------------------------------------------------------------- */
    /* Pull left / pull right
      /*----------------------------------------------------------------------------------- */

    public function rrze_elements_pull_left_right($atts, $content = null, $tag) {
        add_filter('the_content', 'wpautop', 12);
        extract(shortcode_atts(array("title" => '', 'align' => ''), $atts));
        $tag_array = explode('-', $tag);
        $type = $tag_array[1];
        $textalign = in_array($align, array('left', 'right')) ? 'align-' . $align : NULL;
        $output = '<aside class="pull-' . $type . ' ' . $textalign . '">';
        $output .= (isset($title) && $title != '') ? '<h1>' . $title . '</h1>' : '';
        $output .= '<p>' . do_shortcode($content) . '</p></aside>';
        return $output;
    }

    /* ---------------------------------------------------------------------------------- */
    /* Content Slider
      /*----------------------------------------------------------------------------------- */

    public function rrze_elements_content_slider($atts) {
        global $post;
        // Attributes
        extract(shortcode_atts(
                        array(
            "id" => '',
            "type" => 'post',
            "number" => '-1',
            "category" => '',
            "tag" => '',
            'orderby' => 'date', // 'rand' auch möglich!
                        ), $atts, 'content-slider')
        );
        $id = sanitize_text_field($id);
        $ids = explode(",", $id);
        $ids = array_map('trim', $ids);
        $type = sanitize_text_field($type);
        $orderby = sanitize_text_field($orderby);
        if ($orderby == 'random')
            $orderby == 'rand';
        $cat = sanitize_text_field($category);
        $tag = sanitize_text_field($tag);
        $num = sanitize_text_field($number);

        // Code
        $args = array(
            'post_type' => $type,
            'posts_per_page' => $num,
            'category_name' => $cat,
            'orderby' => $orderby,
            'post__not_in' => array($post->ID),
            'ignore_sticky_posts' => 1);
        if (strlen($id) > 0) {
            $args['post__in'] = $ids;
        }
        $the_query = new \WP_Query($args);
        $output = '';
        if ($the_query->have_posts()) :
            $output = '<div class="content-slider flexslider">';
            $output .= '<ul class="slides">';
            while ($the_query->have_posts()) : $the_query->the_post();
                $id = get_the_ID();
                $output .= '<li>';
                $output .= '<h2><a href="'. get_permalink($id) . '">' . get_the_title() . '</a></h2>';
                $output .= '<a href="'. get_permalink($id) . '">' . get_the_post_thumbnail($id, 'teaser-thumb', array('class' => 'attachment-teaser-thumb')) . '</a>';
                $output .= get_the_excerpt($id);
                //$output .= get_wke2014_custom_excerpt($length = 200, $continuenextline = 1, $removeyoutube = 1);
                $output .= '</li>';
            endwhile;
            $output .= '</ul>';
            $output .= '</div>';
        endif;
        wp_reset_postdata();

        return $output;
    }

    /* ---------------------------------------------------------------------------------- */
    /* Helper Functions
      /*----------------------------------------------------------------------------------- */

    public function checkThemes() {
        $current_theme = wp_get_theme();
        $themes = array('FAU-Einrichtungen', 'FAU-Natfak', 'FAU-Philfak', 'FAU-RWFak', 'FAU-Techfak', 'FAU-Medfak', 'RRZE 2015');

        if (!in_array($current_theme, $themes)) {
            return false;
        } else {
            return true;
        }
    }

    /* public function checkRRZETheme() {
      $current_theme = wp_get_theme();
      $themes = array('RRZE 2015');

      if(!in_array($current_theme, $themes)) {
      return false;
      } else {
      return true;
      }
      } */
}
