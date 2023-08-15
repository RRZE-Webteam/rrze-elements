<?php

namespace RRZE\Elements\Accordion;

use const RRZE\Elements\RRZE_ELEMENTS_VERSION;

defined( 'ABSPATH') || exit;

/**
 * [Accordion description]
 */
class Accordion
{
	protected $pluginFile;

	/**
     * [__construct description]
     */
    public function __construct($pluginFile)
    {
        add_shortcode('collapsibles', [$this, 'shortcodeCollapsibles']);
        add_shortcode('accordion', [$this, 'shortcodeCollapsibles']);
        add_shortcode('accordionsub', [$this, 'shortcodeCollapsibles']);
        add_shortcode('collapse', [$this, 'shortcodeCollapse']);
        add_shortcode('accordion-item', [$this, 'shortcodeCollapse']);

        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueScriptTranslations'], 100);

		$this->pluginFile = $pluginFile;
    }

    /**
     * [shortcodeCollapsibles description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeCollapsibles($atts, $content = '', $tag = '')
    {
        if (isset($GLOBALS['collapsibles_count'])) {
            $GLOBALS['collapsibles_count'] ++;
        } else {
            $GLOBALS['collapsibles_count'] = 0;
        }
        $GLOBALS['collapsibles_id'] = $GLOBALS['collapsibles_count'];

        $defaults = array('expand-all-link' => 'false', 'register' => 'false', 'hstart' => '');
        $args = shortcode_atts($defaults, $atts);
        $expand = esc_attr($args['expand-all-link']);
        $expand = (($expand == '1')||($expand == 'true')) ? true : false;
        $register = esc_attr($args['register']);
        $register = (($register == '1')||($register == 'true')) ? true : false;
        if ($args['hstart'] != '') {
            $hstart = intval($args['hstart']);
        } else {
            $hstart = 2;
        }
        if (($hstart < 1) || ($hstart > 6)) {
            $hstart = 2;
        }
        $GLOBALS['collapsibles_hstart'][$GLOBALS['collapsibles_id']] = $hstart;
        $output = '<div class="accordion" id="accordion-' . $GLOBALS['collapsibles_count'] . '">';
        if ($expand) {
            switch (get_post_meta(get_the_ID(), 'fauval_langcode', true)) {
                case 'en':
                    $expandText = 'Expand All';
                    break;
                case 'de':
                    $expandText = 'Alle öffnen';
                    break;
                default:
                    $expandText = __('Expand All', 'rrze-elements');
            }
            $output .= '<div class="button-container-right"><button class="expand-all standard-btn primary-btn xsmall-btn" data-status="closed">' . $expandText . '</button></div>';
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

        if (isset($GLOBALS['collapsibles_id'])) {
            $GLOBALS['collapsibles_id'] --;
        }

        return wpautop($output);
    }

    /**
     * [shortcodeCollapse description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeCollapse($atts, $content, $tag)
    {
        if (!isset($GLOBALS['current_collapse'])) {
            $GLOBALS['current_collapse'] = 0;
        } else {
            $GLOBALS['current_collapse'] ++;
        }
        if (!isset($GLOBALS['collapsibles_count'])) {
            $GLOBALS['collapsibles_count'] = 0;
        }

        $defaults = array('title' => 'Tab', 'color' => '', 'id' => '', 'load' => '', 'name' => '', 'icon' => '', 'suffix' => '');
        extract(shortcode_atts($defaults, $atts));

        $addclass = '';

        if (!isset($GLOBALS['collapsibles_id'])) {
            $GLOBALS['collapsibles_id'] = $GLOBALS['collapsibles_count'];
        }

        $title = strip_tags($title, ['<br>', '<br />']);
        $color = $color ? ' ' . esc_attr($color) : '';
        $load = $load ? ' ' . esc_attr($load) : '';
        $dataname = $name ? 'data-name="' . esc_attr($name) . '"' : '';
        $name = $name ? ' name="' . esc_attr($name) . '"' : '';
        $hlevel = 'h'.($GLOBALS['collapsibles_hstart'][$GLOBALS['collapsibles_id']] ?? '2');
        $icon = esc_attr($icon);
        $suffix = esc_attr($suffix);

        if (!empty($load)) {
            $addclass .= " " . $load;
        }

        $icon_html = '';
        if (!empty($icon)) {
            $icon_html .= do_shortcode('[icon icon="'.$icon.'"]');
        }
        $suffix_hmtl = '';
        if (!empty($suffix)) {
            $suffix_hmtl = "<span class=\"accordion-suffix\">$suffix</span>" ;
        }

        $id = intval($id) ? intval($id) : 0;
        if ($id < 1) {
            $id = $GLOBALS['current_collapse'];
        }

        $output = '<div class="accordion-group' . $color . '">';
        $output .= "<$hlevel class=\"accordion-heading\"><span class=\"read-mode-only\">$title $suffix_hmtl</span><button class=\"accordion-toggle\" data-toggle=\"collapse\" $dataname href=\"#collapse_$id\">$icon_html $title $suffix_hmtl</button></$hlevel>";
        $output .= '<div id="collapse_' . $id . '" class="accordion-body' . $addclass . '"' . $name . '>';
        $output .= '<div class="accordion-inner clearfix">';

        $output .= wpautop(do_shortcode($content), false);

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
    public static function enqueueScripts() {
        wp_register_script(
            'rrze-accordions',
            plugins_url('assets/js/rrze-accordion.min.js', plugin_basename(__FILE__)),
            //plugins_url('assets/js/rrze-accordion.js', plugin_basename(__FILE__)),
            ['jquery', 'wp-i18n'],
            RRZE_ELEMENTS_VERSION
        );
    }

    public function enqueueScriptTranslations() {
        self::console_log(wp_set_script_translations('rrze-accordions', 'rrze-elements',plugin_dir_path( $this->pluginFile ) . 'languages/'));
    }

    public static function console_log($msg = '', $tsStart = 0) {
        if (isset($_GET['elements_debug'])) {
            //$msg .= ' execTime: ' . sprintf('%.2f', microtime(true) - $tsStart) . ' s';
            echo '<script>console.log(' . json_encode($msg, JSON_HEX_TAG) . ');</script>';
        }
    }
}
