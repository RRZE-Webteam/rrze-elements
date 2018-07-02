<?php

namespace RRZE\Elements\ContentSlider;

use RRZE\Elements\Main;

defined('ABSPATH') || exit;

class ContentSlider {

    protected $main;

    public function __construct(Main $main) {
        $this->main = $main;

        add_shortcode('content-slider', [$this, 'shortcode_content_slider']);
        
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function shortcode_content_slider($atts) {
        global $post;

        extract(shortcode_atts([
            "id" => '',
            "type" => 'post',
            "number" => '-1',
            "category" => '',
            "tag" => '',
            'orderby' => 'date', // 'rand' auch möglich!
            'link' => '1'
        ], $atts, 'content-slider'));

        $id = sanitize_text_field($id);
        $ids = explode(",", $id);
        $ids = array_map('trim', $ids);
        $type = sanitize_text_field($type);
        $orderby = sanitize_text_field($orderby);

        if ($orderby == 'random') {
            $orderby = 'rand';
        }

        $cat = sanitize_text_field($category);
        $tag = sanitize_text_field($tag);
        $num = sanitize_text_field($number);
        $link = filter_var($link, FILTER_VALIDATE_BOOLEAN);

        $args = [
            'post_type' => $type,
            'posts_per_page' => $num,
            'orderby' => $orderby,
            'post__not_in' => [$post->ID],
            'ignore_sticky_posts' => 1
        ];

        if (strlen($id) > 0) {
            $args['post__in'] = $ids;
        }

        if ($type == 'speaker' || $type == 'talk') {
            $cats = explode(',', $cat);
            $cats = array_map('trim', $cats);
            $args = array(
                'relation' => 'AND',
            );
            foreach ($cats as $_c) {
                $args['tax_query'][] = array(
                    'taxonomy' => $type . '_category',
                        'field' => 'slug',
                        'terms' => $_c,
                );
            }
        } else {
            if ($cat !='') {
                $args['category_name'] = $cat;
            }
            if ($tag !='') {
                $args['tag'] = $tag;
            }
        }

        $the_query = new \WP_Query($args);
        $output = '';

        if ($the_query->have_posts()) {
            $output = '<div class="content-slider flexslider">';
            $output .= '<ul class="slides">';

            while ($the_query->have_posts()) {
                $the_query->the_post();
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
                $output .= $link_open . get_the_post_thumbnail($id, 'teaser-thumb', ['class' => 'attachment-teaser-thumb']) . $link_close;
                $output .= get_the_excerpt($id);
                $output .= '</li>';
            }

            $output .= '</ul>';
            $output .= '</div>';
        }

        wp_reset_postdata();

        wp_enqueue_style('fontawesome');
        wp_enqueue_style('rrze-elements');
        wp_enqueue_script('jquery-flexslider');
        wp_enqueue_script('flexslider');

        return $output;
    }

    public function enqueue_scripts() {
        wp_register_script('jquery-flexslider', plugins_url('js/jquery.flexslider-min.js', $this->main->plugin_basename), ['jquery'], '2.2.0', true);
        wp_register_script('flexslider', plugins_url('js/flexslider.js', $this->main->plugin_basename), [], false, true);
    }

}
