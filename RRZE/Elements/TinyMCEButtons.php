<?php

namespace RRZE\Elements;

defined('ABSPATH') || exit;

class TinyMCEButtons {

    public function __construct()  {
        add_action('admin_init', array($this, 'rrze_elements_shorcode_buttons'));
    }

    public function rrze_elements_shorcode_buttons()  {
        if( current_user_can('edit_posts') &&  current_user_can('edit_pages') ) {
            add_filter( 'mce_external_plugins', array($this, 'rrze_elements_add_buttons' ));
        }
    }

    public function rrze_elements_add_buttons( $plugin_array ) {
        $plugin_array['rrzeelementsshortcodes'] = plugins_url('js/tinymce-elements-shortcodes.js', dirname(__FILE__,2));
        return $plugin_array;
    }
}
