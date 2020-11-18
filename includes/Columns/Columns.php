<?php

namespace RRZE\Elements\Columns;

defined('ABSPATH') || exit;

/**
 * [Columns description]
 */
class Columns
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        add_shortcode('grid', [$this, 'shortcodeGrid']);
        add_shortcode('cell', [$this, 'shortcodeGridCell']);
        add_shortcode('two_columns_one', [$this, 'shortcodeTwoColumnsOne']);
        add_shortcode('two_columns_one_last', [$this, 'shortcodeTwoColumnsOneLast']);
        add_shortcode('three_columns_one', [$this, 'shortcodeThreeColumnsOne']);
        add_shortcode('three_columns_one_last', [$this, 'shortcodeThreeColumnsOneLast']);
        add_shortcode('three_columns_two', [$this, 'shortcodeThreeColumnsTwo']);
        add_shortcode('three_columns_two_last', [$this, 'shortcodeThreeColumnsTwoLast']);
        add_shortcode('four_columns_one', [$this, 'shortcodeFourColumnsOne']);
        add_shortcode('four_columns_one_last', [$this, 'shortcodeFourColumnsOneLast']);
        add_shortcode('four_columns_two', [$this, 'shortcodeFourColumnsTwo']);
        add_shortcode('four_columns_two_last', [$this, 'shortcodeFourColumnsTwoLast']);
        add_shortcode('four_columns_three', [$this, 'shortcodeFourColumnsThree']);
        add_shortcode('four_columns_three_last', [$this, 'shortcodeFourColumnsThreeLast']);
        add_shortcode('divider', [$this, 'shortcodeDivider']);
        add_shortcode('columns', [$this, 'shortcodeColumns']);
        add_shortcode('column', [$this, 'shortcodeColumn']);
    }

    /*
     * Flexbox Columns
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeColumns($atts, $content = null){
        wp_enqueue_style('rrze-elements');
        $defaults = array(
            'number' => '',
        );
        $args = shortcode_atts($defaults, $atts);
        $columns = absint($args['number']);
        $colClass = $columns > 0 ? 'cols-'.$columns : '';
        return '<div class="elements-columns '.$colClass.'">' . do_shortcode(($content)) . '</div>';
    }

    public function shortcodeColumn($atts, $content = null){
        $defaults = array(
            'span' => '1',
        );
        $spans =['1', '2', '3'];
        $args = shortcode_atts($defaults, $atts);
        $class = 'span_' . (in_array($args['span'], $spans) ? $args['span'] : $defaults['span']);

        return "<div class=\"column $class\">" . do_shortcode(($content)) . '</div>';
    }


    /**
     * Grid Cells
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeGrid($atts, $content = null)
    {
        wp_enqueue_style('rrze-elements');
        $defaults = array(
            'cols' => ' 1,1,1',

        );
        $args = shortcode_atts($defaults, $atts);
        $grid = explode(',', $args['cols']);
        array_walk($grid, function (&$v, $k) {
            $v = (is_numeric($v)) ? $v.'fr' : $v;
        });
        $cols = 'grid-template-columns: ' . implode(' ', $grid) . ';';
        return '<div class="columns-grid" style="' . $cols . '">'
                . do_shortcode(($content))
                . '</div>';
    }

    /**
     * Shortcode style wrapper
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeGridCell($atts, $content = null)
    {
        wp_enqueue_style('rrze-elements');
        return '<div class="column-grid">' . do_shortcode(($content)) . '</div>';
    }

    /**
     * Two Columns 1/1
     * The first column.
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeTwoColumnsOne($atts, $content = null)
    {
        wp_enqueue_style('rrze-elements');
        return '<div class="two-columns-one">' . do_shortcode(($content)) . '</div>';
    }

    /**
     * Two Columns 1/1
     * The last column.
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeTwoColumnsOneLast($atts, $content = null)
    {
        wp_enqueue_style('rrze-elements');
        return '<div class="two-columns-one last">' . do_shortcode(($content)) . '</div>';
    }

    /**
     * Three Columns 1/1/1
     * Is used for the first 2 columns.
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeThreeColumnsOne($atts, $content = null)
    {
        wp_enqueue_style('rrze-elements');
        return '<div class="three-columns-one">' . do_shortcode(($content)) . '</div>';
    }

    /**
     * Three Columns 1/1/1
     * Is used for the last column.
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeThreeColumnsOneLast($atts, $content = null)
    {
        wp_enqueue_style('rrze-elements');
        return '<div class="three-columns-one last">' . do_shortcode(($content)) . '</div>';
    }

    /**
     * Three Columns 2/1
     * Merges the first two columns.
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeThreeColumnsTwo($atts, $content = null)
    {
        wp_enqueue_style('rrze-elements');
        return '<div class="three-columns-two">' . do_shortcode(($content)) . '</div>';
    }

    /**
     * Three Columns 1/2
     * Merges the last two columns.
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeThreeColumnsTwoLast($atts, $content = null)
    {
        wp_enqueue_style('rrze-elements');
        return '<div class="three-columns-two last">' . do_shortcode(($content)) . '</div>';
    }

    /**
     * Four Columns 1/1/1/1
     * Is used for the first 3 columns.
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeFourColumnsOne($atts, $content = null)
    {
        wp_enqueue_style('rrze-elements');
        return '<div class="four-columns-one">' . do_shortcode(($content)) . '</div>';
    }

    /**
     * Four Columns 1/1/1/1
     * Is used for the last column.
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeFourColumnsOneLast($atts, $content = null)
    {
        wp_enqueue_style('rrze-elements');
        return '<div class="four-columns-one last">' . do_shortcode(($content)) . '</div>';
    }

    /**
     * Four Columns 2/1/1
     * Merges the first two columns.
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeFourColumnsTwo($atts, $content = null)
    {
        wp_enqueue_style('rrze-elements');
        return '<div class="four-columns-two">' . do_shortcode(($content)) . '</div>';
    }

    /**
     * Four Columns 1/1/2
     * Merges the last two columns.
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeFourColumnsTwoLast($atts, $content = null)
    {
        wp_enqueue_style('rrze-elements');
        return '<div class="four-columns-two last">' . do_shortcode(($content)) . '</div>';
    }

    /**
     * Four Columns 3/1
     * Merges the first three columns.
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeFourColumnsThree($atts, $content = null)
    {
        wp_enqueue_style('rrze-elements');
        return '<div class="four-columns-three">' . do_shortcode(($content)) . '</div>';
    }

    /**
     * Four Columns 1/3
     * Merges the last three columns.
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeFourColumnsThreeLast($atts, $content = null)
    {
        wp_enqueue_style('rrze-elements');
        return '<div class="four-columns-three last">' . do_shortcode(($content)) . '</div>';
    }

    /**
     * Shortcode divider.
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeDivider($atts, $content = null)
    {
        wp_enqueue_style('rrze-elements');
        return '<div class="elements-divider"></div>';
    }
}
