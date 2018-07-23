<?php

namespace RRZE\Elements;

use RRZE\Elements\Accordion\Accordion;
use RRZE\Elements\Alert\Alert;
use RRZE\Elements\Button\Button;
use RRZE\Elements\Columns\Columns;
use RRZE\Elements\ContentSlider\ContentSlider;
use RRZE\Elements\LaTeX\LaTeX;
use RRZE\Elements\News\News;
use RRZE\Elements\Notice\Notice;
use RRZE\Elements\PullDiv\PullDiv;
use RRZE\Elements\TimeLine\TimeLine;

defined('ABSPATH') || exit;

class Main {
    
    public $plugin_basename;

    public function __construct($plugin_basename) {
        $this->plugin_basename = $plugin_basename;
        
        remove_filter('the_content', 'wpautop');
        add_filter('the_content', 'wpautop', 12);
        
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        
        $accordion = new Accordion($this);
        $alert = new Alert();
        $button = new Button();
        $columns = new Columns();
        $content_slider = new ContentSlider($this);
        $latex = new LaTeX();
        $news = new News();
        $notice = new Notice();
        $pulldiv = new PullDiv();
        $timeline = new TimeLine($this);
    }
    
    public function enqueue_scripts() {
        if (is_404()|| is_search()) {
            return;
        }
        
        if (!wp_style_is('fontawesome') || !wp_style_is('font-awesome')) {
            wp_register_style('fontawesome', plugins_url('css/font-awesome.css', $this->plugin_basename));
        }
        
        wp_register_style('rrze-elements', plugins_url('css/rrze-elements.css', $this->plugin_basename));
    }
}
