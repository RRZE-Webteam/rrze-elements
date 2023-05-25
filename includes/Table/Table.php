<?php

namespace RRZE\Elements\Table;

use const RRZE\Elements\RRZE_ELEMENTS_VERSION;

defined( 'ABSPATH') || exit;

/**
 * [Tabs description]
 */
class Table {

    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_filter( 'the_content', [$this, 'includeTablescript'] );
    }

    public function includeTablescript($content) {
        if (strpos($content, '<table ') !== false) {
            wp_enqueue_script('rrze-tables');
            $script = 'jQuery(function($) { $("table.sorttable").tablesorter(); $("table.filtertable").tablesorter({widgets: ["filter"], filter_columnAnyMatch: true, }); }); ';
            wp_add_inline_script('rrze-tables', $script, 'after' );
            wp_enqueue_style('rrze-elements');
        }
        return $content;
    }

    public static function enqueueScripts() {
        wp_register_script(
            'rrze-tables',
            plugins_url('assets/tablesorter/js/jquery.tablesorter.combined.min.js', plugin_basename(__FILE__)),
            ['jquery'],
            '2.31.3'
        );
    }
}