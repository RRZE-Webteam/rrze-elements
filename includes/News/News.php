<?php

namespace RRZE\Elements\News;

defined('ABSPATH') || exit;

use function RRZE\Elements\Config\getThemeGroup;

/**
 * [News description]
 */
class News
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        add_shortcode('custom-news', [$this, 'shortcodeCustomNews']);
        add_shortcode('blogroll', [$this, 'shortcodeCustomNews']);
    }

    /**
     * [shortcodeCustomNews description]
     * @param  array $atts [description]
     * @return string       [description]
     */
    public function shortcodeCustomNews($atts) {
        global $options;
        $sc_atts = shortcode_atts([
            'category' => '',
            'tag' => '',
            'number' => '10',
            'days' => '',
            'id' => '',
            'hide' => '',
            'display' => '',
            'imgfloat' => 'left',
            'title' => '',
            'has_thumbnail' => 'false',
            'columns' => '',
            'type' => '',
            // aus FAU-Einrichtungen
            'cat'	=> '',
            'num'	=> '',
            'divclass'	=> '',
            'hidemeta'	=> 'false',
            'hstart'	=> 2,
            'hideduplicates'	=> 'false',
            'fau_settings'  => 'false',
        ], $atts);
        $sc_atts = array_map('sanitize_text_field', $sc_atts);

        $cat = ($sc_atts['cat'] != '') ? $sc_atts['cat'] : $sc_atts['category'];
        $tag = $sc_atts['tag'];
        $num = ($sc_atts['num'] != '') ? intval($sc_atts['num']) : intval($sc_atts['number']);
        $days = intval($sc_atts['days']);
        $hide = array_map('trim', explode(",", $sc_atts['hide']));
        $display = $sc_atts['display'] == 'list' ? 'list' : '';
        $imgfloat = ($sc_atts['imgfloat'] == 'right') ? 'float-right' : 'float-left';
        $hstart = intval($sc_atts['hstart']);
        $divclass = esc_attr($sc_atts['divclass']);
        $hideMeta = $sc_atts['hidemeta'] == 'true' ? true : false;
        $title = esc_attr($sc_atts['title']);
        $hasThumbnail = $sc_atts['has_thumbnail'] == 'true' ? true : false;
        $columns = absint($sc_atts['columns']);
        $type = esc_attr($sc_atts['type']);
        $mode = array_map('trim', explode(",", $type));
        $thumbnailSize = 'post-thumbnail';
        $hideDuplicates = $sc_atts['hideduplicates'] == 'true' ? true : false;

        $borderTop = '';
        if ($sc_atts['fau_settings'] == 'true') {
            array_push($mode, 'img_first','ili_mode','show_more');
            $hideMeta = true;
            $borderTop = '1px solid #036';
        }

        $postCols = [];
        if ($columns > 0) {
            $scColumnsOpen = '[columns number='.$columns.']';
            $scColumnsClose = '[/columns]';
        } else {
            $scColumnsOpen = '';
            $scColumnsClose = '';
            foreach ($mode as $v) {
                if (substr($v,0, 5) == 'cols_') {
                    $colsPart = explode('_', $v);
                    $tmpPostCols = explode('-', $colsPart[1]);
                    if (count($tmpPostCols) < 2) {
                        continue;
                    }
                    $postCols['left'] = $tmpPostCols[0];
                    $postCols['right'] = $tmpPostCols[1];
                    $divclass .= ' post-cols';
                }
            }
        }

        if (in_array('ili_mode', $mode)) {
            $divclass .= ' ili-tpl';
        }
        if ($borderTop != '') {
            $divclass .= ' border-top';
        }

        $imgFirst = (in_array('img_first', $mode)) ? true : false;

        if ($sc_atts['id'] != '') {
            $id = array_map(
                function ($post_ID) {
                    return absint(trim($post_ID));
                },
                explode(",", $sc_atts['id'])
            );
        } else {
            $id = [];
        }

        $args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'orderby' => 'date',
            'posts_per_page' => $num,
            'ignore_sticky_posts' => 1
        ];

        if ($cat != '') {
            $c_id = [];
            $categories = array_map('trim', explode(",", $cat));
            foreach ($categories as $_c) {
                if ($cat_id = get_cat_ID($_c)) {
                    $c_id[] = $cat_id;
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

        if ($posts_per_page = absint($num)) {
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

        if (!empty($id)) {
            $args['post__in'] = $id;
        }

        if($hideDuplicates && isset($GLOBALS['a_displayedPosts']) && is_array($GLOBALS['a_displayedPosts'])) {
        	$args['post__not_in'] = array_unique($GLOBALS['a_displayedPosts']);
        }

        if ($hasThumbnail) {
            $args['meta_query'] = [
                [
                    'key'     => '_thumbnail_id',
                    'value'   => '',
                    'compare' => '!=',
                ]
            ];
        }

        $hide_date = in_array('date', $hide);
        if ($hideMeta) {
            $hide[] = 'category';
            $hide[] = 'date';
        }

        $titleText = '';
        $titleHtml = '';
        switch ($title) {
            case 'category':
                if ($cat != '') {
                    $catNames = [];
                    foreach ($categories as $category) {
                        if ($catObj = get_term_by('slug', $category, 'category')) {
                            $catNames[] = $catObj->name;
                        }
                    }
                    $titleText = implode(' | ', $catNames);
                }
                break;
            case 'tag':
                if ($tag != '') {
                    $tagNames = [];
                    foreach ($tags as $onetag) {
                        if ($tagObj = get_term_by('name', $onetag, 'post_tag')) {
                            $tagNames[] = $tagObj->name;
                        }
                    }
                    $titleText = implode(' | ', $tagNames);
                }
                break;
            case '':
                break;
            default:
                $titleText = $title;
                break;
        }

        if ($titleText != '') {
            $titleHtml = '<h'.$hstart.' class="section-title">'.$titleText.'</h'.$hstart.'>';
            $hstart++;
        }

        $moreLink = '';
        if (in_array('show_more', $mode)) {
            if ($cat != '') {
                $moreLink = '<p class="more-posts"><a href="'.get_category_link($c_id[0]).'">' . __('Weitere Artikel', 'rrze-elements') . '</a></p>';
            } elseif ($tag != '') {
                $moreLink = '<p class="more-posts"><a href="'.get_tag_link($t_id[0]).'">' . __('Weitere Artikel', 'rrze-elements') . '</a></p>';
            }
        }

        $output = '';

        $wp_query = new \WP_Query($args);

        if ($wp_query->have_posts()) {

            if ($display == 'list') {
                $output .= $titleHtml . '<ul class="rrze-elements-news">';
            } else {
                $output .= '<section class="rrze-elements-news blogroll ' . $divclass . '">' . $titleHtml . $moreLink . $scColumnsOpen;
            }

            while ($wp_query->have_posts()) {
                $wp_query->the_post();
                $id = get_the_ID();
                $title = get_the_title();
                $permalink = get_permalink();
                $GLOBALS['a_displayedPosts'][] = $id;

                if ($display == 'list') {
                    $output .= '<li>';
                    if (! $hide_date) {
                        $output .= get_the_date('d.m.Y', $id) . ': ';
                    }
                    $output .= '<a href="' . $permalink . '" rel="bookmark">' . $title . '</a>';
                    $output .= '</li>';
                } else {
                    if ($columns > 0) {
                        if ($columns <= 3 || $wp_query->post_count <= 3) {
                            $thumbnailSize = 'large';
                        }
                        $output .= do_shortcode('[column]' . $this->display_news_teaser($id, $hide, $hstart, $imgfloat, $imgFirst, $postCols, $thumbnailSize) . '[/column]');
                    } elseif (!empty($postCols)) {
                        if (array_sum($postCols) / $postCols['left'] > .3) {
                            $thumbnailSize = 'large';
                        }
                        $output .= do_shortcode($this->display_news_teaser($id, $hide, $hstart, $imgfloat, $imgFirst, $postCols, $thumbnailSize));
                    } else {
                        switch (getThemeGroup(get_stylesheet())) {
                            case 'fau':
                                if (function_exists('fau_display_news_teaser')) {
                                    $output .= do_shortcode('[column]' . fau_display_news_teaser($id, !$hide_date, $hstart, $hideMeta) . '[/column]');
                                } else {
                                    $output .= do_shortcode('[column]' . $this->display_news_teaser($id, $hide, $hstart, $imgfloat) . '[/column]');
                                }
                                break;
                            case 'rrze':
                                if (function_exists('rrze_display_news_teaser')) {
                                    $output .= rrze_display_news_teaser($id, $hide, $hstart, $imgfloat);
                                } else {
                                    $output .= $this->display_news_teaser($id, $hide, $hstart, $imgfloat);
                                }
                                break;
                            case 'events':
                            default:
                                $output .= $this->display_news_teaser($id, $hide, $hstart, $imgfloat, $imgFirst, $postCols);
                        }
                    }
                }
            }

            if ($display == 'list') {
                $output .= '</ul>';
            } else {
                $output .= $scColumnsClose . '</section>';
            }

            wp_reset_postdata();
        } else {
            ?>
            <p><?php $output = __('No posts found.', 'rrze-elements'); ?></p>
            <?php
        }

        wp_enqueue_style('fontawesome');
        wp_enqueue_style('rrze-elements');

        wp_reset_postdata();
        return do_shortcode($output);
    }

    private function display_news_teaser($id = 0, $hide = [], $hstart = 2, $imgfloat = 'float-left', $imgFirst = false, $postCols = [], $thumbnailSize = 'post-thumbnail') {
        if ($id == 0) return;

        $arialabelid= "aria-".$id."-".random_int(10000,30000);
        $hide_date = in_array('date', $hide);
        $hide_category = in_array('category', $hide);
        $hide_thumbnail = in_array('thumbnail', $hide);
        $columns = (!empty($postCols));
        if ($columns) {
            $imgFirst = true;
            $numCols = array_sum($postCols);
        }
        if (has_post_thumbnail($id) && ! $hide_thumbnail) {
            $image_data = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), $thumbnailSize );
            $ratioClass = $image_data[2] > $image_data[1] ? 'ratio-portrait' : 'ratio-landscape';
        }
        if (function_exists('fau_create_schema_publisher')) {
            $schemaPublisher = fau_create_schema_publisher();
        } else {
            $custom_logo_id = get_theme_mod( 'custom_logo' );
            $logo = wp_get_attachment_image_url( $custom_logo_id);
            $logo = $logo ? $logo : '';
            $schemaPublisher = '<div itemprop="publisher" itemscope itemtype="https://schema.org/Organization"><meta itemprop="name" content="'.get_bloginfo('name').'"/><meta itemprop="logo" content="'.$logo.'"/></div>';
        }

        $output = '<article id="post-' . $id . '" class="news-item clear clearfix ' . implode(' ', get_post_class()) . ' cf" aria-labelledby="'.$arialabelid.'" itemscope itemtype="http://schema.org/NewsArticle">';
        if ($columns) {
           $output .= '[columns number="'. $numCols .'"][column span="' . $postCols['left'] . '"]';
        }

        if (has_post_thumbnail($id) && ! $hide_thumbnail && $imgFirst) {
            $output .= '<div class="entry-thumbnail ' . $ratioClass . ' ' . $imgfloat . '">' . get_the_post_thumbnail($id, $thumbnailSize)
                . '<meta itemprop="image" content="'.get_the_post_thumbnail_url($id).'">'
                . '</div>';
        }
        if ($columns) {
            $output .= '[/column][column span="' . $postCols['right'] . '"]';
        }
        $output .= '<header class="entry-header">';
        $output .= '<h'.$hstart.' class="entry-title" id="'.$arialabelid.'" itemprop="headline"><a href="' . get_permalink() . '" rel="bookmark" itemprop="url">' . get_the_title() . '</a></h'.$hstart.'>';
        $output .= '</header>';
        $output .= '<div class="entry-meta">';
        $output .= $schemaPublisher;
        $output .= '<div itemprop="author" itemscope itemtype="https://schema.org/Person"><meta itemprop="name" content="'.get_the_author().'"/></div>';
        if (! $hide_date) {
            $output .= '<div class="entry-date" itemprop="datePublished" content="'.get_the_date('Y-m-d').'">' . get_the_date(get_option('date_format'), $id) . '</div>';
        } else {
            $output .= '<div><meta itemprop="datePublished" content="'.get_the_date('Y-m-d').'"></div>';
        }
        if (! $hide_category) {
            $categories = get_the_category($id);
            $separator = " / ";
            $cat_links = [];
            if (!empty($categories)) {
                foreach ($categories as $cat) {
                    $cat_links[] = '<a href="' . esc_url(get_category_link($cat->term_id)) . '" alt="' . esc_attr(sprintf(__('View all posts in %s', 'rrze-elements'), $cat->name)) . '">' . esc_html($cat->name) . '</a>';
                }
                $output .= '<div class="entry-cats">' . implode($separator, $cat_links) . '</div>';
            }
        }
        $output .= '</div>';
        if (has_post_thumbnail($id) && ! $hide_thumbnail && !$imgFirst) {
            $output .= '<div class="entry-thumbnail ' . $ratioClass . ' ' . $imgfloat . '">' . get_the_post_thumbnail($id, 'post-thumbnail')
                . '<meta itemprop="image" content="'.get_the_post_thumbnail_url($id).'">'
                . '</div>';
        }

        // Content
        $abstract = get_post_meta( $id, 'abstract', true );
        if (strlen(trim($abstract))<3) {
            if (function_exists('fau_custom_excerpt')) {
                $abstract = fau_custom_excerpt($id, get_theme_mod('default_anleser_excerpt_length'),false,'',true, get_theme_mod('search_display_excerpt_morestring'));
                if (function_exists('fau_create_readmore')) {
                    $abstract .= fau_create_readmore(get_permalink(), get_the_title(), false, true);
                }
            } else {
                $abstract = get_the_excerpt($id);
            }
        } else {
            if (function_exists('fau_create_readmore')) {
                $abstract .= fau_create_readmore(get_permalink(), get_the_title(), false, true);
            }
        }
        $output .= '<div class="entry-content" itemprop="description">' . $abstract . '</div>';

        if ($columns) {
            $output .= '[/column][/columns]';
        }

        $output .= '</article>';

        return do_shortcode($output);
    }
}
