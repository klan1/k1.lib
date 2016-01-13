<?php

//
//  $pics = $facebook->api(array("method"=>"users.getInfo","uids"=>$fbID,"fields"=>array("pic","pic_square")));
//

function fb_check_permissions($permissions, $url_to_login, $redirect = TRUE) {
    global $fb_uid;

    if (!is_string($permissions)) {
        \k1lib\common\show_error("var \$permissions has to be a string", __FUNCTION__, $exit);
    }
    if (!is_string($url_to_login)) {
        \k1lib\common\show_error("var \$url_to_login has to be a string", __FUNCTION__, $exit);
    }

    $fb_user_permissions = fbq("SELECT {$permissions}  FROM permissions WHERE uid = {$fb_uid}", FALSE);

    foreach ($fb_user_permissions as $permission => $value) {
        if ($value == "1") {
            continue;
        } else {
            if ($redirect) {
                \k1lib\html\js_go($url_to_login);
                return FALSE;
            }
        }
    }
    return $fb_user_permissions;
}

function is_fb_developer($fb_uid = NULL) {
    global $facebook, $db;
    if ($fb_uid == NULL) {
        $fb_uid = $GLOBALS['fb_uid'];
    }
    $isDev = fbq("SELECT developer_id FROM developer WHERE developer_id = '{$fb_uid}' AND application_id = '" . FB_APP_ID . "'");
    if (isset($isDev['developer_id']) && $isDev['developer_id'] == $fb_uid) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function fbq($fbql, $use_cache = TRUE) {
    if (USE_MEMCACHE !== TRUE) {
        $use_cache = FALSE;
    }
    global $fb_connected, $facebook;
    if ($fb_connected) {
        try {
            if (FB_PROFILE) {
                global $fbq_profiles, $fbq_calls;
                $fbq_calls++;
                $fbq_start_time = microtime(TRUE);
            }
            $parameters = array("method" => "fql.query", "query" => $fbql);
            if ($use_cache) {
                $result = $facebook->api_cached($parameters);
            } else {
                $result = $facebook->api($parameters);
            }
            if (FB_PROFILE) {
                $fbq_end_time = microtime(TRUE);
                $fbq_profiles[$fbq_calls]['fbq'] = $fbql;
                $fbq_profiles[$fbq_calls]['time'] = $fbq_end_time - $fbq_start_time;
            }

            if (isset($result[0])) {
                return $result[0];
            } else {
                return FALSE;
            }
        } catch (FacebookApiException $e) {
            \k1lib\common\show_error($e . " SQL: {$fbql}", __FUNCTION__);
            return FALSE;
        }
    } else {
        \k1lib\common\show_error("The FBQ needs to be connected to Facebook", __FUNCTION__, TRUE);
    }
}

function do_facebook_connect($fb_login_config, $js_redirect= TRUE) {
    global $facebook;

    $loginUrl = $facebook->getLoginUrl($fb_login_config);
    if ($js_redirect) {
        \k1lib\html\js_go($loginUrl, "top", FALSE);
        return TRUE;
    } else {
        return $loginUrl;
    }
}

function do_facebook_init() {
    /*
     * TODO: Check is the user data has changed to avoid the SQL INSERT every time the user loads a page
     */
    global $facebook, $fb_connected, $fb_session, $fb_uid, $fb_user;

    $fb_session = NULL;
    $fb_uid = NULL;
    $fb_user = NULL;

    $fb_uid = $facebook->getUser();

    // Session based API call.
    if ($fb_uid) {
        try {
            $fb_user = $facebook->api("/$fb_uid");
        } catch (FacebookApiException $e) {
            \k1lib\common\show_error($e, __FUNCTION__);
        }
    } else {
        session_destroy();
        \k1lib\common\show_message("No te has logeado en la aplicacion, redireccionando...", __FUNCTION__);
    }
    // login or logout url will be needed depending on current user state.
    if ($fb_user) {
        $fb_connected = TRUE;
    } else {
//        \k1lib\html\js_go(\k1lib\urlrewrite\get_fb_app_link("/fb-connect/basic?return=/{$_GET['url']}"), "top", FALSE);
        \k1lib\html\js_go(\k1lib\urlrewrite\get_fb_app_link("/fb-connect/basic?return=" . \k1lib\urlrewrite\url_manager::make_url_from_rewrite("this")), "top", FALSE);
    }

    /*
     * USER STORE
     */
    if (STORE_USERS && (\k1app\APP_MODE != 'ajax')) {
        if (check_fbid($fb_uid)) {
            if ($_SESSION['fb_user_updated'] != TRUE) {
                $_SESSION['fb_user_updated'] = TRUE;
                update_user($fb_user);
            }
        } else {
            insert_user($fb_user);
        }
    }
}

function load_friends($fb_uid = NULL) {

    global $facebook, $friends;

    if ($fb_uid == NULL) {
        $fb_uid = $GLOBALS['fb_uid'];
    }

    try {
        $friends = $facebook->api_cached("/$fb_uid/friends");
        return $friends;
    } catch (FacebookApiException $e) {
        \k1lib\common\show_error($e, __FUNCTION__);
        return FALSE;
    }
}

function load_wall($fb_uid = NULL) {

    global $facebook;

    if ($fb_uid == NULL) {
        $fb_uid = $GLOBALS['fb_uid'];
    }

    try {
        $feeds = $facebook->api("/$fb_uid/feed");
        return $feeds;
    } catch (FacebookApiException $e) {
        \k1lib\common\show_error($e, __FUNCTION__);
        return FALSE;
    }
}

function load_statuses($fb_uid = NULL, $limit = 1) {

    global $facebook, $friends;

    if ($fb_uid == NULL) {
        $fb_uid = $GLOBALS['fb_uid'];
    }

    try {
        $statuses = $facebook->api("/$fb_uid/statuses?limit={$limit}");
        return $statuses;
    } catch (FacebookApiException $e) {
        \k1lib\common\show_error($e, __FUNCTION__);
        return FALSE;
    }
}

function load_object($object_id = NULL) {

    global $facebook, $friends;

    if ($fb_uid == NULL) {
        $fb_uid = $GLOBALS['fb_uid'];
    }

    try {
        $object = $facebook->api_cached("/$object_id");
        return $object;
    } catch (FacebookApiException $e) {
        \k1lib\common\show_error($e, __FUNCTION__);
        return FALSE;
    }
}

function do_post($fb_uid, $message, $extra = NULL) {

    /* $extra example

      $post_vars = array(
      'picture'=> "http://www.royal-films.com/App/fotos/70725.jpg",
      'link'=> "http://lomasbacano.klan1.com/articulo/4635/saw-3d-pelicula.xhtml",
      'name'=> "post name",
      'caption'=> "Aqui va el aption, es largoooo",
      'description'=> "Y aqui una descripcion, mas larga aun segun probe...",
      //'actions'=> "caption here",
      'privacy'=> '{"value": "ALL_FRIENDS"}', );
     *
     * RETURNS: array(1) { ["id"]=> string(25) "611852525_174283085931236" } bool(TRUE)
     */
    if (FB_EMU_SEND) {
        return rand(0, 1);
    } else {
        $extra['message'] = $message;

        global $facebook;
        if (is_array($extra) && (count($extra) <= 9)) {
            try {
                $statusUpdate = $facebook->api("/{$fb_uid}/feed", 'post', $extra);
                if (isset($statusUpdate['id'])) {
                    return $statusUpdate['id'];
                } else {
                    return $statusUpdate;
                }
                //$statusUpdate = $facebook->api(array("method" => "stream.publish", "uid" => $fb_uid, "message" => $message));
                //$statusUpdate = $facebook->api(array("method"=>"status.set","uid"=>$fb_uid,"status"=> $message . " via old rest api set to uid: $fb_uid"));
            } catch (FacebookApiException $e) {
                return FALSE;
            }
        } else {
            \k1lib\common\show_error("Values array is not valid or has too much values", __FUNCTION__);
            return FALSE;
        }
    }
}

function do_set_status($fb_uid, $message) {

    if (FB_EMU_SEND) {
        return rand(0, 1);
    } else {
        global $facebook;
        try {
            $statusUpdate = $facebook->api(array("method" => "status.set", "uid" => $fb_uid, "status" => $message));
            return $statusUpdate;
        } catch (FacebookApiException $e) {
            \k1lib\common\show_error($e, __FUNCTION__);
            return FALSE;
        }
    }
}

function get_last_status($fb_uid) {
    global $facebook;
    $result = fbq("SELECT status_id FROM status WHERE uid = {$fb_uid} limit 1");
    if ($result['status_id']) {
        return $result['status_id'];
    } else {
        return FALSE;
    }
}

function fb_comment_status($fb_uid, $message) {

    if (FB_EMU_SEND) {
        return rand(0, 1);
    } else {
        global $facebook;
        $last_status_id = get_last_status($fb_uid);
        if ($last_status_id) {
            try {
                $status_id_to_post = $fb_uid . '_' . $last_status_id;
                $statusUpdate = $facebook->api("/{$status_id_to_post}/comments", 'post', $extra);
                if ($statusUpdate !== FALSE) {
                    if (isset($statusUpdate['id'])) {
                        return $statusUpdate['id'];
                    } else {
                        return $statusUpdate;
                    }
                } else {
                    return FALSE;
                }
            } catch (FacebookApiException $e) {
                \k1lib\common\show_error($e, __FUNCTION__);
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
}

function do_comment_status($fb_uid, $message) {

    if (FB_EMU_SEND) {
        return rand(0, 1);
    } else {
        global $facebook;
        $last_status_id = get_last_status($fb_uid);
        if ($last_status_id) {
            try {
                $status_id_to_post = $fb_uid . '_' . $last_status_id;
                $statusUpdate = $facebook->api(array("method" => "stream.addComment", "post_id" => $status_id_to_post, "comment" => $message));
                if ($statusUpdate != "") {
                    return $statusUpdate;
                } else {
                    return FALSE;
                }
            } catch (FacebookApiException $e) {
                \k1lib\common\show_error($e, __FUNCTION__);
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
}

function check_fbid($fb_uid) {
    global $db;
    if ($fb_uid != '') {
        if (\k1lib\sql\sql_check_id($db, "usuarios", "uid", $fb_uid, TRUE)) {
            return TRUE;
        } else {
            return FALSE;
        }
    } else {
        \k1lib\common\show_error("UID has no value to check ", __FUNCTION__);
        exit;
    }
}

function fb_back_button($text = "Regresar") {
    return '<a href="javascript:history.back()" class="uiButton">' . $text . '</a>';
}

