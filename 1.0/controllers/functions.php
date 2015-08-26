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

/**
 * Return the controller PATH for include it, we cant do it from the function by the var scope, so we do here some esential checks before the include
 * @global array $url_data url_rewrite data variable from K1FW
 * @param string $controller_name Just the file name 
 * @return string correct path to include the file name recived on $controller_name
 */
//function load_controller($controller_name, $query_auto_load = TRUE) {
function load_controller($controller_name, $controllers_path) {
    $controller_query_file = FALSE;
    if (is_string($controller_name)) {
        // Try with subfolder scheme
        $controller_subfix = $controllers_path . "/{$controller_name}";

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
                \trigger_error("The controller '{$controller_name}' could not be found", E_USER_ERROR);
            }
        }
    } else {
        \trigger_error("The controller name value only can be string", E_USER_ERROR);
        exit;
    }
}