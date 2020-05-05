<?php


namespace RRZE\Elements\Gallery;


class Gallery {
    public function __construct()
    {
        add_filter( 'post_gallery', [$this, 'shortcodeGallery'], 10, 3 );
    }

    public function shortcodeGallery( $input = '', $atts = null, $instance = null ) {
        $return = $input; // fallback
        $output = '';

        if (isset($atts['orderby'])) {
            $atts['orderby'] = sanitize_sql_orderby($atts['orderby']);
            if (!$atts['orderby'])
                unset($atts['orderby']);
        }

        $atts = shortcode_atts( [
            'order'	=> 'ASC',
            'orderby'	=> 'menu_order ID',
            'ids'		=> '',
            'columns'	=> 0,
            'include'	=> '',
            'exclude'	=> '',
            'type'	=> 'default',
            'captions'	=> 'false',
            'link'	=> 'post',
            // aus Wizard:
            // file = direkt zur mediendatei
            // post = null = Anhang Seite   (Im WordPress Wizzard ist dies der Default!)
            // none = nirgendwohin
            'class'	=> '',
            'nodots'	> 0,
        ], $atts);
        if ($atts['captions'] != 'false') {
            $atts['class'] .= ' withcaption';
        }
        if (is_numeric($atts['columns']) && $atts['columns'] > 0) {
            $atts['class'] .= ' cols'.$atts['columns'];
        }

        if (!empty($atts['include'])) {
            $include = preg_replace('/[^0-9,]+/', '', $atts['include']);
            $attachments = get_posts([
                'include' => $include,
                'post_status' => 'inherit',
                'post_type' => 'attachment',
                'post_mime_type' => 'image',
                'order' => $atts['order'],
                'orderby' => $atts['orderby']
            ]);
        }

        if (empty($attachments)) return '';

        $output .= '<div class="elements-gallery">';
        switch ($atts['type']) {
            case 'slider':
                $output .= '<div id="slider" class="gallery-slider flexslider clearfix">'
                            . '<ul class="slides">';
                foreach ($attachments as $attachment) {
                    $output .= '<li><figure>'
                        . wp_get_attachment_image($attachment->ID, 'full');
                    if ($atts['captions'] != 'false' && $attachment->post_excerpt != '') {
                        $output .= '<figcaption class="wp-caption-text">' . $attachment->post_excerpt . '</figcaption>';
                    }
                    $output .= '</figure></li>';
                }
                $output .= '</ul>'
                    .  '</div>'
                    .  '<div id="carousel" class="gallery-slider-thumbs flexslider clearfix">'
                        . '<ul class="slides">';
                foreach ($attachments as $attachment) {
                    $output .= '<li>' . wp_get_attachment_image($attachment->ID, 'medium') . '</li>';
                }
                $output .= '</ul>'
                    . '</div>'
                    . '<script type="text/javascript"> jQuery(document).ready(function($) {
                            // The slider being synced must be initialized first
                            $("#carousel").flexslider({
                                animation: "slide",
                                controlNav: true,
                                animationLoop: false,
                                slideshow: false,
                                itemWidth: 210,
                                itemMargin: 5,
                                asNavFor: "#slider",
                                minItems: 3,
                            });
                            $("#slider").flexslider({
                                animation: "slide",
                                controlNav: false,
                                animationLoop: false,
                                slideshow: false,
                                sync: "#carousel"
                            });
                        });</script>';

                wp_enqueue_script('jquery-flexslider');
            break;
            case 'grid':
                $output .= '<div class="gallery-grid ' . $atts['class'] . '">';
                foreach ($attachments as $attachment) {
                    $output .= '<figure>'
                        . wp_get_attachment_image($attachment->ID, 'full');
                    if ($atts['captions'] != 'false') {
                        $output .= '<figcaption class="wp-caption-text">';
                        if ($attachment->post_excerpt != '') {
                            $output .= $attachment->post_excerpt;
                        }
                        $output .= '</figcaption>';
                    }
                    $output .= '</figure>';
                }
                $output .= '</div>';
            default:

                break;
        }
        $output .= '</div>';

        if ($output != '') {
            $return = $output;
        }

        wp_enqueue_style('rrze-elements');
        return $return;
    }
}