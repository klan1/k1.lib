<?php

namespace k1lib\html;

class template {

    /**
     * Enable state
     * @var bool 
     */
    static private $enabled = FALSE;
    static private $template_path = NULL;
    static private $templates_loaded = [];

//    static private $js_path = NULL;
//    static private $css_path = NULL;
//    static private $images_path = NULL;

    /**
     * Enable the engenie
     */
//    static public function enable($app_url, $template_path, $css_path = 'css/', $js_path = 'js/', $images_path = 'imgages/') {
    static public function enable($template_path) {
        self::$enabled = TRUE;
        if (file_exists($template_path)) {
            self::$template_path = $template_path;
            self::load_template('scripts/definition');

//            if (file_exists($template_path . $css_path)) {
//                self::$css_path = $template_path . $css_path;
//            }
//            if (file_exists($template_path . $js_path)) {
//                self::$js_path = $template_path . $js_path;
//            }
//            if (file_exists($template_path . $images_path)) {
//                self::$images_path = $template_path . $images_path;
//            }
        } else {
            self::error_500('The template path do not exist: ' . $template_path);
        }
    }

    static public function error_500($error_message) {
        header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
        DOM::start();
        DOM::html_document()->body()->append_h1('500 Internal error');
        DOM::html_document()->body()->append_p($error_message);
        echo DOM::generate();
        trigger_error('App error fired', E_USER_NOTICE);
        exit;
    }

    /**
     * Query the enabled state
     * @return Boolean
     */
    static public function is_enabled($show_error = false) {
        if ($show_error && (!self::$enabled)) {
            self::error_500('Template load system is not enabled yet: ' . $template_path);
        }
        return self::$enabled;
    }

    static public function load_template($template_name) {
//        d($template_name);
        if (!array_key_exists($template_name, self::$templates_loaded)) {
            self::$templates_loaded[$template_name] = TRUE;
//            d(self::$templates_loaded);
            $template_name = str_replace('/', DIRECTORY_SEPARATOR, $template_name);
            self::is_enabled(true);
            if (is_string($template_name)) {
                $template_to_load = self::template_exist($template_name);
                if ($template_to_load) {
                    include $template_to_load;
                } else {
                    trigger_error("Template ($template_name) do not exist", E_USER_ERROR);
                }
            } else {
                trigger_error("The template names value only can be string", E_USER_ERROR);
            }
        }
    }

    static public function template_exist($template_name) {
        self::is_enabled(true);
        if (is_string($template_name)) {
            // Try with subfolder scheme
            $template_to_load = self::$template_path . "/{$template_name}.php";
            if (file_exists($template_to_load)) {
                return $template_to_load;
            } else {
                trigger_error("Template ($template_to_load) is not on disk", E_USER_ERROR);
            }
        }
        return FALSE;
    }

}
