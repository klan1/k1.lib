<?php

namespace k1lib\options\classes;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of standar-config-class
 *
 * @author J0hnD03
 */
class standar_options_class {

    private $options = array();

    public function add_option($option_name, $option_value) {
        $this->options[$option_name] = $option_value;
    }

    public function get_option($option_name) {
        if (isset($this->options[$option_name])) {
            return $this->options[$option_name];
        } else {
            return false;
        }
    }

//put your code here
}
