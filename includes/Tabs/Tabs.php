<?php

namespace RRZE\Elements\Tabs;

use const RRZE\Elements\RRZE_ELEMENTS_VERSION;

defined( 'ABSPATH') || exit;

/**
 * [Tabs description]
 */
class Tabs
{
	/**
     * [__construct description]
     */
    public function __construct()
    {
        add_shortcode('tabs', [$this, 'shortcodeTabs']);
	    add_shortcode('tab', [$this, 'shortcodeTab']);

        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    /**
     * [shortcodeTabs description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeTabs($atts, $content = '', $tag = '')
    {
        if (isset($GLOBALS['tabs_count'])) {
            $GLOBALS['tabs_count'] ++;
        } else {
            $GLOBALS['tabs_count'] = 0;
        }
        $GLOBALS['tabs_id'] = $GLOBALS['tabs_count'];

        $defaults = [
            'color' => 'primary',
            'hstart' => '2',
        ];
        $args = shortcode_atts($defaults, $atts);
        $color = in_array($args['color'], ['primary', 'fau', 'zuv', 'phil', 'nat', 'med', 'rw', 'tf']) ? $args['color'] : '';
        if ($color == 'fau' || $color == 'zuv') {
            $color = 'zentral';
        }
        $GLOBALS['tabs_hstart'] = is_numeric($args['hstart']) ? $args['hstart'] : $defaults['hstart'];

        $output = '<div class="rrze-elements-tabs ' . $color . '" id="tabs-' . $GLOBALS['tabs_id'] . '">';

        preg_match_all('(\[tab(.*?)\])',$content, $matchesTabs);
        $matchesTitles = [];
        $matchesIcons = [];
        $matchesSuffix = [];
        foreach ($matchesTabs[1] as $i => $tab) {
            preg_match('(title="(.*?)")',$tab, $matchesTitles[$i]);
            preg_match('(icon="(.*?)")',$tab, $matchesIcons[$i]);
            preg_match('(suffix="(.*?)")',$tab, $matchesSuffix[$i]);
        }

        $output .= '<div role="tablist" class="manual">';
        foreach ( $matchesTitles as $i => $matchesTitle ) {
            $title = $matchesTitle[1];
            $slug = sanitize_title($title);
            if (isset($matchesIcons[$i][1]) && $matchesIcons[$i][1] != '') {
                $icon = do_shortcode('[icon icon="'.$matchesIcons[$i][1].'"]');
            } else {
                $icon = '';
            }
            if (isset($matchesSuffix[$i][1]) && $matchesSuffix[$i][1] != '') {
                $suffix = '<span class="tab-suffix">' . $matchesSuffix[$i][1] . '</span>';
            } else {
                $suffix = '';
            }
            $output .= '<button id="tab-'.$GLOBALS['tabs_id'] . '_' . $slug.'" type="button" role="tab" aria-selected="true" aria-controls="tab-'.$GLOBALS['tabs_id'] . '_tabpanel_'.$slug.'">'
                . '<span class="focus" tabindex="-1">' . $icon . $title . $suffix . '</span>'
                . '</button>';
        }
        $output .= '</div>';

        $output .= do_shortcode($content);

        $output .= '</div>';

        if (isset($GLOBALS['tabs_id'])) {
            $GLOBALS['tabs_id'] --;
        }

        return $output;
    }

    /**
     * [shortcodeTab description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */

    public function shortcodeTab($atts, $content, $tag)
    {
        if (!isset($GLOBALS['current_tab'])) {
            $GLOBALS['current_tab'] = 0;
        } else {
            $GLOBALS['current_tab'] ++;
        }
        if (!isset($GLOBALS['tabs_count'])) {
            $GLOBALS['tabs_count'] = 0;
        }

        $defaults = array(
            'title' => 'Tab '.$GLOBALS['tabs_count'],
            'color' => '',
            'id' => '',
            'load' => '',
            'name' => '',
            'icon' => '',
            'suffix' => '');
        extract(shortcode_atts($defaults, $atts));

        $addclass = '';

        if (!isset($GLOBALS['tabs_id'])) {
            $GLOBALS['tabs_id'] = $GLOBALS['tabs_count'];
        }

        $title = esc_attr($title);
        $color = $color ? ' ' . esc_attr($color) : '';
        $load = $load ? ' ' . esc_attr($load) : '';
        //$dataname = $name ? 'data-name="' . esc_attr($name) . '"' : '';
        $name = $name ? ' name="' . esc_attr($name) . '"' : '';
        //$hlevel = 'h'.($GLOBALS['collapsibles_hstart'][$GLOBALS['tabs_id']] ?? '2');
        $icon = esc_attr($icon);
        $suffix = esc_attr($suffix);

        if (!empty($load)) {
            $addclass .= " " . $load;
        }

        $icon_html = '';
        if (!empty($icon)) {
            $icon_html .= do_shortcode('[icon icon="'.$icon.'"] ');
        }
        $suffix_hmtl = '';
        if (!empty($suffix)) {
            $suffix_hmtl = "<span class=\"accordion-suffix\">$suffix</span>" ;
        }

        $id = intval($id) ?: 0;
        if ($id < 1) {
            $id = $GLOBALS['current_tab'];
        }

        if ($GLOBALS['current_tab'] == 0) {

        }

        $slug = sanitize_title($title);
        $output = '<div id="tab-'.$GLOBALS['tabs_id'] . '_tabpanel_'.$slug.'" role="tabpanel" aria-labelledby="tab-'.$GLOBALS['tabs_id'] . '_' . $slug.'">';
        $output .= '<h' . $GLOBALS['tabs_hstart'] . ' class="print-only">'.$icon_html . $title. '</h' . $GLOBALS['tabs_hstart'] . '>';
        $output .= do_shortcode($content);
        $output .= '</div>';

        wp_enqueue_style('fontawesome');
        wp_enqueue_style('rrze-elements');
        wp_enqueue_script('rrze-tabs');

        return $output;
    }

    /**
     * [enqueueScripts description]
     * @return void
     */
    public static function enqueueScripts() {
        wp_register_script(
            'rrze-tabs',
            plugins_url('assets/js/rrze-tabs.min.js', plugin_basename(__FILE__)),
            //plugins_url('assets/js/rrze-tabs.js', plugin_basename(__FILE__)),
            ['jquery'],
            RRZE_ELEMENTS_VERSION
        );
    }
}
