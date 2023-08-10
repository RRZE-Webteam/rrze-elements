<?php

namespace RRZE\Elements\Notice;

defined('ABSPATH') || exit;

/**
 * [Notice description]
 */
class Notice
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        add_shortcode('notice-alert', [$this, 'shortcodeNotice']);
        add_shortcode('notice-attention', [$this, 'shortcodeNotice']);
        add_shortcode('notice-hinweis', [$this, 'shortcodeNotice']);
        add_shortcode('notice-baustelle', [$this, 'shortcodeNotice']);
        add_shortcode('notice-plus', [$this, 'shortcodeNotice']);
        add_shortcode('notice-minus', [$this, 'shortcodeNotice']);
        add_shortcode('notice-question', [$this, 'shortcodeNotice']);
        add_shortcode('notice-tipp', [$this, 'shortcodeNotice']);
        add_shortcode('notice-video', [$this, 'shortcodeNotice']);
        add_shortcode('notice-audio', [$this, 'shortcodeNotice']);
        add_shortcode('notice-download', [$this, 'shortcodeNotice']);
        add_shortcode('notice-faubox', [$this, 'shortcodeNotice']);
        add_shortcode('notice-thumbs-up', [$this, 'shortcodeNotice']);
        add_shortcode('notice-thumbs-down', [$this, 'shortcodeNotice']);
        /* Für die Abwärtskompatibilität der bereits in FAU-Einrichtungen
         * abwärtskompatiblen Shortcodes noch folgendes: */
        add_shortcode('attention', [$this, 'shortcodeNotice']);
        add_shortcode('hinweis', [$this, 'shortcodeNotice']);
        add_shortcode('baustelle', [$this, 'shortcodeNotice']);
        add_shortcode('plus', [$this, 'shortcodeNotice']);
        add_shortcode('minus', [$this, 'shortcodeNotice']);
        add_shortcode('question', [$this, 'shortcodeNotice']);
    }

    /**
     * [shortcodeNotice description]
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @param  string $tag     [description]
     * @return string          [description]
     */
    public function shortcodeNotice($atts, $content = '', $tag = '')
    {
        extract(shortcode_atts([
            'title' => '',
            'hstart' => '3'
        ], $atts));

        $tag_array = explode('-', $tag, 2);

        if (count($tag_array) > 1) {
            $type = $tag_array[1];
        } else {
            $type = $tag_array[0];
        }
        $class = ($title == '' ? ' no-title' : '');
        $hstart = intval($hstart);
		switch ($type) {
			case 'baustelle':
				$icon = 'wrench';
				$alt = __('Tools', 'rrze-elements');
				break;
			case 'question':
				$icon = 'circle-question';
				$alt = __('Question', 'rrze-elements');
				break;
			case 'minus':
				$icon = 'circle-minus';
				$alt = __('Minus', 'rrze-elements');
				break;
			case 'plus':
				$icon = 'circle-plus';
				$alt = __('Plus', 'rrze-elements');
				break;
			case 'tipp':
				$icon = 'regular lightbulb';
				$alt = __('Tipp', 'rrze-elements');
				break;
			case 'download':
				$icon = 'download';
				$alt = __('Download', 'rrze-elements');
				break;
			case 'faubox':
				$icon = 'cloud-arrow-down';
				$alt = __('Cloud', 'rrze-elements');
				break;
			case 'audio':
				$icon = 'volume-high';
				$alt = __('Audio', 'rrze-elements');
				break;
			case 'video':
				$icon = 'video';
				$alt = __('Video', 'rrze-elements');
				break;
			case 'thumbs-up':
				$icon = 'thumbs-up';
				$alt = __('Thumbs up', 'rrze-elements');
				break;
			case 'thumbs-down':
				$icon = 'thumbs-down';
				$alt = __('Thumbs down', 'rrze-elements');
				break;
			case 'alert':
			case 'attention':
			default:
				$icon = 'solid circle-exclamation';
				$alt = __('Exclamation mark', 'rrze-elements');
		}

        $output = '<div class="notice notice-' . $type . $class . '">'
            . wpautop(do_shortcode('[icon icon="'.$icon.'" style="2x" alt="' . $alt . '"]'));
	    if (isset($title) && $title != '') {
            $output .= "<h$hstart>" . $title . "</h$hstart>";
        }
        $output .= '<p>' . wpautop(do_shortcode($content)) . '</p></div>';

        wp_enqueue_style('fontawesome');
        wp_enqueue_style('rrze-elements');

        return wpautop($output);
    }
}
