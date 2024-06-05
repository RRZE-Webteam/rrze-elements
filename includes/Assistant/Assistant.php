<?php

namespace RRZE\Elements\Assistant;

defined('ABSPATH') || exit;

/**
 * [Assistant description]
 */
class Assistant
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        add_shortcode('assistant', [$this, 'assistant']);
        add_action('wp_enqueue_scripts', ['RRZE\Elements\Accordion\Accordion', 'enqueueScripts']);
    }

    /**
     * [shortcodePullLeftRight description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @param  string $tag     [description]
     * @return string          [description]
     *
     */
    public function assistant( $atts, $content = null) {
        global $post;
        $atts = shortcode_atts( [
            'id' => '',
            'color' => '',
            'expand-all-link' => 'false',
            'subpages' => 'true'
        ], $atts);
        array_walk($atts, 'sanitize_text_field');
        $showSubpages = $atts['subpages'] == 'true' ? true : false;

        $return = '';
        $pages = get_pages(array('sort_order' => 'ASC', 'sort_column' => 'menu_order', 'parent' => $atts['id'], 'hierarchical' => 0));
        $i = 0;
        $shortcode_data = '';
        foreach($pages as $page) {
            // avoid self-reference
            if ($page->ID == $post->ID) {
                continue;
            }

            $inner = '';
            $subpages = get_pages(array('sort_order' => 'ASC', 'sort_column' => 'menu_order', 'parent' => $page->ID, 'hierarchical' => 0));

            if($showSubpages && count($subpages) > 0)  {
                $inner .= '<div class="assistant-tabs">';

                $inner .= '<ul class="assistant-tabs-nav">';

                $j = 0;
                foreach($subpages as $subpage) {
                    if($j == 0) $class = 'active';
                    else $class = '';

                    $inner .= '<li><a href="#accordion-'.$page->ID.'-'.$i.'-tab-'.$j.'" class="accordion-tabs-nav-toggle '.$class.'">'.$subpage->post_title.'</a></li>';
                    $j++;
                }
                $inner .= '</ul>';

                $j = 0;
                foreach($subpages as $subpage) {
                    if($j == 0) $class = 'assistant-tab-pane-active';
                    else $class = '';
                    $inner .= '<div class="assistant-tab-pane '.$class.'" id="accordion-'.$page->ID.'-'.$i.'-tab-'.$j.'">';
                    $inner .= do_shortcode($subpage->post_content);
                    $inner .= '</div>';
                    $j++;
                }
                $inner .= '</div>';
            }  else {
                $inner .= do_shortcode($page->post_content);
            }


            $collapse = sprintf('[collapse title="%1$s" color="%2$s"]%3$s[/collapse]', $page->post_title, $atts['color'], $inner);
            $shortcode_data .= do_shortcode($collapse);
            $i++;
        }
        $collapsibles = sprintf('[collapsibles expand-all-link="%1$s"]%2$s[/collapsibles]', $atts['expand-all-link'], $shortcode_data);
        $return .= do_shortcode($collapsibles);

        wp_enqueue_style('rrze-elements');
        wp_enqueue_script('rrze-accordions');

        return $return;
    }
}
