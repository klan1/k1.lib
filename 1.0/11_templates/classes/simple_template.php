<?php

namespace k1lib\templates\classes;

class simple_template {

    /**
     * Enable state
     * @var Boolean 
     */
    static private $enabled = FALSE;

    /**
     * Actual URL level 
     * @var Int
     */
    static private $levels_count;

    /**
     * URL data array
     * @var Array
     */
    static private $output_places;

    /**
     * Enable the engenie
     */
    static public function enable() {
        self::$enabled = TRUE;
        self::$levels_count = null;
        self::$output_places = array();
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
        return self::$output_places;
    }

    static public function  is_place_registered($place_name) {
        global $output_places;
        if (!is_string($place_name)) {
            \k1lib\common\show_error("The place name HAS to be a string", __FUNCTION__, TRUE);
        }
        if (isset($output_places[$place_name])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static public function  register_place($place_name) {
        global $output_places;
        if (!is_string($place_name)) {
            \k1lib\common\show_error("The place name HAS to be a string", __FUNCTION__, TRUE);
        }
        $output_places[$place_name] = array();
    }

    /**
     * set the value for a place name
     * @global array $output_places
     * @param string $place
     * @param string $value
     * @return none
     */
    static public function  set_place_value($place_name, $value) {
        global $output_places;
        if ((!is_string($place_name) || !is_string($value))) {
            die("The OUTPUT PLACE '{$place_name}' couldn't be registered with a value diferent a string " . __FUNCTION__);
        }
        if (isset($output_places[$place_name])) {
            $output_places[$place_name][] = $value;
        } else {
            die("The OUTPUT PLACE '{$place_name}' is not defined yet " . __FUNCTION__);
        }
    }

    /**
     * output the place name string on the template
     * Rev 1: Now register the place name if is not registererd
     */
    static public function  set_template_place($place_name) {
        //check if the place name exist
        if (!\k1lib\templates\is_place_registered($place_name)) {
            \k1lib\templates\register_place($place_name);
        }
        // only strings for the place name
        if (!is_string($place_name)) {
            \k1lib\common\show_error("The place name HAS to be a string", __FUNCTION__, TRUE);
        }
        // prints the place html code
        echo \k1lib\templates\convert_place_name($place_name) . "\n";
    }

    /**
     * convert a place name to the way k1.lib handle the space names
     * @param string $place_name
     * @return type string
     */
    static public function  convert_place_name($place_name) {
        if (!is_string($place_name)) {
            \k1lib\common\show_error("The place name HAS to be a string", __FUNCTION__, TRUE);
        }
        return "<!-- K1_TEMPLATE_PLACE_" . strtoupper($place_name) . "-->";
    }

    static public function  register_header($url, $relative = FALSE, $type = "auto") {
        if (!is_string($url)) {
            \k1lib\common\show_error("The URL HAS to be a string", __FUNCTION__, TRUE);
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
                $code = "<script src=\"%url%\" type=\"text/javascript\"></script>";
                break;
            case 'css':
                $code = "<link href=\"%url%\" rel=\"stylesheet\" type=\"text/css\"/>";
                break;
            default:
                \k1lib\common\show_error("no extension detected on [$url] ", __FUNCTION__, TRUE);
                return FALSE;
                break;
        }
        if ($relative) {
            $code = str_replace("%url%", \k1lib\urlrewrite\classes\url_manager::get_app_link($url), $code);
        } else {
            $code = str_replace("%url%", $url, $code);
        }
        return \k1lib\templates\set_place_value("header", $code);
    }

    static public function  register_footer($url, $relative = FALSE, $type = "auto") {
        if (!is_string($url)) {
            \k1lib\common\show_error("The URL HAS to be a string", __FUNCTION__, TRUE);
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
                $code = "<script src=\"%url%\" type=\"text/javascript\"></script>";
                break;
            case 'css':
                $code = "<link href=\"%url%\" rel=\"stylesheet\" type=\"text/css\"/>";
                break;
            default:
                \k1lib\common\show_error("no extension detected on [$url] ", __FUNCTION__, TRUE);
                return FALSE;
                break;
        }
        if ($relative) {
            $code = str_replace("%url%", \k1lib\urlrewrite\classes\url_manager::get_app_link($url), $code);
        } else {
            $code = str_replace("%url%", $url, $code);
        }
        return \k1lib\templates\set_place_value("footer", $code);
    }

    static public function  parse_template_places($buffer) {
        global $output_places;
        if (!isset($buffer)) {
            \k1lib\common\show_error("The BUFFER is empty!", __FUNCTION__, TRUE);
        }
        if (count($output_places) > 0) {
            foreach ($output_places as $place_name => $place_data) {
                $template_place_name = \k1lib\templates\convert_place_name($place_name);
                $place_code = "\n";
                foreach ($place_data as $place_value) {
                    $place_code .= "\t" . $place_value . "\n";
                }
                $buffer = str_replace($template_place_name, $place_code, $buffer);
            }
        }
        return $buffer;
    }

    static public function  load_template($template_name) {
        if (is_string($template_name)) {
            if ($template_to_load = \k1lib\templates\template_exist($template_name)) {
                return$template_to_load;
            }
        } else {
            trigger_error("The template names value only can be string");
            exit;
        }
    }

    static public function  template_exist($template_name) {
        if (is_string($template_name)) {
            // Try with subfolder scheme
            $template_to_load = APP_TEMPLATE_PATH . "/{$template_name}/index.php";
            if (file_exists($template_to_load)) {
                return $template_to_load;
            } else {
                // Try with single file scheme
                $template_to_load = APP_TEMPLATE_PATH . "/{$template_name}.php";
                if (file_exists($template_to_load)) {
                    return $template_to_load;
                }
            }
        }
        return FALSE;
    }

    static public function  load_view($view_name, $view_path = APP_VIEWS_PATH, $js_auto_load = TRUE) {
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
                        \k1lib\templates\register_header($js_to_load);
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
                            \k1lib\templates\register_header($js_to_load);
                        }
                    }
                    return $view_to_load;
                } else {
                    die("The view '{$view_to_load}' could not be found " . __FUNCTION__);
                }
            }
        } else {
            trigger_error("The view name value only can be string");
            exit;
        }
    }

}
