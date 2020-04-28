<?php


namespace RRZE\Elements\Gallery;


class Gallery {
    public function __construct()
    {
        add_filter( 'post_gallery', 'shortcodeGallery', 10, 3 );
    }

    public function shortcodeGallery( $output = '', $atts = null, $instance = null ) {
        $return = $output; // fallback

        // retrieve content of your own gallery function
        $my_result = get_my_gallery_content( $atts );

        // boolean false = empty, see http://php.net/empty
        if( !empty( $my_result ) ) {
            $return = $my_result;
        }

        return $return;
    }
}