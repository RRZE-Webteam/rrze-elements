<?php

namespace RRZE\Elements\ContentSlider;

defined('ABSPATH') || exit;

use RRZE\Elements\Main;

/**
 * [ContentSlider description]
 */
class ContentSlider
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        add_shortcode('content-slider', [$this, 'shortcodeContentSlider']);
        add_shortcode('text-slider', [$this, 'shortcodeContentSlider']);
        add_shortcode('text-slider-item', [$this, 'shortcodeTextSliderItem']);

        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    /**
     * [shortcodeContentSlider description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @param  string $tag     [description]
     * @return string          [description]
     */
    public function shortcodeContentSlider($atts, $content='', $tag)
    {
        global $post;
        $output = '';

        if ($tag == 'text-slider') {
            $output .= '<div class="content-slider flexslider clear clearfix"><ul class="slides">'
                    . do_shortcode($content)
                    . '</ul></div>';
        } else {
            extract(shortcode_atts([
                "id" => '',
                "type" => 'post',
                "number" => '-1',
                "category" => '',
                "tag" => '',
                'orderby' => 'date', // 'rand' auch möglich!
                'link' => '1',
                'img_width' => '',
                'img_height' => '300',
                'format' => '',
                'start' => 'play',
            ], $atts, 'content-slider'));

            $id         = sanitize_text_field($id);
            $ids        = explode(",", $id);
            $ids        = array_map('trim', $ids);
            $type       = (in_array(sanitize_text_field($type), array(
                'post',
                'page',
                'speaker',
                'talk'
            ))) ? sanitize_text_field($type) : '';
            $orderby    = sanitize_text_field($orderby);
            $img_width  = (is_numeric($img_width) ? $img_width . 'px' : '');
            $img_height = (is_numeric($img_height) ? $img_height . 'px' : '');
            if ($img_width != '' && $img_height == '') {
                $img_height = 'auto';
            }
            if ($img_height != '' && $img_width == '') {
                $img_width = 'auto';
            }
            if ($orderby == 'random') {
                $orderby = 'rand';
            }
            $img_style = ($img_width != '' || $img_height != '') ? ' width:' . $img_width . '; height:' . $img_height . '; object-fit: cover;' : '';

            $cat  = sanitize_text_field($category);
            $tag  = sanitize_text_field($tag);
            $num  = sanitize_text_field($number);
            $link = filter_var($link, FILTER_VALIDATE_BOOLEAN);
            $format  = sanitize_text_field($format);
            $start = ($start == 'pause' ? 'pause' : '');

            $args = [
                'post_type'           => $type,
                'posts_per_page'      => $num,
                'orderby'             => $orderby,
                'post__not_in'        => [ $post->ID ],
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
                        'field'    => 'slug',
                        'terms'    => $_c,
                    );
                }
            } else {
                if ($cat != '') {
                    $args['category_name'] = $cat;
                }
                if ($tag != '') {
                    $args['tag'] = $tag;
                }
            }

            $the_query = new \WP_Query($args);
            $theme = wp_get_theme();
            switch ($theme->get( 'Name' )) {
                case 'RRZE 2019':
                    $default_image = plugins_url('rrze-elements/assets/img/default-thumbnail-rrze-2019.png');
                    break;
                case 'FAU Events':
                    $default_image = plugins_url('rrze-elements/assets/img/default-thumbnail-rrze-2019.png');
                    break;
                default:
                    $default_image = plugins_url('rrze-elements/assets/img/default-thumbnail-fau-themes.gif');
                    break;
            }

            if ($the_query->have_posts()) {
                $output .= "<div class=\"content-slider flexslider clear clearfix $format\">"
                        . '<ul class="slides">';
                while ($the_query->have_posts()) {
                    $the_query->the_post();
                    $id = get_the_ID();
                    if ($link) {
                        $link_open  = '<a href="' . get_permalink($id) . '">';
                        $link_close = '</a>';
                    } else {
                        $link_open  = '';
                        $link_close = '';
                    }
                    $output .= '<li>';
                    if ($format == 'carousel') {
                        if (has_post_thumbnail($id)) {
                            $image = get_the_post_thumbnail($id, 'medium', ['class' => 'attachment-teaser-thumb']);
                        } else {
                            $image = '<img src="' . $default_image . '" alt="" width="480" height="320">';
                        }
                        $output .= '<div class="image-container">'. $link_open . $image . $link_close . '</div>'
                            . '<div class="content-container">'
                            . '<p class="posted-on">' . get_the_date() . '</p>'
                            . '<h2>' . $link_open . get_the_title() . $link_close . '</h2>'
                            . '<div class="post-categories cat-links news-meta-categories">' . get_the_category_list(' | ') . '</div>'
                            . '</div>';
                    } else {
                        if (function_exists('fau_display_news_teaser')) {
                            $output .= fau_display_news_teaser($id);
                        } else {
                            $output .= '<h2>' . $link_open . get_the_title() . $link_close . '</h2>';
                            $output .= $link_open . get_the_post_thumbnail($id, 'medium_large', array(
                                    'class' => 'attachment-teaser-thumb',
                                    'style' => $img_style
                                )) . $link_close;
                            $output .= get_the_excerpt($id);
                        }
                    }
                    $output .= '</li>';
                }
                $output .= '</ul>';
                $output .= '</div>';
            }

            wp_reset_postdata();
        }

        $localizeScript = array(
            'start' => $start,
        );
        wp_localize_script( 'rrze-flexslider', 'object_name', $localizeScript );

        wp_enqueue_style('fontawesome');
        wp_enqueue_style('rrze-elements');
        wp_enqueue_script('jquery-flexslider');
        wp_enqueue_script('rrze-flexslider');

        return $output;
    }

    /**
     * [shortcodeTextSliderItem description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeTextSliderItem($atts, $content = '')
    {
        extract(shortcode_atts([
            'name' => ''
        ], $atts));

        $output = '';
        $output .= "<li>";
        $output .= do_shortcode($content);
        $output .= "</li>";

        return $output;
    }

    /**
     * [enqueueScripts description]
     * @return void
     */
    public function enqueueScripts()
    {
        wp_register_script(
            'jquery-flexslider',
            plugins_url('assets/js/jquery.flexslider.min.js', plugin_basename(__FILE__)),
            ['jquery'],
            '2.7.2',
            true
        );
        wp_register_script(
            'rrze-flexslider',
            plugins_url('assets/js/rrze-flexslider.min.js', plugin_basename(__FILE__)),
            ['jquery-flexslider'],
            '1.0.0',
            true
        );
    }
}
