<?php

namespace RRZE\Elements\ContentIndex;

defined('ABSPATH') || exit;

/**
 * [ContentIndex description]
 */
class ContentIndex
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        add_action('wp_loaded', [$this, 'elementsEnablePageTax']);
        add_shortcode('content-index', [$this, 'shortcodeContentIndex']);
        add_filter('manage_pages_columns', [$this, 'elementsAddCatColumn']);
        add_action('manage_pages_custom_column', [$this, 'elementsAddCatValue'], 10, 2);
        if (!post_type_supports('page', 'excerpt')) {
            add_post_type_support('page', 'excerpt');
        }
        $this->page_cat = 'page_category';
        $this->page_tag = 'page_tag';
    }

    /**
     * [elementsEnablePageTax description]
     * @return void
     */
    public function elementsEnablePageTax()
    {
        $existingTax = get_object_taxonomies('page');

        if (!in_array($this->page_cat, $existingTax)) {
            $labels_cat = [];
            $args_cat = [
                'labels' => $labels_cat,
                'hierarchical' => true,
                'rewrite' => false,
            ];
            register_taxonomy($this->page_cat, 'page', $args_cat);
        }

        if (!in_array($this->page_tag, $existingTax)) {
            $labels_tag = [];
            $args_tag = [
                'labels' => $labels_tag,
                'rewrite' => false,
            ];
            register_taxonomy( $this->page_tag, 'page', $args_tag );
        }
    }

    /**
     * [shortcodeContentIndex description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeContentIndex($atts, $content = '')
    {
        $output = '';

        $defaults = [
            'category-name' => $this->page_cat,
            'tag-name' => $this->page_tag,
            'show' => 'page',
            'category' => '',
            'tag' => '',
            'display' => '',
            'excerpt' => '0',
            'accordion-color' => '',
            'register' => '0',
            'prefix' => '',
            'expand-all-link' => '0',
            'hstart' => '2'
                        ];
        $sc_args = shortcode_atts($defaults, $atts);
        $show = (post_type_exists(sanitize_text_field($sc_args['show']))) ? sanitize_text_field($sc_args['show']) : 'page_tag';
        $category_name = sanitize_text_field($sc_args['category-name']);
        $category = sanitize_text_field($sc_args['category']);
        $tag_name = sanitize_text_field($sc_args['tag-name']);
        $tag = sanitize_text_field($sc_args['tag']);
        $display = (sanitize_text_field($sc_args['display']) == 'list') ? 'list' : '';
        $excerpt = ($sc_args['excerpt'] == '1') ? true : false;
        $accordion_color = sanitize_text_field($sc_args['accordion-color']);
        $register = ($sc_args['register'] == '1') ? true : false;
        $prefix = ($sc_args['prefix'] != '') ? sanitize_title($sc_args['prefix']) . '_' : '';
        $expand = ($sc_args['expand-all-link'] == '1') ? '1' : '0';
        $hstart = intval($sc_args['hstart']);
        $hsecond = $hstart + 1;
        $list_categories_ordered = [];

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
            $pages = [];
            foreach ($the_query->posts as $_post) {
                $page_cats = (get_the_terms($_post->ID, $category_name));
                $page_tags = (get_the_terms($_post->ID, $tag_name));
                $pages[$_post->ID]['title'] = $_post->post_title;
                $pages[$_post->ID]['url'] = get_permalink($_post->ID);
                $pages[$_post->ID]['excerpt'] = $_post->post_excerpt;
                if ($page_cats) {
                    foreach ($page_cats as $pc) {
                        if (is_array($pc) && array_key_exists('invalid_taxonomy', $pc)) {
                            return $pc['invalid_taxonomy'][0];
                        }
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
                $alphabet = range('A', 'Z');
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
                    $output .= '<h'.$hstart.'><a name="' . $prefix . $index . '"></a>' . $index . '</h'.$hstart.'>';
                }

                foreach ($group as $cat) {
                    $page_list = '';
                    foreach ($pages as $page) {
                        if (isset($page['cats']) && in_array($cat->slug, $page['cats'])) {
                            $page_list .= '<li><a href="' . $page['url'] . '">' . $this->escBrackets($page['title']) . '</a>';
                            $page_list .= $excerpt ? '<br>' . $this->escBrackets($page['excerpt']) : '';
                            $page_list .= '</li>';
                        }
                    }
                    if (strlen($page_list)>0) {
                        $page_list = '<ul>' . $page_list . '</ul>';
                    }

                    if (strlen($page_list) > 0) {
                        if ($display == 'list') {
                            $output .= '<h'.$hsecond.'>' . $cat->name . '</h'.$hsecond.'>' . $page_list;
                        } else {
                            $collapse = sprintf('[collapse title="%1$s" color="%2$s"]%3$s[/collapse]', $cat->name, $accordion_color, $page_list);
                            $shortcode_data .= do_shortcode($collapse);
                        }
                    }
                }
                if ($display != 'list') {
                    $collapsibles = sprintf('[collapsibles expand-all-link="%1$s" hstart="%3$s"]%2$s[/collapsibles]', $expand, $shortcode_data, $hstart);
                    $output .= do_shortcode($collapsibles);
                }
            }
            $output .= '</div>';
        }

        wp_reset_postdata();

        wp_enqueue_style('rrze-elements');
        return $output;
    }

    /**
     * [escBrackets description]
     * @param  string $content [description]
     * @return string          [description]
     */
    protected function escBrackets($content = '')
    {
        return str_replace(["[" , "]"], ["&#91;" , "&#93;"], $content);
    }

    /**
     * [elementsAddCatColumn description]
     * @param  array $cols [description]
     * @return array       [description]
     */
    public static function elementsAddCatColumn($cols)
    {
        $cols['category'] = __('Category', 'rrze-elements');
        return $cols;
    }

    /**
     * [elementsAddCatValue description]
     * @param  string $column_name [description]
     * @param  integer $post_id     [description]
     * @return void
     */
    public function elementsAddCatValue($column_name, $post_id)
    {
        if ('category' == $column_name) {
            $page_cats = (get_the_terms($post_id, $this->page_cat));
            $cats = [];
            if ($page_cats) {
                foreach ($page_cats as $_pc) {
                    $cat_link = '<a href="'.get_term_link($_pc->term_id).'">'.$_pc->name.'</a>';
                    array_push($cats, $cat_link);
                }
                echo implode("<br />", $cats);
            }
        }
    }
}
