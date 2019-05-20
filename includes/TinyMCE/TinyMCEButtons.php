<?php

namespace RRZE\Elements\TinyMCE;

defined('ABSPATH') || exit;

/**
 * [TinyMCEButtons description]
 */
class TinyMCEButtons
{

    /**
     * [__construct description]
     */
    public function __construct()
    {
        add_action('admin_init', [$this, 'shortcodeButtons']);
    }

    /**
     * [shortcodeButtons description]
     * @return [type] [description]
     */
    public function shortcodeButtons()
    {
        if (current_user_can('edit_posts') &&  current_user_can('edit_pages')) {
            add_filter('mce_external_plugins', [$this, 'addButtons']);
        }
    }

    /**
     * [addButtons description]
     * @param array $pluginArray [description]
     * @return array
     */
    public function addButtons($pluginArray)
    {
        $pluginArray['rrzeelementsshortcodes'] = plugins_url('assets/js/tinymce-shortcodes.min.js', plugin_basename(__FILE__));
        return $pluginArray;
    }
}
