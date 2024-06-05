<?php

namespace RRZE\Elements\CTA;

use RRZE\Elements\Alert\Alert;
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
        if ((!isset($atts['search']) || $atts['search'] == '') && (!isset($atts['url']) || $atts['url'] == '')) {
            return (new Alert)->shortcodeAlert(['style' => 'danger'], sprintf(__('%1$sURL missing.%2$s Please provide the %3$surl%4$s attribute in your CTA shortcode.', 'rrze-elements'), '<strong>', '</strong>', '<code>', '</code>'));
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
            'placeholder' => __('Search for...', 'rrze-elements'),
            'additional_link' => '',
            'additional_link_text' => '',
        ], $atts);
        $title = sanitize_text_field($atts['title']);
        $subtitle = sanitize_text_field($atts['subtitle']);
        $button = sanitize_text_field($atts['button']);
        $placeholder = sanitize_text_field($atts['placeholder']);
        $url = sanitize_url($atts['url']);
        $additionalLink = sanitize_url($atts['additional_link']);
        $additionalLinkText = $atts['additional_link_text'] != '' ? sanitize_text_field($atts['additional_link_text']) : $additionalLink;
        $styleClass = $atts['style'] == 'small' ? ' style-'.$atts['style'] : '';
        $bgClass = in_array($atts['background'], ['1', 'rrze']) ? ' bg-'.$atts['background'] : '';
        $image = sanitize_url($atts['image']);
        $imageID = attachment_url_to_postid($image); // returns 0 on failure
        $wrapperClass = $imageID != '0' ? ' has-image' : ' no-image';
        $wrapperClass .= $additionalLink != '' ? ' has-additional-link' : '';
        switch ((string)$atts['search']) {
            case '':
                $search = false;
                break;
            case 'true':
            case '1':
            case 'yes':
            case 'ja':
            case 'on':
                $search = 's'; // Backwards compatibility
                break;
            default:
                $search = sanitize_text_field($atts['search']);
        }
        $icon = sanitize_text_field($atts['icon']);
        if ($search && $icon == '') {
            $icon = 'magnifying-glass';
        }
        if ($icon != '') {
            $iconCB = new Icon($this->pluginFile);
            $iconOut = $iconCB->shortcodeIcon(['icon'=> $icon, 'style' => '2x']);
        } else {
            $iconOut = '';
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
            if ($url == '') $url = get_site_url();
            $rand = random_int(0, 999999);
            $output .= '<div class="cta-search-container">'
                . '<form itemprop="potentialAction" itemscope="" itemtype="https://schema.org/SearchAction" role="search" aria-label="' . sprintf(__('Search on %s', 'rrze-elements'), $url) . '" method="get" class="cta-search searchform" action="' . trailingslashit($url) . '">'
                . '<label for="cta_search_' . $rand . '">' . sprintf(__('Please enter the search term for searching on %s', 'rrze-elements'), $url) . ':</label>'
                . '<meta itemprop="target" content="' . trailingslashit($url) . '?' . $search . '={' . $search . '}">'
                . '<input itemprop="query-input" id="' . $rand . '" type="text" value="" name="' . $search . '" placeholder="' . $placeholder . '" required>'
                . '<button type="submit" enterkeyhint="search" value="">' . $iconOut . '<span class="sr-only">' . __('Find', 'rrze-elements') . '</span></button>'
                . '</form>';
            if ($additionalLink != '') {
                $output .= '<div class="extended-search-link"><a href="' . $additionalLink . '" class="standard-btn primary-btn xsmall-btn">' . $additionalLinkText . '</a></div>';
            }
            $output .= '</div>';
        } else {
            $output .= '<div class="cta-button-container"><a href="' . $url . '" class="btn cta-button">' . $button . $iconOut . '</a></div>';
        }

        $output .= '</div>';
        return $output;
    }



}