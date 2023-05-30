<?php

namespace RRZE\Elements\CTA;

use RRZE\Elements\Icon\Icon;

use function RRZE\Downloads\Shortcodes\downloads;
use function RRZE\Elements\Config\calculateContrastColor;

defined('ABSPATH') || exit;

/**
 * [Alert description]
 */
class CTA {

    protected $pluginFile;

    public function __construct($pluginFile) {
        add_shortcode('CTA', [$this, 'shortcodeCTA']);
        $this->pluginFile = $pluginFile;
    }

    public function shortcodeCTA($atts, $content = '') {
        if (/*!isset($atts['button']) || $atts['button'] == '' || */!isset($atts['url']) || $atts['url'] == '') {
            return do_shortcode('[alert style="danger"]' . sprintf(__('%1$sURL missing.%2$s Please provide the %3$surl%4$s attribute in your CTA shortcode.', 'rrze-elements'), '<strong>', '</strong>', '<code>', '</code>') . '[/alert]');
        }
        $atts = shortcode_atts([
            'title' => '',
            'subtitle' => '',
            'button' => '',
            'url' => '',
            'icon' => '',
            'background' => '',
            'image' => '',
            'search' => '',
            'style' => '',
        ], $atts);
        $title = sanitize_text_field($atts['title']);
        $subtitle = sanitize_text_field($atts['subtitle']);
        $button = sanitize_text_field($atts['button']);
        $url = sanitize_url($atts['url']);
        $icon = sanitize_text_field($atts['icon']);
        if ($icon != '') {
            $iconCB = new Icon($this->pluginFile);
            $iconOut = $iconCB->shortcodeIcon(['icon'=> $icon, 'style' => '2x']);
        } else {
            $iconOut = '';
        }
        $styleClass = $atts['style'] == 'small' ? ' style-'.$atts['style'] : '';
        $bgClass = in_array($atts['background'], ['1', 'rrze']) ? ' bg-'.$atts['background'] : '';
        $image = sanitize_url($atts['image']);
        $imageID = attachment_url_to_postid($image); // returns 0 on failure
        $wrapperClass = $imageID != '0' ? ' has-image' : ' no-image';
        switch ($atts['search']) {
            case '':
                $search = false;
                break;
            case '1':
                $search = 's'; // Backwards compatibility
                break;
            default:
                $search = sanitize_text_field($atts['search']);
        }
        $output = '<div class="rrze-elements-cta' . $wrapperClass . $bgClass . $styleClass . '"><div class="cta-content' . '">';
        if ($title != '') {
            $output .= '<span class="cta-title">' . $title . '</span>';
        }
        if ($subtitle != '') {
            $output .= '<span class="cta-subtitle">' . $subtitle . '</span>';
        }
        $output .= '</div>';
        if ($imageID != '0') {
            $output .= '<div class="cta-image">' . wp_get_attachment_image($imageID, 'large') . '</div>';
        }
        if ($search !== false) {
            if ($search == 1) $search = 's';
            $rand = random_int(0, 999999);
            $output .= '<div class="cta-search-container">'
                . '<form itemprop="potentialAction" itemscope="" itemtype="https://schema.org/SearchAction" role="search" aria-label="' . sprintf(__('Search on %s', 'rrze-elements'), $url) . '" method="get" class="cta-search searchform" action="' . trailingslashit($url) . '">'
                . '<label for="cta_search_' . $rand . '">' . sprintf(__('Please enter the search term for searching on %s', 'rrze-elements'), $url) . ':</label>'
                . '<meta itemprop="target" content="' . trailingslashit($url) . '?' . $search . '={' . $search . '}">'
                . '<input itemprop="query-input" id="' . $rand . '" type="text" value="" name="' . $search . '" placeholder="' . __('Search for...', 'rrze-elements') . '" required>'
                . '<button type="submit" enterkeyhint="search" value="">'.do_shortcode('[icon icon="magnifying-glass" color="#1f4c7a" style="2x"]').'<span class="sr-only">' . __('Find', 'rrze-elements') . '</span></button>'
                . '</form>'
                . '</div>';
        } else {
            $output .= '<div class="cta-button-container"><a href="' . $url . '" class="btn cta-button">' . $button . $iconOut . '</a></div>';
        }

        $output .= '</div>';
        return $output;
    }



}