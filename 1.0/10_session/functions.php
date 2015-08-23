<?php

namespace k1lib\session;

$k1_user_id = "";
$k1_user_data = "";
$k1_user_level = "";
$k1_user_name = "";
$k1_user_logged = false;

/**
 * This is required to use the K1FW session system, THIS WILL OVERRIDE ANY OTHER SESSION
 * 
 * TODO: make this multisession for mutiple session management
 * 
 * @return boolean 
 */
function start_app_session($session_name = "") {
    if (!defined("\k1app\IN_K1APP")) {
        die("You are not on K1FW " . __FUNCTION__);
        return false;
    }

    if (!defined("APP_SESSION_NAME")) {
        define("APP_SESSION_NAME", "K1SID");
    }

    //PHP session system
    session_name(\k1app\APP_SESSION_NAME);
    session_start();

    if (!\k1lib\session\on_app_session(FALSE)) {
        // Begin the app session vars
        $_SESSION['k1_app_session']['started'] = true;
        $_SESSION['k1_app_session']['loged'] = null;
        $_SESSION['k1_app_session']['user_id'] = null;
        $_SESSION['k1_app_session']['user_data'] = null;
        $_SESSION['k1_app_session']['user_name'] = null;
        $_SESSION['k1_app_session']['user_level'] = 0;
        $_SESSION['k1_app_session']['user_hash'] = null;
    }
    return true;
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
function on_app_session($redirect = true, $url_to_redirect = "/") {
    global $k1_user_logged, $k1_user_id, $k1_user_name, $k1_user_data, $k1_user_level;
    if ($redirect) {
        global $url_save_flag, $last_url;
        $url_save_flag = \k1lib\forms\unserialize_var("url-save-flag");
        $last_url = \k1lib\urlrewrite\get_app_link(str_replace(APP_DIR, "", $_SERVER['REQUEST_URI']));
        \k1lib\forms\serialize_var($url_save_flag, "url-save-flag");
        \k1lib\forms\serialize_var($last_url, "last-url");
    }
    if (isset($_SESSION['k1_app_session']['started']) && ($_SESSION['k1_app_session']['started'] == true)) {
        if ($_SESSION['k1_app_session']['user_hash'] == \k1lib\session\get_client_hash($_SESSION['k1_app_session']['user_id'])) {
            $k1_user_id = $_SESSION['k1_app_session']['user_id'];
            $k1_user_data = $_SESSION['k1_app_session']['user_data'];
            $k1_user_name = $_SESSION['k1_app_session']['user_name'];
            $k1_user_level = $_SESSION['k1_app_session']['user_level'];
            $k1_user_logged = true;
            return true;
        } else {
            $actual_session_id = session_id();
            if (empty($actual_session_id)) {
                \k1lib\session\unset_app_session();
            }
        }
    }
    if ($redirect) {
        setcookie("K1_LAST_URL", APP_DOMAIN_URL . $_SERVER['REQUEST_URI'], 0, APP_DIR);
        \k1lib\html\html_header_go(APP_LOGIN_URL . "?error=not-logged", false);
        exit;
    }
    return false;
}

/**
 * Makes the required SESSION array for the K1FW session system
 * @param type $k1_user_data
 * @param type $k1_user_id
 * @param type $k1_user_name
 * @param type $k1_user_level 
 */
function set_app_session($k1_user_data, $k1_user_id, $k1_user_name = false, $k1_user_level = 0) {
    if (is_array($k1_user_data)) {
        $_SESSION['k1_app_session']['user_data'] = $k1_user_data;
    } else {
        die("user_data MUST be an array with user data" . __FUNCTION__);
    }
    $_SESSION['k1_app_session']['started'] = true;
    $_SESSION['k1_app_session']['loged'] = true;
    $_SESSION['k1_app_session']['user_id'] = $k1_user_id;
    $_SESSION['k1_app_session']['user_name'] = $k1_user_name;
    $_SESSION['k1_app_session']['user_level'] = $k1_user_level;
    $_SESSION['k1_app_session']['user_hash'] = \k1lib\session\get_client_hash($k1_user_id);

    global $url_save_flag;
    $url_save_flag = true;
    \k1lib\forms\serialize_var($url_save_flag, "url-save-flag");
}

/**
 * Makes the required SESSION array for the K1FW session system with user groups functionality
 * @param type $k1_user_data
 * @param type $k1_user_id
 * @param type $k1_user_name
 * @param type $k1_user_level 
 */
function set_app_session_group($k1_user_data, $k1_user_id, $k1_user_name = false, $k1_user_group = null, $permision_array = null) {
    if (is_array($k1_user_data)) {
        $_SESSION['k1_app_session']['user_data'] = $k1_user_data;
    } else {
        die("user_data MUST be an array with user data" . __FUNCTION__);
    }
    $_SESSION['k1_app_session']['started'] = true;
    $_SESSION['k1_app_session']['loged'] = true;
    $_SESSION['k1_app_session']['user_id'] = $k1_user_id;
    $_SESSION['k1_app_session']['user_name'] = $k1_user_name;
    $_SESSION['k1_app_session']['user_level'] = $k1_user_level;
    $_SESSION['k1_app_session']['user_hash'] = \k1lib\session\get_client_hash($k1_user_id);

    global $url_save_flag;
    $url_save_flag = true;
    \k1lib\forms\serialize_var($url_save_flag, "url-save-flag");
}

/**
 *  Uses the PHP session functions 
 */
function unset_app_session() {
    session_destroy();
    session_unset();
}

/**
 * Generate the client MD5 to check if the current session is valid using the user_id, user_ip, user_agent and the magic_value
 * @param string $k1_user_id
 * @return type 
 */
function get_client_hash($k1_user_id = " ") {
    if (isset($_SESSION['k1_app_session']['user_id'])) {
        $k1_user_id = $_SESSION['k1_app_session']['user_id'];
    }
    return md5($k1_user_id . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . MAGIC_VALUE);
}

/**
 * Check the current user level VS a comma separated list as 1,2,3,4,5
 * @param string $levels_to_check
 * @return boolean
 */
function check_user_level($levels_to_check) {
    if (!isset($_SESSION['k1_app_session']['user_level'])) {
        die("No session to check");
    }
//    if (empty($levels_to_check) || (!is_string($levels_to_check) && !is_numeric($levels_to_check))) {
    // EMPTY fails with '0'
    if (!is_string($levels_to_check) && !is_numeric($levels_to_check)) {
        die("level_to_check have to be a string or numeric");
    }
    $levels = explode(",", $levels_to_check);
    $has_access = false;
    foreach ($levels as $level) {
        if ($_SESSION['k1_app_session']['user_level'] == $level) {
            $has_access = true;
        }
    }
    return $has_access;
}

function check_self_id($self_id, $compare_id, $self_level) {
    if (empty($self_id)) {
        die(__FUNCTION__ . ': $selft_id cant be empty');
    }
    if (empty($compare_id)) {
        die(__FUNCTION__ . ': $compare_id cant be empty');
    }
    if (empty($self_level) || (!is_string($self_level) && !is_numeric($self_level))) {
        die(__FUNCTION__ . ': $self_level cant be empty and must to be string');
    }
    if (\k1lib\session\check_user_level($self_level)) {
        $self_user = true;
    } else {
        $self_user = false;
    }
    if ($self_user) {
        if ($self_id === $self_level) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}
