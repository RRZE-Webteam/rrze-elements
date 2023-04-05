<?php

namespace RRZE\Elements\Icon;

defined('ABSPATH') || exit;
class Icon {

	protected $pluginFile;

	public function __construct($pluginFile) {
		add_shortcode( 'icon', [ $this, 'shortcodeIcon' ] );
		add_shortcode( 'list-icons', [ $this, 'shortcodeListIcons' ] );
		$this->pluginFile = $pluginFile;
	}

	public function shortcodeIcon($atts, $content = '', $tag = '') {
        if (!isset($atts['icon']) || $atts =='') return '';
        $args = self::sanitizeAtts($atts);

        $svg = self::getIcon($args['icon']);
        if ($svg == '') {
            return '<span style="background-color: var(--color-Negative, #a71c18); color: var(--color-Negative-Kontrast, #ffffff); padding: 0 5px;">' . __('Icon not found.', 'rrze-elements') . '</span>';
        }

		if ($args['alt'] == '') {
			$a11yTags = ' aria-hidden="true" focusable="false"';
		} else {
			$a11yTags = ' alt="' . $args['alt'] . '"';
		}
		$styles = array_map('trim', explode(',', $args['style']));
		sort($styles);
		$scale = 1;
		$CSSstyles = [];
		foreach ($styles as $style) {
			switch($style) {
				case '2x':
				case '3x':
				case '4x':
				case '5x':
				$scale = (int)str_replace('x', '', $style);
					break;
				case 'border':
					$CSSstyles[] = 'padding: .2em .25em .15em;';
					$CSSstyles[] = 'border: solid .08em #eee;';
					$CSSstyles[] = 'border-radius: .1em;';
					break;
				case 'pull-left':
					$CSSstyles[] = 'float: left;';
					$CSSstyles[] = 'margin-right: .3em;';
					break;
				case 'pull-right':
					$CSSstyles[] = 'float: right;';
					$CSSstyles[] = 'margin-left: .3em;';
					break;
			}
		}
		$CSSstyles[] = 'font-size: ' . $scale . 'em;';
		if (in_array($args['color'], ['fau', 'zuv', 'phil', 'nat', 'med', 'rw', 'tf']) ) {
			// FAU Theme Colors
			if ($args['color'] == 'fau' || $args['color'] == 'zuv') {
				$args['color'] = 'zentral';
			}
			$CSSstyles[] = 'fill: var(--color-'.$args['color'].'-basis, #04316A);';
		} elseif (strlen($args['color']) == 7 && strpos($args['color'], '#') == 0) {
			// Hex Colors
			$CSSstyles[] = 'fill: '.$args['color'].';';
		} else {
			$CSSstyles[] = 'fill: currentcolor;';
		}
		$style = 'style="' . implode(' ', $CSSstyles) .'" ';

		$output = str_replace('<svg ', '<svg height="1em" class="rrze-elements-icon"' . $style . $a11yTags, $svg);
		//$output = str_replace('<svg ', '<svg ' . $style, $svg);

		wp_enqueue_style('fontawesome');
		wp_enqueue_style('rrze-elements');

		return $output;

	}

    public function shortcodeListIcons($atts, $content = '', $tag = '') {
        if (!isset($atts['icon']) || $atts =='') return $content;
        $args = self::sanitizeAtts($atts);

        $svg = self::getIcon($args['icon']);
        if ($svg == '') {
            wp_enqueue_style('rrze-elements');
            return '<div class="alert alert-danger clearfix clear">' . __('Icon not found.', 'rrze-elements') . '</div>' . $content;
        }

        $svgMod = str_replace('<path', '<path fill="'.urlencode($args['color']).'"', $svg);
        $rand = random_int(0, 9999);
        $class = 'rrze-elements-icon-list-'.$rand;
        $style = 'ul.'.$class . ' li:before { background-image: url(\'data:image/svg+xml;utf8,'.$svgMod.'\'); }';
        $contentMod = str_replace('<ul>', '<ul class="'.$class.'">', $content);

        wp_enqueue_style('rrze-elements');
        wp_add_inline_style('rrze-elements', $style);

        return $contentMod;
    }

    private function getIcon($icon) {
        $iconDetails = explode(' ', $icon);
        if (count($iconDetails) > 1 && in_array($iconDetails[0], ['solid', 'regular', 'brands'])) {
            $subset = $iconDetails[0];
            $icon = sanitize_title($iconDetails[1]);
        } else {
            $subset = 'solid';
            $icon = sanitize_title($iconDetails[0]);
        }
        $path = plugin_dir_path( $this->pluginFile ) . 'assets/svg/';
        $file = $path . $subset . '/' . $icon . '.svg';
        if (!file_exists($file) && str_contains($icon, '-')) {
            // For backwards compatibility In prior versions circle-user icon was named user-circle :-)
            $iconParts = explode('-', $icon, 2);
            $icon = $iconParts[1].'-'.$iconParts[0];
            $file = $path . $subset . '/' . $icon . '.svg';
        }
        if (!file_exists($file)) {
            return '';
        } else {
            return file_get_contents($file);
        }
    }

    private function sanitizeAtts($atts) {
        $defaults = [
            'icon' => '',
            'style' => '',
            'color' => '',
            'subset' => 'solid',
            'alt' => '',];
        $args = shortcode_atts($defaults, $atts);
        array_walk($args, 'sanitize_text_field');
        return $args;
    }

}