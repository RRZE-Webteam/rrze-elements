<?php

/**
 * Plugin Name:     RRZE Elements
 * Plugin URI:      https://github.com/RRZE-Webteam/rrze-elements
 * Description:     Erweiterte Gestaltungselemente für WordPress-Websites
 * Version:         1.7.3
 * Author:          RRZE-Webteam
 * Author URI:      https://blogs.fau.de/webworking/
 * License:         GNU General Public License v2
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:     /languages
 * Text Domain:     rrze-elements
 */

namespace RRZE\Elements;

use RRZE\Elements\Main;

defined('ABSPATH') || exit;

const RRZE_PHP_VERSION = '7.1';
const RRZE_WP_VERSION = '5.0';

register_activation_hook(__FILE__, 'RRZE\Elements\activation');
register_deactivation_hook(__FILE__, 'RRZE\Elements\deactivation');

add_action('plugins_loaded', 'RRZE\Elements\loaded');

/*
 * Einbindung der Sprachdateien.
 * @return void
 */
function load_textdomain()
{
    load_plugin_textdomain('rrze-elements', false, sprintf('%s/languages/', dirname(plugin_basename(__FILE__))));
}

/*
 * Wird durchgeführt, nachdem das Plugin aktiviert wurde.
 * @return void
 */
function activation()
{
    // Sprachdateien werden eingebunden.
    load_textdomain();

    // Überprüft die minimal erforderliche PHP- u. WP-Version.
    system_requirements();

    // Ab hier können die Funktionen hinzugefügt werden,
    // die bei der Aktivierung des Plugins aufgerufen werden müssen.
    // Bspw. wp_schedule_event, flush_rewrite_rules, etc.
}

/*
 * Wird durchgeführt, nachdem das Plugin deaktiviert wurde.
 * @return void
 */
function deactivation()
{
    // Hier können die Funktionen hinzugefügt werden, die
    // bei der Deaktivierung des Plugins aufgerufen werden müssen.
    // Bspw. wp_clear_scheduled_hook, flush_rewrite_rules, etc.
}

/*
 * Überprüft die minimal erforderliche PHP- u. WP-Version.
 * @return void
 */
function system_requirements()
{
    $error = '';

    if (version_compare(PHP_VERSION, RRZE_PHP_VERSION, '<')) {
        $error = sprintf(__('Your server is running PHP version %s. Please upgrade at least to PHP version %s.', 'rrze-elements'), PHP_VERSION, RRZE_PHP_VERSION);
    }

    if (version_compare($GLOBALS['wp_version'], RRZE_WP_VERSION, '<')) {
        $error = sprintf(__('Your Wordpress version is %s. Please upgrade at least to Wordpress version %s.', 'rrze-elements'), $GLOBALS['wp_version'], RRZE_WP_VERSION);
    }

    // Wenn die Überprüfung fehlschlägt, dann wird das Plugin automatisch deaktiviert.
    if (!empty($error)) {
        deactivate_plugins(plugin_basename(__FILE__), false, true);
        wp_die($error);
    }
}

/*
 * Wird durchgeführt, nachdem das WP-Grundsystem hochgefahren
 * und alle Plugins eingebunden wurden.
 * @return void
 */
function loaded()
{
    // Sprachdateien werden eingebunden.
    load_textdomain();

    // Automatische Laden von Klassen.
    autoload();
}

/*
 * Automatische Laden von Klassen.
 * @return void
 */
function autoload()
{
    require 'autoload.php';
    return new Main(plugin_basename(__FILE__));
}
