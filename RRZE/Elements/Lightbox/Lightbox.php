<?php

namespace RRZE\Elements\Lightbox;

use RRZE\Elements\Main;
use DOMDocument;
use DOMXPath;

defined('ABSPATH') || exit;

class Lightbox
{
    protected $main;

    public function __construct(Main $main)
    {
        $this->main = $main;

        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_filter('the_content', [$this, 'the_content']);
    }

    public function enqueue_scripts()
    {
        wp_register_script('jquery-fancybox', plugins_url('js/jquery.fancybox.min.js', $this->main->plugin_basename), ['jquery'], '3.5.2', true);
        wp_register_script('rrze-fancybox', plugins_url('js/fancybox.min.js', $this->main->plugin_basename), ['jquery-fancybox'], false, true);
    }

    public function the_content($content)
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
