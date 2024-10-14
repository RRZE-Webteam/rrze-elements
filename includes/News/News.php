<?php

namespace RRZE\Elements\News;

defined('ABSPATH') || exit;

use RRZE\Elements\Columns\Columns;
use RRZE\Elements\Helper;

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
        add_filter( 'the_seo_framework_query_supports_seo',  [$this, 'disableTSF']);
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
            'year' => '',
            'month' => '',
            'day' => '',
            // aus FAU-Einrichtungen
            'cat'	=> '',
            'num'	=> '',
            'divclass'	=> '',
            'hidemeta'	=> 'false',
            'hstart'	=> 2,
            'hideduplicates'	=> 'false',
            'hide_duplicates'	=> 'false',
            'fau_settings'  => 'false',
            'forcelandscape' => 'false',
            'force_landscape' => 'false',
            'sticky_only' => 'false',
            'teaser_length' => '55',
        ], $atts);
        $sc_atts = array_map('sanitize_text_field', $sc_atts);

        $cat = ($sc_atts['cat'] != '') ? $sc_atts['cat'] : $sc_atts['category'];
        $tag = $sc_atts['tag'];
        $num = ($sc_atts['num'] != '') ? intval($sc_atts['num']) : intval($sc_atts['number']);
        $days = intval($sc_atts['days']);
        $hide = array_map('trim', explode(",", $sc_atts['hide']));
        $display = ($sc_atts['display'] == 'list' || $sc_atts['display'] == 'table') ? $sc_atts['display'] : '';
        $imgfloat = ($sc_atts['imgfloat'] == 'right') ? 'float-right' : 'float-left';
        $hstart = intval($sc_atts['hstart']);
        $divclass = esc_attr($sc_atts['divclass']);
        $hideMeta = Helper::shortcode_boolean($sc_atts['hidemeta']);
        $title = esc_attr($sc_atts['title']);
        $hasThumbnail = Helper::shortcode_boolean($sc_atts['has_thumbnail']);
        $columns = absint($sc_atts['columns']);
        $type = esc_attr($sc_atts['type']);
        $mode = array_map('trim', explode(",", $type));
        $thumbnailSize = 'large';
        $hideDuplicates = !empty($sc_atts['hideduplicates']) ? $sc_atts['hideduplicates'] : $sc_atts['hide_duplicates'];
        $hideDuplicates = Helper::shortcode_boolean($hideDuplicates);
        $forceLandscape = !empty($sc_atts['forcelandscape']) ? $sc_atts['forcelandscape'] : $sc_atts['force_landscape'];
        $forceLandscape = Helper::shortcode_boolean($forceLandscape);
        $stickyOnly = Helper::shortcode_boolean($sc_atts['sticky_only']);
        $teaserLength = absint($sc_atts['teaser_length']);

        $borderTop = '';
        if (Helper::shortcode_boolean($sc_atts['fau_settings'])) {
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
        if ($stickyOnly === true) {
            $args['post__in'] = get_option( 'sticky_posts' );
        }

        $c_id = [];
        if ($cat != '') {
            $categories = array_map('trim', explode(",", $cat));
            foreach ($categories as $_c) {
                $cat_obj = get_category_by_slug($_c);
                if (!$cat_obj) {
                    // if slug not found -> try with cat name
                    $cat_id = get_cat_ID($_c);
                } else {
                    $cat_id = $cat_obj->term_id;
                }
                $c_id[] = $cat_id;
            }
            $args['cat'] = implode(',', $c_id);
        }

        if ($tag != '') {
            $t_id = [];
            $tags = array_map('trim', explode(",", $tag));
            foreach ($tags as $_t) {
                if ($term = get_term_by('name', $_t, 'post_tag')) {
                    $t_id[] = $term->term_id;
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

        if(absint($sc_atts['year'])) {
        	$date_query['year'] = $sc_atts['year'];
        	if(absint($sc_atts['month'])) {
	        	$date_query['month'] = $sc_atts['month'];
                if(absint($sc_atts['day'])) {
                    $date_query['day'] = $sc_atts['day'];
                }
            }
        	$args['date_query'] = $date_query;
        }

        if (!empty($id)) {
            $args['post__in'] = $id;
        }

        if($hideDuplicates && isset($GLOBALS['a_rrze_elements_displayed_posts']) && is_array($GLOBALS['a_rrze_elements_displayed_posts'])) {
        	$args['post__not_in'] = array_unique($GLOBALS['a_rrze_elements_displayed_posts']);
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
                $moreLink = '<div class="more-posts"><a class="standard-btn xsmall-btn primary-btn" href="'.get_category_link($c_id[0]).'" aria-label="' . __('More news', 'rrze-elements') . ': ' . $cat . '">' . __('More news', 'rrze-elements') . '</a></div>';
            } elseif ($tag != '') {
                $moreLink = '<div class="more-posts"><a class="standard-btn xsmall-btn primary-btn" href="'.get_tag_link($t_id[0]).'" aria-label="' . __('More news', 'rrze-elements') . ': ' . $tag . '">' . __('More news', 'rrze-elements') . '</a></div>';
            }
        }

        $output = '';

        $wp_query = new \WP_Query($args);

        if ($wp_query->have_posts()) {

            if ($display == 'list' || $display == 'table') {
                $output .= $titleHtml . '<ul class="rrze-elements-news' . ($display == 'table' ? ' news-table' : '') . '">';
            } else {
                $output .= '<section class="rrze-elements-news blogroll ' . $divclass . '">' . $titleHtml . $moreLink . $scColumnsOpen;
            }

            while ($wp_query->have_posts()) {
                $wp_query->the_post();
                $id = get_the_ID();
                $title = get_the_title();
                $permalink = get_permalink();
                $externalLink = get_post_meta($id, 'external_link', true);
                if (filter_var($externalLink, FILTER_VALIDATE_URL) !== false) {
                    $permalink = $externalLink;
                }
                $GLOBALS['a_rrze_elements_displayed_posts'][] = $id;
                $args = [];

                if ($display == 'list' || $display == 'table') {
                    $output .= '<li>';
                    if (! $hide_date) {
                        $output .= '<span class="news-date">' . get_the_date('d.m.Y', $id) . ': </span>';
                    }
                    $output .= '<a href="' . $permalink . '" rel="bookmark" class="news-title">' . $title . '</a>';
                    $output .= '</li>';
	            } else {
                    if ($columns > 0) {
                        if ($columns <= 3 || $wp_query->post_count <= 3) {
                            $thumbnailSize = 'large';
                        }
                        $args = [
                            'id' => $id,
                            'hide' => $hide,
                            'hstart' => $hstart,
                            'imgfloat' => $imgfloat,
                            'imgFirst' => $imgFirst,
                            'postCols' => $postCols,
                            'thumbnailSize' => $thumbnailSize,
                            'forceLandscape' => $forceLandscape,
                            'showContent' => (in_array('show_content', $mode) ? true : false),
                            'teaserLength' => $teaserLength,
                        ];
                        $output .= (new Columns())->shortcodeColumn([], $this->display_news_teaser($args));
                    } elseif (!empty($postCols)) {
                        if (array_sum($postCols) / $postCols['left'] > .3) {
                            $thumbnailSize = 'large';
                        }
                        $args = [
                            'id' => $id,
                            'hide' => $hide,
                            'hstart' => $hstart,
                            'imgfloat' => $imgfloat,
                            'imgFirst' => $imgFirst,
                            'postCols' => $postCols,
                            'thumbnailSize' => $thumbnailSize,
                            'forceLandscape' => $forceLandscape,
                            'showContent' => (in_array('show_content', $mode) ? true : false),
                            'teaserLength' => $teaserLength,
                        ];
                        $output .= do_shortcode($this->display_news_teaser($args));
                    } else {
                        switch (getThemeGroup(get_stylesheet())) {
                            case 'fau':
                                if (function_exists('fau_display_news_teaser')) {
                                    $output .= do_shortcode(fau_display_news_teaser($id, !$hide_date, $hstart, $hideMeta, true));
                                } else {
                                    $args = [
                                        'id' => $id,
                                        'hide' => $hide,
                                        'hstart' => $hstart,
                                        'imgfloat' => $imgfloat,
                                        'forceLandscape' => $forceLandscape,
                                        'showContent' => (in_array('show_content', $mode) ? true : false),
                                    ];
                                    $output .= do_shortcode($this->display_news_teaser($args));
                                }
                                break;
                            case 'rrze':
                                if (function_exists('rrze_display_news_teaser')) {
                                    $hide[] = 'caption';
                                    $output .= rrze_display_news_teaser($id, $hide, 1, $imgfloat, $teaserLength);
                                } else {
                                    $args = [
                                        'id' => $id,
                                        'hide' => $hide,
                                        'hstart' => $hstart,
                                        'imgfloat' => $imgfloat,
                                        'forceLandscape' => $forceLandscape,
                                        'showContent' => (in_array('show_content', $mode) ? true : false),
                                    ];
                                    $output .= $this->display_news_teaser($args);
                                }
                                break;
                            case 'events':
                            default:
                                $args = [
                                    'id' => $id,
                                    'hide' => $hide,
                                    'hstart' => $hstart,
                                    'imgfloat' => $imgfloat,
                                    'imgFirst' => $imgFirst,
                                    'postCols' => $postCols,
                                    'forceLandscape' => $forceLandscape,
                                    'showContent' => (in_array('show_content', $mode) ? true : false),
                                ];
                                $output .= $this->display_news_teaser($args);
                        }
                    }
                }
            }

            if ($display == 'list' || $display == 'table') {
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

        wp_enqueue_style('rrze-elements');

        wp_reset_postdata();
        return do_shortcode($output);
    }

    private function display_news_teaser($argsRaw) {
        $defaults = [
            'id'            => 0,
            'hide'          => [],
            'hstart'        => 2,
            'imgfloat'      => 'float-left',
            'imgFirst'      => false,
            'postCols'      => [],
            'thumbnailSize' => 'large',
            'forceLandscape'=> false,
            'teaserLength'  => 20,
            ];
        if ($argsRaw['id'] == 0) return '';
        $args = wp_parse_args($argsRaw, $defaults);
        foreach ($args as $k => $v) {
            ${$k} = $v;
        }
        $arialabelid= "aria-".$id."-".random_int(10000,30000);
        $hide_date = in_array('date', $hide);
        $hide_category = in_array('category', $hide);
        $hide_thumbnail = in_array('thumbnail', $hide);
        $columns = (!empty($postCols));
        if ($columns) {
            $imgFirst = true;
            $numCols = array_sum($postCols);
        }
        $permalink = get_permalink();
        $externalLink = get_post_meta($id, 'external_link', true);
        if (filter_var($externalLink, FILTER_VALIDATE_URL) !== false) {
            $permalink = $externalLink;
        }
        $displayThumbnail = false;
        if (has_post_thumbnail($id) && ! $hide_thumbnail && $image_data = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), $thumbnailSize )) {
            $displayThumbnail = true;
            if ($forceLandscape) {
                $ratioClass = 'ratio-landscape';
            } else {
                $ratioClass = $image_data[2] > $image_data[1] ? 'ratio-portrait' : 'ratio-landscape';
            }
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

        if ($displayThumbnail && $imgFirst && $image_data) {
            $output .= '<div class="entry-thumbnail ' . $ratioClass . ' ' . $imgfloat . '" aria-hidden="true" role="presentation">'
                . '<meta itemprop="image" content="'.get_the_post_thumbnail_url($id).'">'
                . '<a href="'.$permalink.'" tabindex="-1">'
                . get_the_post_thumbnail($id, $thumbnailSize)
                . '</a>'
                . '</div>';
        }
        if ($columns) {
            $output .= '[/column][column span="' . $postCols['right'] . '"]';
        }
        $output .= '<header class="entry-header">';
        $output .= '<h'.$hstart.' class="entry-title" id="'.$arialabelid.'" itemprop="headline"><a href="' . $permalink . '" rel="bookmark" itemprop="url">' . get_the_title() . '</a></h'.$hstart.'>';
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
        if ($displayThumbnail && !$imgFirst) {
            $output .= '<div class="entry-thumbnail ' . $ratioClass . ' ' . $imgfloat . '">' . get_the_post_thumbnail($id, $thumbnailSize)
                . '<meta itemprop="image" content="'.get_the_post_thumbnail_url($id).'">'
                . '</div>';
        }

		if(!in_array('teaser', $hide)) {
			// Content
            if ($args['showContent'] == true) {
                $abstract = get_the_content(null, false, $id);
            } else {
                $abstract = get_post_meta( $id, 'abstract', true );
                if (strlen(trim($abstract))<3) {
                    if (function_exists('fau_custom_excerpt')) {
                        $abstract = fau_custom_excerpt($id, get_theme_mod('default_anleser_excerpt_length'),false,'',true, get_theme_mod('search_display_excerpt_morestring'));
                        if (function_exists('fau_create_readmore')) {
                            $abstract .= fau_create_readmore($permalink, get_the_title(), false, true);
                        }
                    } else {
                        $excerpt = get_the_excerpt($id);
                        $excerpt_more = apply_filters( 'excerpt_more', '&hellip;' );
                        $abstract = wp_trim_words( $excerpt, $teaserLength, $excerpt_more );
                    }
                } else {
                    if (function_exists('fau_create_readmore')) {
                        $abstract .= fau_create_readmore($permalink, get_the_title(), false, true);
                    }
                }
            }
			$output .= '<div class="entry-content" itemprop="description">' . $abstract . '</div>';
		}
        if ($columns) {
            $output .= '[/column][/columns]';
        }

        $output .= '</article>';

        return do_shortcode($output);
    }

    /*
     * Disable TSF on pages containing 'hideduplicates="true"' because of counter issues
     * Cf. https://wordpress.org/support/topic/how-to-identify-pre-rendered-content/
     */
    public function disableTSF($supported) {
        if (!is_plugin_active('autodescription/autodescription.php') && !is_plugin_active_for_network('autodescription/autodescription.php')) {
            return true;
        }

        $content = get_the_content(null, false, get_the_ID());
        if (str_contains($content, 'hideduplicates="true"')) {
            return false;
        } else {
            return TRUE;
        }
    }
}
