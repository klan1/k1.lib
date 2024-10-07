<?php

namespace k1lib\templates;

class _temply {

    /**
     * Enable state
     * @var Boolean 
     */
    static private $enabled = FALSE;

    /**
     * URL data array
     * @var Array
     */
    static private $output_places;

    /**
     * Enable the engenie
     */
    static public function enable($app_mode) {
        self::$enabled = TRUE;
        self::$output_places = array();
        if ($app_mode == "web") {
            \ob_start('\k1lib\templates\temply::parse_template_places');
//            \ob_start();
        }
    }

    static public function end($app_mode) {
        if ($app_mode == "web") {
            \ob_end_flush();
        }
    }

    /**
     * Query the enabled state
     * @return Boolean
     */
    static public function is_enabled($show_error = false) {
        if ($show_error && (!self::$enabled)) {
            trigger_error("URL Rewrite system is not enabled yet", E_USER_ERROR);
        }
        return self::$enabled;
    }

    static public function get_data() {
        self::is_enabled(true);
        return self::$output_places;
    }

    static public function is_place_registered($place_name) {
        self::is_enabled(true);

        if (!is_string($place_name)) {
            \trigger_error("The place name HAS to be a string", E_USER_ERROR);
        }
        if (isset(self::$output_places[$place_name])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static public function register_place($place_name) {
        trigger_error("Do not use me " . __METHOD__, E_USER_WARNING);
        self::is_enabled(true);
        if (!is_string($place_name)) {
            \trigger_error("The place name HAS to be a string", E_USER_ERROR);
        }
        self::$output_places[$place_name] = array();
    }

    /**
     * set the value for a place name
     * @global array self::$output_places
     * @param string $place
     * @param string $value
     * @return none
     */
    static public function set_place_value($place_name, $value) {
        trigger_error("Do not use me " . __METHOD__, E_USER_WARNING);
        self::is_enabled(true);

        if (empty($value)) {
            return FALSE;
        }

        if (!is_string($place_name)) {
            trigger_error("The OUTPUT PLACE '{$place_name}' couldn't be registered with a value diferent a string " . __FUNCTION__, E_USER_WARNING);
        }
        if (!is_string($value)) {
            if (!is_object($value)) {
                trigger_error("The OUTPUT VALUE '$place_name'->$value couldn't be used " . __FUNCTION__, E_USER_ERROR);
            } elseif (strstr(get_class($value), 'k1lib\html\\') === false) {
                trigger_error("The OUTPUT VALUE as object diferent from html couldn't be used, now is (" . get_class($value) . ") " . __FUNCTION__, E_USER_ERROR);
            }
        }

        if (isset(self::$output_places[$place_name])) {
            self::$output_places[$place_name][] = $value;
        } else {
            die("The OUTPUT PLACE '{$place_name}' is not defined yet " . __FUNCTION__);
        }
    }

    /**
     * get the value for a place name
     * @global array self::$output_places
     * @param string $place
     * @param string $value
     * @return none
     */
    static public function get_place_value($place_name) {
        self::is_enabled(true);

        if (!is_string($place_name)) {
            trigger_error("The OUTPUT PLACE '{$place_name}' couldn't be registered with a value diferent a string " . __FUNCTION__, E_USER_WARNING);
        }
        if (isset(self::$output_places[$place_name]) && (count(self::$output_places[$place_name]) > 0)) {
            return implode("\n", self::$output_places[$place_name]);
        } else {
            return false;
        }
    }

    /**
     * output the place name string on the template
     * Rev 1: Now register the place name if is not registererd
     */
    static public function set_template_place($place_name) {
        trigger_error("Do not use me " . __METHOD__, E_USER_WARNING);
        self::is_enabled(true);
        //check if the place name exist
        if (!self::is_place_registered($place_name)) {
            self::register_place($place_name);
        }
        // only strings for the place name
        if (!is_string($place_name)) {
            \trigger_error("The place name HAS to be a string", E_USER_ERROR);
        }
        // prints the place html code
        return self::convert_place_name($place_name) . "\n";
    }

    /**
     * convert a place name to the way k1.lib handle the space names
     * @param string $place_name
     * @return type string
     */
    static public function convert_place_name($place_name) {
        self::is_enabled(true);
        if (!is_string($place_name)) {
            \trigger_error("The place name HAS to be a string", E_USER_ERROR);
        }
        return "<!-- K1_TEMPLATE_PLACE_" . strtoupper($place_name) . "-->";
    }

    static public function register_header($url, $relative = FALSE, $type = "auto") {
        trigger_error("Do not use me " . __METHOD__, E_USER_WARNING);
        self::is_enabled(true);
        if (!is_string($url)) {
            \trigger_error("The URL HAS to be a string", E_USER_ERROR);
        }
        if ($type == "auto") {
            $file_extension = \k1lib\common\get_file_extension($url);
        } else {
            $file_extension = $type;
        }
        if (!$file_extension) {
            return FALSE;
        }
        switch ($file_extension) {
            case 'js':
                $code = "<script src=\"%url%\"></script>";
                break;
            case 'css':
                $code = "<link href=\"%url%\" rel=\"stylesheet\" />";
                break;
            default:
                \trigger_error("no extension detected on [$url] ", E_USER_ERROR);
                return FALSE;
                break;
        }
        if ($relative) {
            $code = str_replace("%url%", \k1lib\urlrewrite\url::do_url($url, [], FALSE), $code);
        } else {
            $code = str_replace("%url%", $url, $code);
        }
        return self::set_place_value("header", $code);
    }

    static public function register_footer($url, $relative = FALSE, $type = "auto") {
        trigger_error("Do not use me " . __METHOD__, E_USER_WARNING);
        self::is_enabled(true);
        if (!is_string($url)) {
            \trigger_error("The URL HAS to be a string", E_USER_ERROR);
        }
        if ($type == "auto") {
            $file_extension = \k1lib\common\get_file_extension($url);
        } else {
            $file_extension = $type;
        }
        if (!$file_extension) {
            return FALSE;
        }
        switch ($file_extension) {
            case 'js':
                $code = "<script src=\"%url%\"></script>";
                break;
            case 'css':
                $code = "<link href=\"%url%\" rel=\"stylesheet\">";
                break;
            default:
                \trigger_error("no extension detected on [$url] ", E_USER_ERROR);
                return FALSE;
                break;
        }
        if ($relative) {
            $code = str_replace("%url%", \k1lib\urlrewrite\url::do_url($url, [], FALSE), $code);
        } else {
            $code = str_replace("%url%", $url, $code);
        }
        return self::set_place_value("footer", $code);
    }

    static public function parse_template_places($buffer) {
        self::is_enabled(true);

        if (!isset($buffer)) {
            \trigger_error("The BUFFER is empty", E_USER_ERROR);
        }
        if (count(self::$output_places) > 0) {
            foreach (self::$output_places as $place_name => $place_data) {
                $template_place_name = self::convert_place_name($place_name);
                $place_code = "\n";
                foreach ($place_data as $place_value) {
                    $place_code .= "\t" . $place_value . "\n";
                }
                $buffer = str_replace($template_place_name, $place_code, $buffer);
            }
        }
        return $buffer;
    }

    static public function load_template($template_name, $path_to_use) {
        self::is_enabled(true);
        if (is_string($template_name)) {
            if ($template_to_load = self::template_exist($template_name, $path_to_use)) {
                return $template_to_load;
            } else {
                trigger_error("Template ($template_name) do not exist", E_USER_ERROR);
            }
        } else {
            trigger_error("The template names value only can be string", E_USER_ERROR);
        }
    }

    static public function template_exist($template_name, $path_to_use) {
        self::is_enabled(true);
        if (is_string($template_name)) {
            // Try with subfolder scheme
            $template_to_load = $path_to_use . "/{$template_name}.php";
            if (file_exists($template_to_load)) {
                return $template_to_load;
            } else {
                trigger_error("Template ($template_to_load) is not on disk", E_USER_ERROR);
            }
        }
        return FALSE;
    }

    static public function load_view($view_name, $view_path, $js_auto_load = TRUE) {
        trigger_error("Do not use me " . __METHOD__, E_USER_WARNING);
        self::is_enabled(true);
        if (is_string($view_name)) {
            // Try with subfolder scheme
            $view_subfix = $view_path . "/{$view_name}";
            $js_view_subfix = $view_path . "/{$view_name}";

            $view_to_load = $view_subfix . "/index.php";

            if (file_exists($view_to_load)) {
                // JS Auto load
                if ($js_auto_load) {
                    $last_controles_name = basename($view_name);
                    $js_to_load = "{$js_view_subfix}/js/{$last_controles_name}.js";
                    $js_file_to_check = "{$view_subfix}/js/{$last_controles_name}.js";
                    if (file_exists($js_file_to_check)) {
                        self::register_header($js_to_load);
                    }
                }
                return $view_to_load;
            } else {
// Try with single file scheme
                $view_to_load = $view_subfix . ".php";
                if (file_exists($view_to_load)) {
                    // JS Auto load
                    if ($js_auto_load) {
                        $last_controles_name = basename(($view_name));
                        $js_to_load = dirname($js_view_subfix) . "/js/{$last_controles_name}.js";
                        $js_file_to_check = dirname($view_subfix) . "/js/{$last_controles_name}.js";
                        if (file_exists($js_file_to_check)) {
                            self::register_header($js_to_load);
                        }
                    }
                    return $view_to_load;
                } else {
//                    trigger_error(__METHOD__ . " : The view '{$view_to_load}' could not be found", E_USER_NOTICE);
                    return FALSE;
                }
            }
        } else {
            trigger_error("The view name value only can be string", E_USER_ERROR);
            exit;
        }
    }

}
