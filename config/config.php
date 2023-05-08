<?php

namespace RRZE\Elements\Config;

defined('ABSPATH') || exit;

/**
 * Gibt der Name der Option zurück.
 * @return array [description]
 */
function getThemeGroup($value = '') {
    $themes = [
        'fau' => [
            'FAU-Einrichtungen',
            'FAU-Einrichtungen-BETA',
            'FAU-Philfak',
            'FAU-Medfak',
            'FAU-Techfak',
            'FAU-Natfak',
            'FAU-RWFak',
            'Fau-Blog',
        ],
        'rrze' => ['rrze-2019'],
        'events' => ['FAU-Events'],
    ];
    foreach ($themes as $group=>$theme) {
        if (in_array($value, $theme))
            return $group;
    }
    return false;
}

/*-----------------------------------------------------------------------------------*/
/* Calculate Contrast Color
/*-----------------------------------------------------------------------------------*/
function calculateContrastColor( $color ) {
    $color = str_replace( '#', '', $color );
    $r = hexdec( substr( $color, 0, 2 ) );
    $g = hexdec( substr( $color, 2, 2 ) );
    $b = hexdec( substr( $color, 4, 2 ) );
    $d = '#000';
    // Counting the perceptive luminance - human eye favors green color...
    $luminance = ( 0.299 * $r + 0.587 * $g + 0.114 * $b ) / 255;
    if ( $luminance < 0.5 ) {
        $d = '#fff';
    }

    return $d;
}