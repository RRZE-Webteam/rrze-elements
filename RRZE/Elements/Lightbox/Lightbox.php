<?php

namespace RRZE\Elements\Lightbox;

use RRZE\Elements\Main;

defined('ABSPATH') || exit;

class Lightbox {

    public function __construct(Main $main) {
        $this->main = $main;
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function enqueue_scripts() {
        wp_register_script('jquery-fancybox', plugins_url('js/jquery.fancybox.min.js', $this->main->plugin_basename), ['jquery'], '2.2.0', true);
        wp_enqueue_script('jquery-fancybox');
        wp_register_script('fancybox', plugins_url('js/fancybox.js', $this->main->plugin_basename), ['jquery-fancybox'], null, true);
        wp_enqueue_script('fancybox');

        wp_enqueue_style('rrze-elements');
    }

}