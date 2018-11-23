<?php

namespace RRZE\Elements\TinyMCE;

use RRZE\Elements\Main;

defined('ABSPATH') || exit;

class TinyMCEButtons {

    protected $main;

    public function __construct(Main $main) {
        $this->main = $main;

        add_action('admin_init', [$this, 'shorcode_buttons']);
    }

    public function shorcode_buttons()  {
        if( current_user_can('edit_posts') &&  current_user_can('edit_pages')) {
            add_filter('mce_external_plugins', [$this, 'add_buttons']);
        }
    }

    public function add_buttons($plugin_array) {
        $plugin_array['rrzeelementsshortcodes'] = plugins_url('js/tinymce-shortcodes.min.js', $this->main->plugin_basename);
        return $plugin_array;
    }
}
