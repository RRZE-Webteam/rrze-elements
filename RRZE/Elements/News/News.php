<?php

namespace RRZE\Elements\News;

defined('ABSPATH') || exit;

class News
{
    public function __construct()
    {
        add_shortcode('custom-news', [$this, 'shortcode_custom_news']);
    }

    public function shortcode_custom_news($atts)
    {
        global $options;
        extract(shortcode_atts([
            'category' => '',
            'tag' => '',
            'number' => '',
            'days' => '',
            'id' => '',
            'hide' => '',
            'display' => '',
            'imgfloat' => 'left'
        ], $atts));

        $args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'orderby' => 'date',
            'posts_per_page' => get_option('posts_per_page'),
            'ignore_sticky_posts' => 1
        ];

        if ($category != '') {
            $c_id = [];
            $categories = array_map('trim', explode(",", $category));
            foreach ($categories as $_c) {
                if ($category_id = get_cat_ID($_c)) {
                    $c_id[] = $category_id;
                }
            }
            $args['cat'] = implode(',', $c_id);
        }

        if ($tag != '') {
            $t_id = [];
            $tags = array_map('trim', explode(",", $tag));
            foreach ($tags as $_t) {
                if ($term_id = get_term_by('name', $_t, 'post_tag')->term_id) {
                    $t_id[] = $term_id;
                }
            }
            $args['tag__in'] = implode(',', $t_id);
        }

        if ($posts_per_page = absint($number)) {
            $args['posts_per_page'] = $posts_per_page;
        }

        if (absint($days)) {
            $now = current_time('timestamp');
            $timestamp = strtotime('-' . $days . ' days', $now);
            if ($timestamp) {
                $startdate = date('Y-m-d', $timestamp);
                $date_elements = explode('-', $startdate);
                $date_query = [
                    'after' => [
                        'year' => $date_elements[0],
                        'month' => $date_elements[1],
                        'day' => $date_elements[2],
                    ],
                ];
                $args['date_query'] = $date_query;
            }
        }

        if ($id != '') {
            $args['post__in'] = array_map(
                function ($post_ID) {
                    return absint(trim($post_ID));
                }, explode(",", $id)
            );
        }

        $output = '';
        $imgfloat = ($imgfloat == 'right') ? 'float-right' : 'float-left';
        $wp_query = new \WP_Query($args);

        $hide_ary = array_map('trim', explode(",", $hide));
        $hide_date = in_array('date', $hide_ary);
        $hide_category = in_array('category', $hide_ary);
        $hide_thumbnail = in_array('thumbnail', $hide_ary);

        if ($wp_query->have_posts()) {
            if ($display == 'list') {
                $output .= '<ul class="rrze-elements-news">';
            } else {
                $output .= '<div class="rrze-elements-news">';
            }

            while ($wp_query->have_posts()) {
                $wp_query->the_post();
                $id = get_the_ID();
                $title = get_the_title();
                $permalink = get_permalink();

                if ($display == 'list') {
                    $output .= '<li>';
                    if (! $hide_date) {
                        $output .= get_the_date('d.m.Y', $id) . ': ';
                    }
                    $output .= '<a href="' . $permalink . '" rel="bookmark">' . $title . '</a>';
                    $output .= '</li>';
                } else {
                    $stylesheets = [
                        'fau' => [
                            'FAU-Einrichtungen',
                            'FAU-Einrichtungen-BETA',
                            'FAU-Medfak',
                            'FAU-RWFak',
                            'FAU-Philfak',
                            'FAU-Techfak',
                            'FAU-Natfak',
                        ],
                        'rrze' => [
                            'rrze-2015',
                        ],
                        'fau-events' => [
                            'FAU-Events',
                        ],
                    ];
                    $current_theme = wp_get_theme();
                    if (in_array($current_theme->Name, $stylesheets['fau']) && function_exists('fau_display_news_teaser')) {
                        $output .= fau_display_news_teaser($id, ! $hide_date, 2, $hide_category);
                    } elseif (in_array($current_theme->Name, $stylesheets['fau-events'])) {
                        $output .= get_template_part('content');
                    } elseif (in_array($current_theme->Name, $stylesheets['rrze'])) {
                        $output .= get_template_part('template-parts/content');
                    } else {
                        $output .= '<article id="post-' . $id . '" class="news-item clear clearfix ' . implode(get_post_class(), ' ') . ' cf">';
                        $output .= '<header class="entry-header">';
                        $output .= '<h2 class="entry-title"><a href="' . $permalink . '" rel="bookmark">' . $title . '</a></h2>';
                        $output .= '</header>';
                        $output .= '<div class="entry-meta">';
                        if (! $hide_date) {
                            $output .= '<div class="entry-date">' . get_the_date('d.m.Y', $id) . '</div>';
                        }
                        if (! $hide_category) {
                            $categories = get_the_category($id);
                            $separator = " / ";
                            $cat_links = [];
                            if (!empty($categories)) {
                                foreach ($categories as $category) {
                                    $cat_links[] = '<a href="' . esc_url(get_category_link($category->term_id)) . '" alt="' . esc_attr(sprintf(__('View all posts in %s', 'rrze-elements'), $category->name)) . '">' . esc_html($category->name) . '</a>';
                                }
                                $output .= '<div class="entry-cats">' . implode($separator, $cat_links) . '</div>';
                            }
                        }
                        $output .= '</div>';
                        if (has_post_thumbnail($id) && ! $hide_thumbnail) {
                            $output .= '<div class="entry-thumbnail ' . $imgfloat . '">' . get_the_post_thumbnail($id, 'post-thumbnail') . '</div>';
                        }
                        $output .= '<div class="entry-content">' . get_the_excerpt($id) . "</div>";
                        $output .= '</article>';
                    }
                }
            }

            if ($display == 'list') {
                $output .= '</ul>';
            } else {
                $output .= '</div>';
            }

            wp_reset_postdata();
        } else {
            ?>
            <p><?php $output = __('No posts found', 'rrze-elements'); ?></p>
            <?php
        }

        wp_enqueue_style('fontawesome');
        wp_enqueue_style('rrze-elements');

        return $output;
    }
}
