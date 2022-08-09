<?php

namespace RRZE\Elements\TextColumns;

defined('ABSPATH') || exit;

class TextColumns {

	public function __construct()
	{
		add_shortcode('text-columns', [$this, 'shortcodeTextColumns']);
	}

	public function shortcodeTextColumns($atts, $content = null) {
		$defaults = array(
			'number' => '2',
			'width' => '240',
			'rule' => 'true',
			'rule-color' => 'var(--color-ContentBorders, #C3C3CB)',
			'background-color' => '',
			'border-color' => '',
			'font' => 'dark',
			'style' => '',
		);
		$args = shortcode_atts($defaults, $atts);
		$count = absint($args['number']);
		$width = absint($args['width']);
		$ruleColor = esc_attr($args['rule-color']);
		$backgroundColor = esc_attr($args['background-color']);
		$borderColor = esc_attr($args['border-color']);
		$style = (in_array($args['style'], array('success', 'info', 'warning', 'danger', 'example'))) ? ' alert-' . $args['style'] : '';

		$css = [
			"column-count: $count;",
			"column-width: $width\px;",
		];
		if ($args['rule'] == 'true') {
			$css[] = "column-rule: 1px solid $ruleColor;";
		}
		if ($backgroundColor != '') {
			$css[] = "background-color: $backgroundColor;";
		}
		if ($borderColor != '') {
			$css[] = "border: 1px solid $borderColor;";
		}
		if ($backgroundColor != '' || $borderColor != '') {
			$css[] = 'padding: .8em;';
		}
		if ($args['font'] == 'light') {
			$class = 'font-light';
		} else {
			$class = '';
		}
		if (in_array($args['style'], array('success', 'info', 'warning', 'danger', 'example'))) {
			$class .= ' alert alert-' . $args['style'];
		}

		wp_enqueue_style('rrze-elements');
		return '<div class="elements-textcolumns '.$class.'" ' . 'style="' . implode(' ', $css) . '">' . do_shortcode($content) . '</div>';
	}

}