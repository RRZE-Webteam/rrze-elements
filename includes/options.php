<?php

namespace RRZE\Elements;

defined('ABSPATH') || exit;

class Options {
    
    protected $option_name = 'rrze_elements';
    
    public function __construct() {
        
    }
    
    /*
     * Standard Einstellungen werden definiert
     * @return array
     */
    public function default_options() {
        $options = array(
            'rrze_elements_field_1' => '',
            // Hier können weitere Felder ('key' => 'value') angelegt werden.
        );

        return $options;
    }

    /*
     * Gibt die Einstellungen zurück.
     * @return object
     */
    public function get_options() {
        $defaults = self::default_options();

        $options = (array) get_option($this->option_name);
        $options = wp_parse_args($options, $defaults);
        $options = array_intersect_key($options, $defaults);

        return (object) $options;
    }
    
    public function get_option_name() {
        return $this->option_name;
    }
    
}
