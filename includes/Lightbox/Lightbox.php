<?php

namespace RRZE\Elements\Lightbox;

defined('ABSPATH') || exit;

use DOMDocument;
use DOMXPath;

/**
 * [Lightbox description]
 */
class Lightbox
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_filter('the_content', [$this, 'theContent']);
    }

    /**
     * [enqueueScripts description]
     * @return void
     */
    public function enqueueScripts()
    {
        wp_register_script(
            'jquery-fancybox',
            plugins_url('assets/js/jquery.fancybox.min.js', plugin_basename(__FILE__)),
            ['jquery'],
            '3.5.2',
            true
        );
        wp_register_script(
            'rrze-fancybox',
            plugins_url('assets/js/rrze-fancybox.min.js', plugin_basename(__FILE__)),
            ['jquery-fancybox'],
            ['1.0.0'],
            true
        );
    }

    /**
     * [theContent description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function theContent($content)
    {
        $current_theme = wp_get_theme();
        $allowed_themes = ['rrze-2015', 'fau-events'];

        if (!in_array(strtolower($current_theme->stylesheet), $allowed_themes)) {
            return $content;
        }

        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        $xpath = new DOMXPath($dom);
        $entries = $xpath->query("//a[@class='lightbox']");

        if ($entries->length) {
            wp_enqueue_style('rrze-elements');
            wp_enqueue_script('rrze-fancybox');
        }
        return $content;
    }
}
