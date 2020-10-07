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
    public function shortcodeCollapsibles($atts, $content = '', $tag)
    {
        if (isset($GLOBALS['collapsibles_count'])) {
            $GLOBALS['collapsibles_count'] ++;
        } else {
            $GLOBALS['collapsibles_count'] = 0;
        }

        $defaults = array('expand-all-link' => 'false', 'register' => 'false', 'icon' => '', 'suffix' => '');
        $args = shortcode_atts($defaults, $atts);
        $expand = esc_attr($args['expand-all-link']);
        $expand = (($expand == '1')||($expand == 'true')) ? true : false;
        $register = esc_attr($args['register']);
        $register = (($register == '1')||($register == 'true')) ? true : false;
        $icon = esc_attr($icon);
        $suffix = esc_attr($suffix);

        $output = '';

        $output .= '<div class="accordion" id="accordion-' . $GLOBALS['collapsibles_count'] . '">';
        if ($expand) {
            $output .= '<div class="button-container-right"><button class="expand-all standard-btn primary-btn xsmall-btn" data-status="closed">' . __('Expand All', 'rrze-elements') . '</button></div>';
        }
        if ($register) {
            preg_match_all('(name="(.*?)")',$content, $matches);
            $names = array_filter($matches[1], function($value) { return $value !== ''; });
            if (!empty($names)) {
                $output .= '<ul aria-hidden="true" class="accordion-register clear clearfix">';
                foreach ($names as $name) {
                    $output .= '<li><a href="#' . $name . '">' . $name . '</a></li>';
                }
                $output .= '</ul>';
            }
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
    public function shortcodeCollapse($atts, $content = '', $tag)
    {
        if (!isset($GLOBALS['current_collapse'])) {
            $GLOBALS['current_collapse'] = 0;
        } else {
            $GLOBALS['current_collapse'] ++;
        }

        $defaults = array('title' => 'Tab', 'color' => '', 'id' => '', 'load' => '', 'name' => '', 'icon' => '', 'suffix' => '');
        extract(shortcode_atts($defaults, $atts));

        $addclass = '';

        $title = esc_attr($title);
        $color = $color ? ' ' . esc_attr($color) : '';
        $load = $load ? ' ' . esc_attr($load) : '';
        $dataname = $name ? 'data-name="' . esc_attr($name) . '"' : '';
        $name = $name ? ' name="' . esc_attr($name) . '"' : '';
        $hlevel = 'h3';
        $icon = esc_attr($icon);
        $suffix = esc_attr($suffix);
        if ($tag == 'accordion-item') {
            $hlevel = 'h4';
        }

        if (!empty($load)) {
            $addclass .= " " . $load;
        }

        if (!empty($icon)) {
            $icon_hmtl = "<span class=\"accordion-icon fa fa-$icon\"></span> " ;
        }
        if (!empty($suffix)) {
            $suffix_hmtl = "<span class=\"accordion-suffix\">$suffix</span>" ;
        }

        $id = intval($id) ? intval($id) : 0;
        if ($id < 1) {
            $id = $GLOBALS['current_collapse'];
        }

        $output = '<div class="accordion-group' . $color . '">';
        $output .= "<$hlevel class=\"accordion-heading\"><button class=\"accordion-toggle\" data-toggle=\"collapse\" $dataname href=\"#collapse_$id\">$icon_hmtl $title $suffix_hmtl</button></$hlevel>";
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
            //plugins_url('assets/js/rrze-accordion.min.js', plugin_basename(__FILE__)),
            plugins_url('assets/js/rrze-accordion.js', plugin_basename(__FILE__)),
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
