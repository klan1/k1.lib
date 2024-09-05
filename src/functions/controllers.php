<?php

/**
 * Controller related functions, K1.lib.
 * 
 * This are my controller use propose.
 * @author J0hnD03 <alejandro.trujillo@klan1.com>
 * @version 1.0
 * @package controllers
 */

namespace k1lib\controllers;

use k1lib\html\DOM as DOM;

/**
 * Return the controller PATH for include it, we cant do it from the function by the var scope, so we do here some esential checks before the include
 * @global array $url_data url_rewrite data variable from K1FW
 * @param string $controller_name Just the file name 
 * @return string correct path to include the file name recived on $controller_name
 */
//function load_controller($controller_name, $query_auto_load = TRUE) {
function load_controller($controller_name, $controllers_path, $return_error = FALSE, $api_mode = FALSE) {
//    d($controllers_path);
//    $controller_query_file = FALSE;
    if (is_string($controller_name)) {
        // Try with subfolder scheme
        $controller_subfix = $controllers_path . "{$controller_name}";

        $controller_to_load = $controller_subfix . ".php";
        if (file_exists($controller_to_load)) {
            return $controller_to_load;
        } else {
            // Try with single file scheme
            $controller_to_load = $controller_subfix . "/index.php";
            if (file_exists($controller_to_load)) {
                // QUERY Auto load
                return $controller_to_load;
            } else {
                if (!$return_error) {
                    if ($api_mode) {
                        $error = new \k1lib\api\base();
                        $error->send_response(400, ['message' => 'Not found: ' . $controller_name]);
                    } else {
                        error_404($controller_name);
                    }
                } else {
                    return NULL;
                }
//                \trigger_error("The controller '{$controller_name}' could not be found on '{$controllers_path}'", E_USER_ERROR);
//                return false;
            }
        }
    } else {
        \trigger_error("The controller name value only can be string", E_USER_ERROR);
        exit;
    }
}

function error_404($non_found_name) {
    http_response_code(404);
    header("Access-Control-Allow-Origin: *");
    DOM::start();
    DOM::html_document()->body()->append_h1('404 Not found');
    DOM::html_document()->body()->append_p('The controller file \'' . $non_found_name . '\' is not on path.');
    echo DOM::generate();
    trigger_error('App error fired', E_USER_NOTICE);
    exit;
}

function load_template($template_name, $path_to_use) {
    if (is_string($template_name)) {
        if ($template_to_load = template_exist($template_name, $path_to_use)) {
            return $template_to_load;
        } else {
            trigger_error("Template ($template_name) do not exist", E_USER_ERROR);
        }
    } else {
        trigger_error("The template names value only can be string", E_USER_ERROR);
    }
}

function template_exist($template_name, $path_to_use) {
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
