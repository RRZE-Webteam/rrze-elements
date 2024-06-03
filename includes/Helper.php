<?php

namespace RRZE\Elements;

defined('ABSPATH') || exit;

class Helper
{
    /**
     * [isPluginAvailable description]
     * @param  [string  $plugin [description]
     * @return boolean         [description]
     */
    public static function isPluginAvailable($plugin)
    {
        if (is_network_admin()) {
            return file_exists(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin);
        } elseif (!function_exists('is_plugin_active')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        return is_plugin_active($plugin);
    }

    public static function get_html_var_dump($input, $nohtml = true)
    {
        if ($nohtml) {
            foreach ($input as $key => $value) {

                if (is_array($value)) {
                    foreach ($value as $skey => $svalue) {
                        if (is_string($svalue)) {
                            $input[$key][$skey] = '<em>' . esc_html($svalue) . '</em>';
                        }
                    }
                } elseif (is_string($value)) {
                    $input[$key] = esc_html($value);
                }
            }
        }

        $out = self::get_var_dump($input);

        $out = preg_replace("/=>[\r\n\s]+/", ' => ', $out);
        $out = preg_replace("/\s+bool\(true\)/", ' <span style="color:green">TRUE</span>,', $out);
        $out = preg_replace("/\s+bool\(false\)/", ' <span style="color:red">FALSE</span>,', $out);
        $out = preg_replace("/,([\r\n\s]+})/", "$1", $out);
        $out = preg_replace("/\s+string\(\d+\)/", '', $out);
        $out = preg_replace("/\[\"([a-z\-_0-9]+)\"\]/i", "[\"<span style=\"color:#dd8800\">$1</span>\"]", $out);

        return '<pre>' . $out . '</pre>';
    }
    public static function get_var_dump($input)
    {
        ob_start();
        var_dump($input);
        return "\n" . ob_get_clean();
    }

    /**
     * Log errors by writing to the debug.log file.
     */
    public static function debug($input, string $level = 'i')
    {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }
        if (in_array(strtolower((string) WP_DEBUG_LOG), ['true', '1'], true)) {
            $logPath = WP_CONTENT_DIR . '/debug.log';
        } elseif (is_string(WP_DEBUG_LOG)) {
            $logPath = WP_DEBUG_LOG;
        } else {
            return;
        }
        if (is_array($input) || is_object($input)) {
            $input = print_r($input, true);
        }
        switch (strtolower($level)) {
            case 'e':
            case 'error':
                $level = 'Error';
                break;
            case 'i':
            case 'info':
                $level = 'Info';
                break;
            case 'd':
            case 'debug':
                $level = 'Debug';
                break;
            default:
                $level = 'Info';
        }
        error_log(
            date("[d-M-Y H:i:s \U\T\C]")
                . " WP $level: "
                . basename(__FILE__) . ' '
                . $input
                . PHP_EOL,
            3,
            $logPath
        );
    }

    public static function shortcode_boolean($value): bool {
        $value = esc_attr($value);
        return in_array($value, [true, 'true', '1', 'yes', 'ja', 'on'], true);
    }
}
