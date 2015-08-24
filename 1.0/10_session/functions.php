<?php

namespace k1lib\session;

//$k1_user_id = "";
//$k1_user_data = "";
//$k1_user_level = "";
//$k1_user_name = "";
//$k1_user_logged = FALSE;

/**
 * This is required to use the K1FW session system, THIS WILL OVERRIDE ANY OTHER SESSION
 * 
 * TODO: make this multisession for mutiple session management
 * 
 * @return boolean 
 */
function start_app_session() {
    k1_deprecated(__FUNCTION__);
}

/**
 * Check is the actual execution is on a K1FW session 
 * @global boolean $k1_user_logged
 * @global mixed $k1_user_id
 * @global string $k1_user_name
 * @global array $k1_user_data
 * @global mixed $k1_user_level
 * @param boolean $redirect
 * @return boolean 
 */
function on_app_session($redirect = TRUE, $url_to_redirect = "/") {
    k1_deprecated(__FUNCTION__);
}

/**
 * Makes the required SESSION array for the K1FW session system
 * @param type $k1_user_data
 * @param type $k1_user_id
 * @param type $k1_user_name
 * @param type $k1_user_level 
 */
function set_app_session($k1_user_data, $k1_user_id, $k1_user_name = FALSE, $k1_user_level = 0) {
    k1_deprecated(__FUNCTION__);
}

/**
 * Makes the required SESSION array for the K1FW session system with user groups functionality
 * @param type $k1_user_data
 * @param type $k1_user_id
 * @param type $k1_user_name
 * @param type $k1_user_level 
 */
function set_app_session_group($k1_user_data, $k1_user_id, $k1_user_name = FALSE, $k1_user_group = NULL, $permision_array = NULL) {
    k1_deprecated(__FUNCTION__);
}

/**
 *  Uses the PHP session functions 
 */
function unset_app_session() {
    k1_deprecated(__FUNCTION__);
}

/**
 * Generate the client MD5 to check if the current session is valid using the user_id, user_ip, user_agent and the magic_value
 * @param string $k1_user_id
 * @return type 
 */
function get_client_hash($k1_user_id = " ") {
    k1_deprecated(__FUNCTION__);
}

/**
 * Check the current user level VS a comma separated list as 1,2,3,4,5
 * @param string $levels_to_check
 * @return boolean
 */
function check_user_level($levels_to_check) {
    k1_deprecated(__FUNCTION__);
}

