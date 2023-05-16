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
        if (!isset($atts['button']) || $atts['button'] == '' || !isset($atts['url']) || $atts['url'] == '') {
            return do_shortcode('[alert style="danger"]' . sprintf(__('%1$sButton text and URL missing.%2$s Please provide the %3$sbutton%4$s and %3$surl%4$s attributes in your CTA shortcode.', 'rrze-elements'), '<strong>', '</strong>', '<code>', '</code>') . '[/alert]');
        }
        $atts = shortcode_atts([
            'title' => '',
            'subtitle' => '',
            'button' => '',
            'url' => '',
            'icon' => '',
            'background' => '',
            'image' => '',
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
        $bgClass = is_numeric($atts['background']) ? ' bg-'.$atts['background'] : '';
        $image = sanitize_url($atts['image']);
        $imageID = attachment_url_to_postid($image); // returns 0 on failure
        $wrapperClass = $imageID != '0' ? ' has-image' : ' no-image';

        $output = '<div class="rrze-elements-cta' . $wrapperClass . $bgClass . '"><div class="cta-content' . '">';
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
        $output .= '<div class="cta-button-container"><a href="' . $url . '" class="btn cta-button">' . $button . $iconOut . '</a></div>';

        $output .= '</div>';
        return $output;
    }



}