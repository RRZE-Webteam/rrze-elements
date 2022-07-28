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
			'rule' => 'true'
			//'rule'
		);
		$args = shortcode_atts($defaults, $atts);
		$count = absint($args['number']);
		$width = absint($args['width']);
		$rule = $args['rule'] == 'true' ? ' column-rule: 1px solid var(--color-TextLight, #C3C3CB);' : '';

		wp_enqueue_style('rrze-elements');
		return '<div class="elements-textcolumns" style="column-count: '.$count.'; column-width: '.$width.'px;'.$rule.'">' . do_shortcode(($content)) . '</div>';
	}

}