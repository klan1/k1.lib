<?php

/**
 * Return the controller PATH for include it, we cant do it from the function by the var scope, so we do here some esential checks before the include
 * @global array $url_data url_rewrite data variable from K1FW
 * @param string $controller_name Just the file name 
 * @return string correct path to include the file name recived on $controller_name
 */
function k1_load_controller($controller_name, $query_auto_load = true) {
    global $url_data, $controller_query_file;
    $controller_query_file = false;
    if (is_string($controller_name)) {
        // Try with subfolder scheme
        $controller_subfix = APP_CONTROLLERS_PATH . "/{$controller_name}";

        $controller_to_load = $controller_subfix . "/index.php";
        if (file_exists($controller_to_load)) {
            // QUERY Auto load
            if ($query_auto_load) {
                $last_controles_name = basename($controller_name);
                $query_to_load = "{$controller_subfix}/query/{$last_controles_name}.php";
                if (file_exists($query_to_load)) {
                    $controller_query_file = $query_to_load;
                }
            }
            return $controller_to_load;
        } else {
            // Try with single file scheme
            $controller_to_load = $controller_subfix . ".php";
            if (file_exists($controller_to_load)) {
                // QUERY Auto load
                if ($query_auto_load) {
                    $last_controles_name = basename(($controller_name));
                    $query_to_load = dirname($controller_subfix) . "/query/{$last_controles_name}.php";
                    if (file_exists($query_to_load)) {
                        $controller_query_file = $query_to_load;
                    }
                }
                return $controller_to_load;
            } else {
                die("The controller '{$controller_name}' could not be found " . __FUNCTION__);
            }
        }
    } else {
        die("The controller name value only can be string");
        exit;
    }
}

/**
 * Check if the controller exist on the designed path on the K1FW config (APP_CONTROLLERS_PATH)
 * @global array $url_data url_rewrite data variable from K1FW
 * @param string $controller_name Just the file name 
 * @return boolean tell if exist or not the file on $controller_name
 */
function k1_if_exist_controller($controller_name) {
    global $url_data;
    if (is_string($controller_name)) {
        $controller_to_load = APP_CONTROLLERS_PATH . "/{$controller_name}.php";
        if (file_exists($controller_to_load)) {
            return true;
        } else {
            return false;
        }
    }
}

?>
