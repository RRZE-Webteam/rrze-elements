<?php

namespace RRZE\Elements\Columns;

use RRZE\Elements\Main;

defined('ABSPATH') || exit;

class Columns {

    public function __construct() {
        add_shortcode('two_columns_one', [$this, 'shortcode_two_columns_one']);
        add_shortcode('two_columns_one_last', [$this, 'shortcode_two_columns_one_last']);
        add_shortcode('three_columns_one', [$this, 'shortcode_three_columns_one']);
        add_shortcode('three_columns_one_last', [$this, 'shortcode_three_columns_one_last']);
        add_shortcode('three_columns_two', [$this, 'shortcode_three_columns_two']);
        add_shortcode('three_columns_two_last', [$this, 'shortcode_three_columns_two_last']);
        add_shortcode('four_columns_one', [$this, 'shortcode_four_columns_one']);
        add_shortcode('four_columns_one_last', [$this, 'shortcode_four_columns_one_last']);
        add_shortcode('four_columns_two', [$this, 'shortcode_four_columns_two']);
        add_shortcode('four_columns_two_last', [$this, 'shortcode_four_columns_two_last']);
        add_shortcode('four_columns_three', [$this, 'shortcode_four_columns_three']);
        add_shortcode('four_columns_three_last', [$this, 'shortcode_four_columns_three_last']);
        add_shortcode('divider', [$this, 'shortcode_divider']);

    }

    // Two Columns
    public function shortcode_two_columns_one($atts, $content = null) {
        wp_enqueue_style('rrze-elements');
        return '<div class="two-columns-one">' . do_shortcode(($content)) . '</div>';
    }


    public function shortcode_two_columns_one_last($atts, $content = null) {
        wp_enqueue_style('rrze-elements');
        return '<div class="two-columns-one last">' . do_shortcode(($content)) . '</div>';
    }

    // Three Columns
    public function shortcode_three_columns_one($atts, $content = null) {
        wp_enqueue_style('rrze-elements');
        return '<div class="three-columns-one">' . do_shortcode(($content)) . '</div>';
    }

    public function shortcode_three_columns_one_last($atts, $content = null) {
        wp_enqueue_style('rrze-elements');
        return '<div class="three-columns-one last">' . do_shortcode(($content)) . '</div>';
    }

    public function shortcode_three_columns_two($atts, $content = null) {
        wp_enqueue_style('rrze-elements');
        return '<div class="three-columns-two">' . do_shortcode(($content)) . '</div>';
    }

    public function shortcode_three_columns_two_last($atts, $content = null) {
        wp_enqueue_style('rrze-elements');
        return '<div class="three-columns-two last">' . do_shortcode(($content)) . '</div>';
    }

    // Four Columns
    public function shortcode_four_columns_one($atts, $content = null) {
        wp_enqueue_style('rrze-elements');
        return '<div class="four-columns-one">' . do_shortcode(($content)) . '</div>';
    }

    public function shortcode_four_columns_one_last($atts, $content = null) {
        wp_enqueue_style('rrze-elements');
        return '<div class="four-columns-one last">' . do_shortcode(($content)) . '</div>';
    }

    public function shortcode_four_columns_two($atts, $content = null) {
        wp_enqueue_style('rrze-elements');
        return '<div class="four-columns-two">' . do_shortcode(($content)) . '</div>';
    }

    public function shortcode_four_columns_two_last($atts, $content = null) {
        wp_enqueue_style('rrze-elements');
        return '<div class="four-columns-two last">' . do_shortcode(($content)) . '</div>';
    }

    public function shortcode_four_columns_three($atts, $content = null) {
        wp_enqueue_style('rrze-elements');
        return '<div class="four-columns-three">' . do_shortcode(($content)) . '</div>';
    }

    public function shortcode_four_columns_three_last($atts, $content = null) {
        wp_enqueue_style('rrze-elements');
        return '<div class="four-columns-three last">' . do_shortcode(($content)) . '</div>';
    }

    // Divide Text Shortcode
    public function shortcode_divider($atts, $content = null) {
        wp_enqueue_style('rrze-elements');
        return '<div class="elements-divider"></div>';
    }

}
