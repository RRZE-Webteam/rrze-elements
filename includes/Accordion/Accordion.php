<?php

namespace RRZE\Elements\Accordion;

defined('ABSPATH') || exit;

/**
 * [Accordion description]
 */
class Accordion
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        add_shortcode('collapsibles', [$this, 'shortcodeCollapsibles']);
        add_shortcode('accordion', [$this, 'shortcodeCollapsibles']);
        add_shortcode('accordionsub', [$this, 'shortcodeCollapsibles']);
        add_shortcode('collapse', [$this, 'shortcodeCollapse']);
        add_shortcode('accordion-item', [$this, 'shortcodeCollapse']);

        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    /**
     * [shortcodeCollapsibles description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeCollapsibles($atts, $content = '')
    {
        if (isset($GLOBALS['collapsibles_count'])) {
            $GLOBALS['collapsibles_count'] ++;
        } else {
            $GLOBALS['collapsibles_count'] = 0;
        }

        $defaults = array('expand-all-link' => 'false');
        $args = shortcode_atts($defaults, $atts);
        $expand = esc_attr($args['expand-all-link']);

        $output = '';

        $output .= '<div class="accordion" id="accordion-' . $GLOBALS['collapsibles_count'] . '">';
        if ($expand == "true" || $expand == "1") {
            $output .= '<div class="button-container-right"><button class="expand-all standard-btn primary-btn xsmall-btn" data-status="closed">' . __('Expand All', 'rrze-elements') . '</button></div>';
        }
        $output .= do_shortcode($content);
        $output .= '</div>';

        return $output;
    }

    /**
     * [shortcodeCollapse description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeCollapse($atts, $content = '')
    {
        if (!isset($GLOBALS['current_collapse'])) {
            $GLOBALS['current_collapse'] = 0;
        } else {
            $GLOBALS['current_collapse'] ++;
        }

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
        $output .= '<h3 class="accordion-heading"><button class="accordion-toggle" data-toggle="collapse" href="#collapse_' . $id . '">' . $title . '</button></h3>';
        $output .= '<div id="collapse_' . $id . '" class="accordion-body' . $addclass . '"' . $name . '>';
        $output .= '<div class="accordion-inner clearfix">';

        $output .= do_shortcode($content);

        $output .= '</div></div>';  // .accordion-inner & .accordion-body
        $output .= '</div>';        // . accordion-group

        wp_enqueue_style('fontawesome');
        wp_enqueue_style('rrze-elements');
        wp_enqueue_script('rrze-accordions');

        return $output;
    }

    /**
     * [enqueueScripts description]
     * @return void
     */
    public function enqueueScripts()
    {
        wp_register_script(
            'rrze-accordions',
            plugins_url('assets/js/rrze-accordion.min.js', plugin_basename(__FILE__)),
            ['jquery'],
            '1.0.0'
        );
        wp_localize_script(
            'rrze-accordions',
            'accordionToggle',
            [
                'expand_all' => __('Expand All', 'rrze-elements'),
                'collapse_all' => __('Collapse All', 'rrze-elements'),
            ]
        );
    }
}
