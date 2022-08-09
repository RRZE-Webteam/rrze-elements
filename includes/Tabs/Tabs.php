<?php

namespace RRZE\Elements\Tabs;

defined('ABSPATH') || exit;

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
     * [shortcodeCollapsibles description]
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
        ];
        $args = shortcode_atts($defaults, $atts);
		$color = in_array($args['color'], ['primary', 'fau', 'zuv', 'phil', 'nat', 'med', 'rw', 'tf']) ? $args['color'] : '';
		if ($color == 'fau' || $color == 'zuv') {
			$color = 'zentral';
	    }

		$output = '<div class="rrze-elements-tabs ' . $color . '">';

	    preg_match_all('(title="(.*?)")',$content, $matches);
	    $titles = array_filter($matches[1], function($value) { return $value !== ''; });
	    if (!empty($titles)) {
		    $output .= '<ul role="tablist" class="tablist clear clearfix hide-in-print">';
			$tabIndex = 'aria-selected="true"';
		    foreach ($titles as $title) {
				$slug = sanitize_title($title);
			    $output .= '<li role="presentation"><a role="tab" '. $tabIndex .' href="#'.$slug.'" id="tab_'.$slug.'">' . $title . '</a></li>';
		        $tabIndex = 'tabindex="-1"';
			}
		    $output .= '</ul>';
	    }

        $output .= do_shortcode($content);
        $output .= '</div>';

        if (isset($GLOBALS['tabs_id'])) {
            $GLOBALS['tabs_id'] --;
        }

        return $output;
    }

    /**
     * [shortcodeCollapse description]
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

        $defaults = array('title' => 'Tab '.$GLOBALS['tabs_count'], 'color' => '', 'id' => '', 'load' => '', 'name' => '', 'icon' => '', 'suffix' => '');
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

        $icon_hmtl = '';
        if (!empty($icon)) {
            $icon_hmtl = "<span class=\"accordion-icon fa fa-$icon\" aria-hidden=\"true\"></span> " ;
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
		$output = '<section role="tabpanel" id="section_'.$slug.'" aria-labelledby="tab_'.$slug.'">';
	    $output .= '<h1 class="print-only">'.$title.'</h1>';
	    $output .= do_shortcode($content);
		$output .= '</section>';

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
            //plugins_url('assets/js/rrze-tabs.min.js', plugin_basename(__FILE__)),
            plugins_url('assets/js/rrze-tabs.js', plugin_basename(__FILE__)),
            ['jquery'],
            '1.23.1'
        );
    }
}
