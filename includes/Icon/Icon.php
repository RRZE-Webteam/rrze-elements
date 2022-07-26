<?php

namespace RRZE\Elements\Icon;

defined('ABSPATH') || exit;
class Icon {

	public function __construct() {
		add_shortcode( 'icon', [ $this, 'shortcodeIcon' ] );
	}

	public function shortcodeIcon($atts, $content = '', $tag = '') {
		$defaults = [
			'icon' => '',
			'style' => '',
			'color' => ''];
		$args = shortcode_atts($defaults, $atts);
		array_walk($args, 'sanitize_text_field');
		if ($args['icon'] == '') return '';
		$styles = array_map('trim', explode(',', $args['style']));
		$styleParams = '';
		foreach ($styles as $style) {
			if (in_array($style, ['2x', '3x', '4x', '5x', 'border', 'pull-right', 'pull-left']) ) {
				$styleParams .= ' fa-'. $style;
			}
		}
		$inlineCSS = '';
		if (in_array($args['color'], ['fau', 'zuv', 'phil', 'nat', 'med', 'rw', 'tf']) ) {
			// FAU Theme Colors
			if ($args['color'] == 'fau' || $args['color'] == 'zuv') {
				$args['color'] = 'zentral';
			}
			$inlineCSS = 'style="color: var(--color-'.$args['color'].'-basis, #04316A);"';
		} elseif (strlen($args['color']) == 7 && strpos($args['color'], '#') == 0) {
			// Hex Colors
			$inlineCSS = 'style="color: '.$args['color'].';"';
		}
		$output = '<i class="rrze-elements-icon fa fa-'.$args['icon'].' ' . $styleParams . '" ' . $inlineCSS . 'aria-hidden="true" ></i>';

		wp_enqueue_style('fontawesome');
		wp_enqueue_style('rrze-elements');

		return $output;

	}
}