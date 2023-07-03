<?php

/*
Plugin Name:     RRZE Elements
Plugin URI:      https://github.com/RRZE-Webteam/rrze-elements
Description:     Advanced design elements for WordPress websites.
Version:         1.25.10
Author:          RRZE Webteam
Author URI:      https://blogs.fau.de/webworking/
License:         GNU General Public License v2
License URI:     http://www.gnu.org/licenses/gpl-2.0.html
Domain Path:     /languages
Text Domain:     rrze-elements
*/

namespace RRZE\Elements;

defined('ABSPATH') || exit;

require_once 'config/config.php';

use RRZE\Elements\Main;

const RRZE_PHP_VERSION = '7.4';
const RRZE_WP_VERSION = '5.9';
const RRZE_ELEMENTS_VERSION = '1.25.10';

spl_autoload_register(function ($class) {
    $prefix = __NAMESPACE__;
    $base_dir = __DIR__ . '/includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

register_activation_hook(__FILE__, __NAMESPACE__ . '\activation');
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\deactivation');
add_action('plugins_loaded', __NAMESPACE__ . '\loaded');

register_activation_hook(__FILE__, 'RRZE\Elements\activation');
register_deactivation_hook(__FILE__, 'RRZE\Elements\deactivation');

add_action('plugins_loaded', 'RRZE\Elements\loaded');

/**
 * [loadTextdomain description]
 * @return void
 */
function loadTextdomain()
{
    load_plugin_textdomain('rrze-elements', false, sprintf('%s/languages/', dirname(plugin_basename(__FILE__))));
}

/**
 * [systemRequirements description]
 * @return string [description]
 */
function systemRequirements()
{
    $error = '';
    if (version_compare(PHP_VERSION, RRZE_PHP_VERSION, '<')) {
        $error = sprintf(__('The server is running PHP version %1$s. The Plugin requires at least PHP version %2$s.', 'rrze-elements'), PHP_VERSION, RRZE_PHP_VERSION);
    } elseif (version_compare($GLOBALS['wp_version'], RRZE_WP_VERSION, '<')) {
        $error = sprintf(__('The server is running WordPress version %1$s. The Plugin requires at least WordPress version %2$s.', 'rrze-elements'), $GLOBALS['wp_version'], RRZE_WP_VERSION);
    }
    return $error;
}

/**
 * [activation description]
 * @return void
 */
function activation()
{
    loadTextdomain();

    if ($error = systemRequirements()) {
        deactivate_plugins(plugin_basename(__FILE__), false, true);
        wp_die($error);
    }
}

/**
 * [deactivation description]
 * @return [type] [description]
 */
function deactivation()
{
}

/**
 * [loaded description]
 * @return void
 */
function loaded()
{
    loadTextdomain();

    if ($error = systemRequirements()) {
        if (!function_exists('get_plugin_data')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        $plugin_data = get_plugin_data(__FILE__);
        $plugin_name = $plugin_data['Name'];
        $tag = is_network_admin() ? 'network_admin_notices' : 'admin_notices';
        add_action($tag, function () use ($plugin_name, $error) {
            printf('<div class="notice notice-error"><p>%1$s: %2$s</p></div>', esc_html($plugin_name), esc_html($error));
        });
    } else {
        new Main(__FILE__);
    }
}
