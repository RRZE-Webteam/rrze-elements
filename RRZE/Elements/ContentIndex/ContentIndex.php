<?php

namespace RRZE\Elements\ContentIndex;

use RRZE\Elements\Main;

defined('ABSPATH') || exit;

class ContentIndex {

    protected $main;

    public function __construct(Main $main) {
        $this->main = $main;
        add_action('init', [$this, 'elements_enable_page_tax']);
        add_shortcode('content-index', [$this, 'shortcode_contentindex']);
        if (!post_type_supports('page', 'excerpt')) {
            add_post_type_support('page', 'excerpt');
        }
    }

    function elements_enable_page_tax() {
        if (!taxonomy_exists('page_tag')) {
            $labels_tag = array();
            $args_tag = array(
                'labels' => $labels_tag,
                'rewrite' => false,
            );
            register_taxonomy('page_tag', 'page', $args_tag);
        }
        if (!taxonomy_exists('page_category')) {
            $labels_cat = array();
            $args_cat = array(
                'labels' => $labels_cat,
                'hierarchical' => true,
                'rewrite' => false,
            );
            register_taxonomy('page_category', 'page', $args_cat);
        }
    }

    function shortcode_contentindex($atts, $content = '') {
        $output = '';

        extract(shortcode_atts([
            'category_name' => 'page_category',
            'tag_name' => 'page_tag',
            'show' => 'page',
            'category' => '',
            'tag' => '',
            'display' => '',
            'excerpt' => '0',
            'accordion_color' => '',
            'register' => '0',
            'prefix' => '',
                        ], $atts));
        $excerpt = ($excerpt == '1') ? true : false;
        $register = ($register == '1') ? true : false;
        $accordion_color = sanitize_text_field($accordion_color);
        $prefix = ($prefix != '') ? sanitize_title($prefix) . '_' : '';

        // Query Args
        $args = [
            'post_status' => 'publish',
            'orderby' => 'date',
            'posts_per_page' => -1,
        ];

        if ($show != '') {
            $show = esc_attr($show);
            $args['post_type'] = $show;
        }
        if ($category != '') {
            if (strpos($category, ',')) {
                $categories = explode(',', $category);
            } else {
                $categories[] = $category;
            }
            $args['tax_query'][] = array(
                'taxonomy' => $category_name,
                'field' => 'slug',
                'terms' => $categories
            );
            foreach ($categories as $_c) {
                $list_categories[] = get_term_by('slug', trim($_c), $category_name);
            }
        } else {
            $list_categories = get_terms(['taxonomy' => $category_name, 'orderby' => 'name', 'order' => 'ASC']);
        }
        if ($register) {
            foreach ($list_categories as $_lc) {
                $list_categories_ordered[strtoupper(substr($_lc->name, 0, 1))][] = $_lc;
            }
        } else {
            foreach ($list_categories as $_lc) {
                $list_categories_ordered['no_reg'][] = $_lc;
            }
        }
        if ($tag != '') {
            if (strpos($tag, ',')) {
                $tags = explode(',', $tag);
            } else {
                $tags[] = $tag;
            }
            $args['tax_query'][] = array(
                'taxonomy' => $tag_name,
                'field' => 'slug',
                'terms' => $tags
            );
        }

        // Loop
        $the_query = new \WP_Query($args);
        if ($the_query->have_posts()) {
            // Build Page Array
            $pages = array();
            foreach ($the_query->posts as $_post) {
                $page_cats = (get_the_terms($_post->ID, $category_name));
                $page_tags = (get_the_terms($_post->ID, $tag_name));
                $pages[$_post->ID]['title'] = $_post->post_title;
                $pages[$_post->ID]['url'] = get_permalink($_post->ID);
                $pages[$_post->ID]['excerpt'] = $_post->post_excerpt;
                if ($page_cats) {
                    foreach ($page_cats as $pc) {
                        $pages[$_post->ID]['cats'][] = $pc->slug;
                    }
                }
                if ($page_tags) {
                    foreach ($page_tags as $pt) {
                        $pages[$_post->ID]['cats'][] = $pt->slug;
                    }
                }
            }
            usort($pages, function ($a, $b) {
                return $a['title'] <=> $b['title'];
            });

            // Build Output
            $output .= '<div class="content-index">';
            if ($register) {
                $alphabet = range( 'A', 'Z' );
                $output .= '<ul aria-hidden="true" class="tax-list-register clear clearfix">';
                foreach ($alphabet as $letter) {
                    if (array_key_exists($letter, $list_categories_ordered)) {
                        $output .= '<li><a href="#' . $prefix . $letter . '">' . $letter . '</a></li>';
                    } else {
                        $output .= '<li class="empty">' . $letter . '</li>';
                    }
                }
                $output .= '</ul>';
            }
            foreach ($list_categories_ordered as $index => $group) {
                $shortcode_data = '';
                if ($register) {
                    $output .= '<h2><a name="' . $prefix . $index . '"></a>' . $index . '</h2>';
                }
                foreach ($group as $cat) {
                    $page_list = '<ul>';
                    foreach ($pages as $page) {
                        if (isset($page['cats']) && in_array($cat->slug, $page['cats'])) {
                            $page_list .= '<li><a href="' . $page['url'] . '">' . $page['title'] . '</a>';
                            $page_list .= $excerpt ? ('<br />' . $page['excerpt']) : '';
                            $page_list .= '</li>';
                        }
                    }
                    $page_list .= '</ul>';
                    if ($display == 'list') {
                        $output .= '<h3>' . $cat->name . '</h3>' . $page_list;
                    } else {
                        $shortcode_data .= do_shortcode('[collapse title="' . $cat->name . '" color="' . $accordion_color . '"]' . $page_list . '[/collapse]');
                    }
                }
                if ($display != 'list') {
                    $output .= do_shortcode('[collapsibles]' . $shortcode_data . '[/collapsibles]');
                }
            }
            $output .= '</div>';
        }
        
        wp_enqueue_style('rrze-elements');
        return $output;
    }

}
