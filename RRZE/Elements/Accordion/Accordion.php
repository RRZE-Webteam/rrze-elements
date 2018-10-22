<?php

namespace RRZE\Elements\Accordion;

use RRZE\Elements\Main;

defined('ABSPATH') || exit;

class Accordion {

    protected $main;

    public function __construct(Main $main) {
        $this->main = $main;

        add_shortcode('collapsibles', [$this, 'shortcode_collapsibles']);
        add_shortcode('accordion', [$this, 'shortcode_collapsibles']);
        add_shortcode('accordionsub', [$this, 'shortcode_collapsibles']);
        add_shortcode('collapse', [$this, 'shortcode_collapse']);
        add_shortcode('accordion-item', [$this, 'shortcode_collapse']);

        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    function shortcode_collapsibles($atts, $content = '') {

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
        if ($expand == "true") {
            $output .= '<div class="button-container-right"><button class="expand-all standard-btn primary-btn xsmall-btn" data-status="closed">' . __('Expand All', 'rrze-elements') . '</button></div>';
        }
        $output .= do_shortcode($content);
        $output .= '</div>';

        return $output;
    }

    public function shortcode_collapse($atts, $content = '') {
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

        $output .= do_shortcode(trim($content));

        $output .= '</div></div>';  // .accordion-inner & .accordion-body
        $output .= '</div>';        // . accordion-group

        wp_enqueue_style('fontawesome');
        wp_enqueue_style('rrze-elements');
        wp_enqueue_script('rrze-accordions');

        return $output;
    }

    public function enqueue_scripts() {
        wp_register_script('rrze-accordions', plugins_url('js/accordion.js', $this->main->plugin_basename), ['jquery']);
        wp_localize_script('rrze-accordions', 'accordionToggle', [
            'expand_all' => __('Expand All', 'rrze-elements'),
            'collapse_all' => __('Collapse All', 'rrze-elements'),
        ]);
    }

}
