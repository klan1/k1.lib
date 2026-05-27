<?php

/**
 * @license Apache-2.0
 * @package k1lib
 * @subpackage controllers
 * Controller Functions - Utility functions for loading and managing controllers.
 */

namespace k1lib\controllers;

use k1lib\html\DOM;

/**
 * Load a controller file and return the path for inclusion.
 * 
 * Attempts to load a controller using two strategies: first as a direct file,
 * then as a directory with index.php. Returns the path on success or NULL
 * if return_error is TRUE and the controller cannot be found.
 * 
 * @param string $controller_name The controller file name to load (without extension)
 * @param string $controllers_path Base directory path where controllers are located
 * @param bool $return_error If TRUE, return NULL on error instead of triggering error
 * @param bool $api_mode If TRUE, send JSON 400 response on error instead of HTML
 * @return string|null Path to the controller file, or NULL if not found and return_error is TRUE
 */
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

/**
 * Display a 404 error page and terminate execution.
 * 
 * Outputs an HTTP 404 response with an HTML page indicating the requested
 * controller was not found, then exits the script.
 * 
 * @param string $non_found_name Name of the controller that was not found
 * @return void
 */
function error_404($non_found_name) {
    http_response_code(404);
    header("Access-Control-Allow-Origin: *");
    DOM::start();
    DOM::html()->body()->append_h1('404 Not found');
    DOM::html()->body()->append_p('The controller file \'' . $non_found_name . '\' is not on path.');
    echo DOM::generate();
    trigger_error('App error fired', E_USER_NOTICE);
    exit;
}

/**
 * Load a template file and return its path.
 * 
 * Searches for the template file in the specified directory using the subfolder
 * scheme (templates/NAME.php). Triggers an error if the template is not found.
 * 
 * @param string $template_name Name of the template to load
 * @param string $path_to_use Base directory path where templates are located
 * @return string|false Path to the template file, or FALSE on error (triggers error)
 */
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

/**
 * Check if a template file exists and return its path.
 * 
 * Uses the subfolder scheme to locate templates (path/NAME.php).
 * Triggers an error if the template file does not exist.
 * 
 * @param string $template_name Name of the template to check
 * @param string $path_to_use Base directory path where templates are located
 * @return string|false Path to the template file, or FALSE if not found
 */
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
