<?php

namespace RRZE\Elements;

use RRZE\Elements\Options;
use RRZE\Elements\Settings;

defined('ABSPATH') || exit;

class Main {

    public $plugin_basename;
    public $options;
    public $settings;

    public function __construct($plugin_basename) {
        $this->plugin_basename = $plugin_basename;
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

        add_shortcode('custom-news', array($this, 'rrze_elements_news'));

        add_shortcode('content-slider', array($this, 'rrze_elements_content_slider'));

        add_shortcode('alert', array($this, 'rrze_elements_shortcode_alert'));
        add_shortcode('button', array($this, 'rrze_elements_button'));

    }

    public static function rrze_elements_enqueue_styles() {
        if (is_404()|| is_search())
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
                || has_shortcode($post->post_content, 'notice-faubox')
                || has_shortcode($post->post_content, 'collapsibles')
                || has_shortcode($post->post_content, 'accordion')
                || has_shortcode($post->post_content, 'accordionsub')
                || has_shortcode($post->post_content, 'collapse')
                || has_shortcode($post->post_content, 'rrze_elements_content_slider')
                || has_shortcode($post->post_content, 'alert')
                || has_shortcode($post->post_content, 'button')) {
            if (!wp_style_is('fontawesome') || !wp_style_is('font-awesome')) {
                wp_enqueue_style('fontawesome', $plugin_url . 'css/font-awesome.css');
            }
            wp_enqueue_style('rrze-elements', $plugin_url . 'css/rrze-elements.css');
        }

        if ($post && (has_shortcode($post->post_content, 'collapsibles')
                || has_shortcode($post->post_content, 'accordion')
                || has_shortcode($post->post_content, 'accordionsub')
                || has_shortcode($post->post_content, 'collapse'))) {
            if ($this->checkThemes(array('FAU-Themes', 'RRZE-Theme')) === false) {
                //wp_enqueue_script('rrze-accordions');
                // wp_enqueue_script('rrze-accordions', $plugin_url . 'js/accordion.js', array('jquery'));
            }
            wp_enqueue_script('rrze-accordions', $plugin_url . 'js/accordion.js', array('jquery'));
            wp_localize_script('rrze-accordions', 'accordionToggle', array(
                'expand_all' => __('Alle öffnen', 'rrze-elements'),
                'collapse_all' => __('Alle schließen', 'rrze-elements'),
            ));
        }

        wp_enqueue_script('rrze-timeline', $plugin_url . 'js/jquery.timelinr-0.9.6.js', array('jquery'));


