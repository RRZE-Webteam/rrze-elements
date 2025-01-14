<?php

namespace RRZE\Elements\Columns;

use function RRZE\Elements\Config\calculateContrastColor;

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
        $content = shortcode_unautop(trim($content));
        wp_enqueue_style('rrze-elements');
        $defaults = array(
            'number' => '',
            'valign' => '',
        );
        $args = shortcode_atts($defaults, $atts);
        $columns = absint($args['number']);
        $colClass = $columns > 0 ? 'cols-'.$columns : '';
        switch ($args['valign']) {
            case 'top':
                $valign = 'style="align-items: flex-start"';
                break;
            case 'bottom':
                $valign = 'style="align-items: flex-end"';
                break;
            case 'center':
            case 'middle':
                $valign = 'style="align-items: center"';
                break;
            case 'stretch':
                $valign = 'style="align-items: stretch"';
                break;
            default:
                $valign = '';
        }
        return wpautop('<div class="rrze-elements elements-columns '.$colClass.'" ' . $valign . '>' . do_shortcode($content) . '</div>', false);
    }

    public function shortcodeColumn($atts, $content = null){
        $content = shortcode_unautop(trim($content));
        $defaults = array(
            'span' => '1',
            'valign' => '',
            'color' => '',
        );
        $spans = ['1', '2', '3'];
        $args = shortcode_atts($defaults, $atts);
        $classesArr = ['colspan-' . (in_array($args['span'], $spans) ? $args['span'] : $defaults['span'])];
        $stylesArr = [];
        switch ($args['valign']) {
            case 'top':
                $stylesArr[] = 'align-self: flex-start';
                break;
            case 'bottom':
                $stylesArr[] = 'align-self: flex-end';
                break;
            case 'center':
            case 'middle':
            $stylesArr[] = 'align-self: center';
                break;
            case 'stretch':
                $stylesArr[] = 'align-self: stretch';
                break;
        }
        if ((str_starts_with($args['color'], '#')) && (in_array(strlen($args['color']), [4, 7]))) {
            $stylesArr[] = 'background-color:' . $args['color'];
            $classesArr[] = 'has-background';
            $classesArr[] = (calculateContrastColor($atts['color']) == '#000000' ? 'font-dark' : 'font-light');
        }
        return '<div class="column ' . implode(' ', $classesArr) . '" style="' . implode('; ', $stylesArr) . '">' . wpautop(do_shortcode(shortcode_unautop($content)), false) . '</div>';
    }


    /**
     * Grid Cells
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeGrid($atts, $content = null)
    {
        $content = shortcode_unautop(trim($content));
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
        return wpautop('<div class="rrze-elements columns-grid" style="' . $cols . '">'
                . wpautop(do_shortcode(shortcode_unautop($content)), false)
                . '</div>');
    }

    /**
     * Shortcode style wrapper
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeGridCell($atts, $content = null)
    {
        $content = shortcode_unautop(trim($content));
        wp_enqueue_style('rrze-elements');
        return wpautop('<div class="column-grid">' . wpautop(do_shortcode(shortcode_unautop($content)), false) . '</div>');
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
        $content = shortcode_unautop(trim($content));
        wp_enqueue_style('rrze-elements');
        return '<div class="two-columns-one">' . wpautop(do_shortcode(shortcode_unautop($content)), false) . '</div>';
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
        $content = shortcode_unautop(trim($content));
        wp_enqueue_style('rrze-elements');
        return '<div class="two-columns-one last">' . wpautop(do_shortcode(shortcode_unautop($content)), false) . '</div>';
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
        $content = shortcode_unautop(trim($content));
        wp_enqueue_style('rrze-elements');
        return '<div class="three-columns-one">' . wpautop(do_shortcode(shortcode_unautop($content)), false) . '</div>';
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
        $content = shortcode_unautop(trim($content));
        wp_enqueue_style('rrze-elements');
        return '<div class="three-columns-one last">' . wpautop(do_shortcode(shortcode_unautop($content)), false) . '</div>';
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
        $content = shortcode_unautop(trim($content));
        wp_enqueue_style('rrze-elements');
        return '<div class="three-columns-two">' . wpautop(do_shortcode(shortcode_unautop($content)), false) . '</div>';
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
        $content = shortcode_unautop(trim($content));
        wp_enqueue_style('rrze-elements');
        return '<div class="three-columns-two last">' . wpautop(do_shortcode(shortcode_unautop($content)), false) . '</div>';
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
        $content = shortcode_unautop(trim($content));
        wp_enqueue_style('rrze-elements');
        return '<div class="four-columns-one">' . wpautop(do_shortcode(shortcode_unautop($content)), false) . '</div>';
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
        $content = shortcode_unautop(trim($content));
        wp_enqueue_style('rrze-elements');
        return '<div class="four-columns-one last">' . wpautop(do_shortcode(shortcode_unautop($content)), false) . '</div>';
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
        $content = shortcode_unautop(trim($content));
        wp_enqueue_style('rrze-elements');
        return '<div class="four-columns-two">' . wpautop(do_shortcode(shortcode_unautop($content)), false) . '</div>';
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
        $content = shortcode_unautop(trim($content));
        wp_enqueue_style('rrze-elements');
        return '<div class="four-columns-two last">' . wpautop(do_shortcode(shortcode_unautop($content)), false) . '</div>';
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
        $content = shortcode_unautop(trim($content));
        wp_enqueue_style('rrze-elements');
        return '<div class="four-columns-three">' . wpautop(do_shortcode(shortcode_unautop($content)), false) . '</div>';
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
        $content = shortcode_unautop(trim($content));
        wp_enqueue_style('rrze-elements');
        return '<div class="four-columns-three last">' . wpautop(do_shortcode(shortcode_unautop($content)), false) . '</div>';
    }

    /**
     * Shortcode divider.
     * @param  array $atts    [description]
     * @param  string $content [description]
     * @return string          [description]
     */
    public function shortcodeDivider($atts, $content = null)
    {
        $content = shortcode_unautop(trim($content));
        wp_enqueue_style('rrze-elements');
        return '<div class="elements-divider"></div>';
    }
}
