<?php

namespace RRZE\Elements\News;

defined('ABSPATH') || exit;

class News {

    public function __construct() {
        add_shortcode('custom-news', [$this, 'shortcode_custom_news']);
    }

    public function shortcode_custom_news($atts) {
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
            'posts_per_page' => -1,
            'ignore_sticky_posts' => 1
        ];

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
                $t_id[] = get_term_by('name', $_t, 'post_tag')->term_id;
            }
            $args['tag__in'] = implode(',', $t_id);
        }

        if ($number != '' && is_numeric($number)) {
            $args['posts_per_page'] = $number;
        }

        if ($days != '') {
            $startdate = date('Y-m-d', strtotime('-' . $days . ' days'));
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
                $id = get_the_ID();
                $title = get_the_title();
                $permalink = get_permalink();

                if ($display == 'list') {
                    $output .= '<li>';
                    if (strpos($hide, 'date') === false) {
                        $output .= get_the_date('d.m.Y', $id) . ': ';
                    }
                    $output .= '<a href="' . $permalink . '" rel="bookmark">' . $title . '</a>';
                    $output .= '</li>';
                } else {
                    $stylesheets = [
			'fau'        => [
				'FAU-Einrichtungen',
				'FAU-Einrichtungen-BETA',
				'FAU-Medfak',
				'FAU-RWFak',
				'FAU-Philfak',
				'FAU-Techfak',
				'FAU-Natfak',
			],
			'rrze'       => [
				'rrze-2015',
			],
			'fau-events' => [
				'FAU-Events',
			],
                    ];
                    $current_theme = wp_get_theme();
                    if (in_array($current_theme->Name, $stylesheets['fau'])) {
                        $withdate = (strpos($hide, 'date') === false);
                        $hidemeta = strpos($hide, 'categories');
                        $output .= fau_display_news_teaser($id, $withdate, 2, $hidemeta);
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
                        if (strpos($hide, 'date') === false) {
                            $output .= '<div class="entry-date">' . get_the_date('d.m.Y', $id) . '</div>';
                        }
                        if (strpos($hide, 'categories') === false) {
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
                        if (has_post_thumbnail($id) && (strpos($hide, 'thumbnail') === false)) {
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

            /* Restore original Post Data */
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