        if ($post && (has_shortcode($post->post_content, 'content-slider'))) {
            wp_enqueue_style('elements', $plugin_url . 'css/rrze-elements.css');
            wp_enqueue_script('jquery-flexslider', $plugin_url . 'js/jquery.flexslider-min.js', array('jquery'), '2.2.0', true);
            wp_enqueue_script('flexslider', $plugin_url . 'js/flexslider.js', array(), false, true);
        }
    }

    /* ---------------------------------------------------------------------------------- */
    /* Absatzklassen Shortcodes
    /*----------------------------------------------------------------------------------- */

    public function rrze_elements_absatzklasse($atts, $content = null, $tag) {
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
        
        if (isset($GLOBALS['collapsibles_count'])) {
            $GLOBALS['collapsibles_count'] ++;
        } else {
            $GLOBALS['collapsibles_count'] = 0;
        }
        $defaults = array('expand-all-link' => 'false');
        $args = shortcode_atts($defaults, $atts);
        $expand = esc_attr($args['expand-all-link']);

        $output = '';

        // if( count($tab_titles) ){
        $output .= '<div class="accordion" id="accordion-' . $GLOBALS['collapsibles_count'] . '">';
        if ($expand == "true") {
            $output .= '<button class="expand-all" data-status="closed">' . __('Alle öffnen', 'rrze-2015') . '</button>';
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
        $output .= '<h3 class="accordion-heading"><button class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-' . $GLOBALS['collapsibles_count'] . '" href="#collapse_' . $id . '">' . $title . '</button></h3>';
        $output .= '<div id="collapse_' . $id . '" class="accordion-body' . $addclass . '"' . $name . '>';
        $output .= '<div class="accordion-inner clearfix">';
        //$output .= $content;
        //$output .= wpautop(trim($content));
        $output .= do_shortcode(trim($content));
        //$output .= do_shortcode(wpautop(trim($content)));
        $output .= '</div></div>';  // .accordion-inner & .accordion-body
        $output .= '</div>';        // . accordion-group

        return $output;
    }

    /*
     * Pull-left / pull-right divs
     */

    public function rrze_elements_pull_left_right($atts, $content = null, $tag) {
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
            'link' => '1'
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
        $link = filter_var($link, FILTER_VALIDATE_BOOLEAN);

        // Code
        $args = array(
            'post_type' => $type,
            'posts_per_page' => $num,
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
                if ($link) {
                    $link_open = '<a href="' . get_permalink($id) . '">';
                    $link_close = '</a>';
                } else {
                    $link_open = '';
                    $link_close = '';
                }
                $output .= '<li>';
                $output .= '<h2>' . $link_open . get_the_title() . $link_close . '</h2>';
                $output .= $link_open . get_the_post_thumbnail($id, 'teaser-thumb', array('class' => 'attachment-teaser-thumb')) . $link_close;
                $output .= get_the_excerpt($id);
                $output .= '</li>';
            endwhile;
            $output .= '</ul>';
            $output .= '</div>';
        endif;
        wp_reset_postdata();

        return $output;
    }

    /*
     * Shortcode zum Einbinden von News
     */

    public function rrze_elements_news($atts, $content = null) {
        global $options;
        extract(shortcode_atts(array(
            'category' => '',
            'tag' => '',
            'number' => '',
            'days' => '',
            'id' => '',
            'hide' => '',
            'display' => '',
            'imgfloat' => 'left'
                        ), $atts));

        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'orderby' => 'date',
            'posts_per_page' => -1,
            'ignore_sticky_posts' => 1
        );

        if ($category != '') {
            $c_id = array();
            if (strpos($category, ',')) {
                $categories = explode(',', $category);
            } else {
                $categories[] = $category;
            }
            foreach ($categories as $_c) {
                $c_id[] = get_cat_ID($_c);
            }
            $args['cat'] = implode(',', $c_id);
        }

        if ($tag != '') {
            $t_id = array();
            if (strpos($tag, ',')) {
                $tags = explode(',', $tag);
            } else {
                $tags[] = $tag;
            }
            foreach ($tags as $_t) {
                $t_id[] = \get_term_by('name', $_t, 'post_tag')->term_id;
            }
            $args['tag__in'] = implode(',', $t_id);
        }

        if ($number != '' && is_numeric($number))
            $args['posts_per_page'] = $number;

        if ($days != '') {
            $startdate = date('Y-m-d', strtotime('-' . $days . ' days'));
            $date_elements = explode('-', $startdate);
            $date_query = array(
                'after' => array(
                    'year' => $date_elements[0],
                    'month' => $date_elements[1],
                    'day' => $date_elements[2],
                ),
            );
            $args['date_query'] = $date_query;
        }

        if ($id != '') {
            $args['post__in'] = $id;
        }
        $output = '';
        $imgfloat = ($imgfloat == 'right') ? 'float-right' : 'float-left';
        $the_query = new \WP_Query($args);
        if ($the_query->have_posts()) {
            if ($display == 'list') {
                $output .= '<ul class="rrze-elements-news">';
            } else {
                $output .= '<div class="rrze-elements-news">';
            }
            while ($the_query->have_posts()) {
                $the_query->the_post();
                $id = \get_the_ID();
                $title = \get_the_title();
                $permalink = \get_permalink();
                if ($display == 'list') {
                    $output .= '<li>';
                    if (strpos($hide, 'date') === false) {
                        $output .= \get_the_date('d.m.Y', $id) . ': ';
                    }
                    $output .= '<a href="' . $permalink . '" rel="bookmark">' . $title . '</a>';
                    $output .= '</li>';
                } else {
                    $output .= '<article id="post-' . $id . '" class="' . implode(\get_post_class(), ' ') . ' cf">';
                    if (has_post_thumbnail($id) && (strpos($hide, 'thumbnail') === false)) {
                        $output .= '<div class="entry-thumbnail ' . $imgfloat . '">' . \get_the_post_thumbnail($id, 'post-thumbnail') . '</div>';
                    }
                    $output .= '<header class="entry-header">';
                    $output .= '<h2 class="entry-title"><a href="' . $permalink . '" rel="bookmark">' . $title . '</a></h2>';
                    $output .= '</header>';
                    $output .= '<div class="entry-meta">';
                    if (strpos($hide, 'date') === false) {
                        $output .= '<div class="entry-date">' . \get_the_date('d.m.Y', $id) . '</div>';
                    }
                    if (strpos($hide, 'categories') === false) {
                        $categories = \get_the_category($id);
                        $separator = " / ";
                        $cat_links = array();
                        if (!empty($categories)) {
                            foreach ($categories as $category) {
                                $cat_links[] = '<a href="' . esc_url(get_category_link($category->term_id)) . '" alt="' . esc_attr(sprintf(__('View all posts in %s', 'rrze-elements'), $category->name)) . '">' . esc_html($category->name) . '</a>';
                            }
                            $output .= '<div class="entry-cats">' . implode($separator, $cat_links) . '</div>';
                        }
                    }
                    $output .= '</div>';
                    $output .= '<div class="entry-content">' . \get_the_excerpt($id) . "</div>";
                    $output .= '</article>';
                }
            }
            if ($display == 'list') {
                $output .= '</ul>';
            } else {
                $output .= '</div>';
            }
            /* Restore original Post Data */
            wp_reset_postdata();
        } else {
            ?>
            <p><?php $output = __('Keine Beiträge gefunden', 'rrze-2015'); ?></p>
            <?php
        }
        return $output;
    }

    /*
     * Alerts
     */

    function rrze_elements_shortcode_alert($atts, $content = null) {
        extract(shortcode_atts(array(
            'style' => '',
            'color' => '',
            'border_color' => '',
            'font' => 'dark'), $atts));

        $style = (in_array($style, array('success', 'info', 'warning', 'danger'))) ? ' alert-' . $style : '';
        $color = ((substr($color, 0, 1) == '#') && (in_array(strlen($color), array(4, 7)))) ? 'background-color:' . $color . ';' : '';
        $border_color = ((substr($border_color, 0, 1) == '#') && (in_array(strlen($border_color), array(4, 7)))) ? ' border:1px solid' . $border_color . ';' : '';
        $font = ($font == 'light') ? ' color: #fff;letter-spacing: 0.03em;' : '';

        if ('' != $color || '' != $border_color || '' != $font) {
            $style = '';
        }

        return '<div class="alert' . $style . '" style="' . $color . $border_color . $font . '">' . do_shortcode(($content)) . '</div>';
    }

    /* ----------------------------------------------------------------------------------- */
    /* Buttons Shortcodes
      /*----------------------------------------------------------------------------------- */

    function rrze_elements_button($atts, $content = null) {
        extract(shortcode_atts(array(
            'link' => '#',
            'target' => '',
            'color' => '',
            'border_color' => '',
            'size' => '',
            'width' => '',
            'style' => '',
            'font' => '',
                        ), $atts));
        //var_dump($font);
        $style = (in_array($style, array('success', 'info', 'warning', 'danger', 'primary'))) ? ' ' . $style . '-btn' : '';
        $color_hex = '';
        $color_name = '';
        if ((substr($color, 0, 1) == '#') && (in_array(strlen($color), array(4, 7)))) {
            $color_name = '';
            $color_hex = 'background-color:' . $color . ';';
            $style = 'X';
        }
        if (in_array($color, array('red', 'yellow', 'blue', 'green', 'grey', 'black'))) {
            $color_name = ' ' . $color . '-btn';
            $style = 'Y';
        }
        
        $border_color = ((substr($border_color, 0, 1) == '#') && (in_array(strlen($border_color), array(4, 7)))) ? ' border:1px solid' . $border_color . ';' : '';

        $size = ($size) ? ' ' . $size . '-btn' : '';
        $target = ($target == 'blank') ? ' target="_blank"' : '';
        $link = esc_url($link);
        $font = ($font == 'dark') ? ' color: #1a1a1a;' : '';
        if ($width == 'full') {
            $width_full = ' full-btn';
            $width_px = '';
        } elseif (is_numeric($width)) {
            $width_px = 'width:' . $width . 'px; max-width:100%;"';
            $width_full = '';
        } else {
            $width_px = '';
            $width_full = '';
        }
        if ('' != $color || '' != $font) {
            $style = '';
        }

        $out = '<a' . $target . ' class="standard-btn' . $color_name . $size . $width_full . $style . '" href="' . $link . '" style="' . $font . $color_hex . $width_px . $border_color . '"><span>' . do_shortcode($content) . '</span></a>';

        return $out;
    }

    /* ---------------------------------------------------------------------------------- */
    /* Helper Functions
    /*----------------------------------------------------------------------------------- */

    public function checkThemes($themes = array('FAU-Themes', 'RRZE 2015')) {
        // statt alle FAU-Themes einzeln aufzuzählen...
        $fau_themes = array('FAU-Einrichtungen', 'FAU-Natfak', 'FAU-Philfak', 'FAU-RWFak', 'FAU-Techfak', 'FAU-Medfak');
        if (in_array('FAU-Themes', $themes)) {
            $themes = array_merge($themes, $fau_themes);
        }
        $current_theme = wp_get_theme();
        if (!in_array($current_theme, $themes)) {
            return false;
        } else {
            return true;
        }
    }
}