<?php

namespace RRZE\Elements\LaTeX;

defined('ABSPATH') || exit;

/**
 * LaTeX Shortcode.
 */
class LaTeX
{
    /**
     * KaTeX version
     * @var string
     * @link https://github.com/KaTeX/KaTeX/releases
     */
    const KATEX_VERSION = '0.16.9';

    /**
     * Constructor
     */
    public function __construct()
    {
        add_shortcode('latex', [$this, 'shortcodeLatex'], 10, 2);
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_filter('no_texturize_shortcodes', [$this, 'katexExemptWpTexturize']);
    }

    /**
     * Shortcode callback.
     * @param  array $atts Attributes
     * @param  string $content Content
     * @return string
     */
    public function shortcodeLatex($atts, $content = '')
    {
        $latex_atts = shortcode_atts([
            'display' => 'false'
        ], $atts);

        $display = $latex_atts['display'] == 'true' ? true : false;

        $output = '';

        if ($display || strpos($content, '\\displaystyle') === 0) {
            $output = '<span class="wp-katex-eq katex-display" data-display="true">' . htmlspecialchars(html_entity_decode($content)) . '</span>';
        } else {
            $output = '<span class="wp-katex-eq" data-display="false">' . htmlspecialchars(html_entity_decode($content)) . '</span>';
        }

        wp_enqueue_style('katex');
        wp_enqueue_script('katex');
        add_action('wp_footer', [$this, 'katexScript'], 99);

        return $output;
    }

    /**
     * Enqueue scripts and styles.
     * @return void
     */
    public function enqueueScripts()
    {
        wp_register_style(
            'katex',
            plugins_url('assets/katex/' . static::KATEX_VERSION . '/katex.min.css', plugin_basename(__FILE__)),
            false,
            static::KATEX_VERSION
        );
        wp_register_script(
            'katex',
            plugins_url('assets/katex/' . static::KATEX_VERSION . '/katex.min.js', plugin_basename(__FILE__)),
            [],
            static::KATEX_VERSION,
            true
        );
    }

    /**
     * KaTeX initial script.
     * @return void
     */
    public function katexScript()
    {
        $script = '!function(){"use strict";var e=document.querySelectorAll(".wp-katex-eq");Array.prototype.forEach.call(e,function(e){var t={displayMode:"true"===e.getAttribute("data-display"),throwOnError:!1},r=document.createElement("span");try{katex.render(e.textContent,r,t)}catch(a){r.style.color="red",r.textContent=a.message}e.parentNode.replaceChild(r,e)})}();';
        echo PHP_EOL, '<script>', PHP_EOL, $script, PHP_EOL, '</script>', PHP_EOL;
    }

    /**
     * No texturize for latex shortcode.
     * @param  array $shortcodes Shortcodes
     * @return array
     */
    public function katexExemptWpTexturize($shortcodes)
    {
        $shortcodes[] = 'latex';
        return $shortcodes;
    }
}
