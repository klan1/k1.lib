<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of k1-facebook-cached
 *
 * @author J0hnD03
 */
if (class_exists("Facebook")) {

    class K1FacebookCached extends Facebook {

        public function api_cached() {
            global $skip_memcache;
            if (FB_PROFILE) {
                global $fbapi_profiles, $fb_api_calls;
                $fb_api_calls++;
                $fb_api_start_time = microtime(TRUE);
            }
            // Begin FB SDK Code
            $args = func_get_args();
            // End FB SDK Code
            if (defined("USE_MEMCACHE") && USE_MEMCACHE && !$skip_memcache) {
                global $memcache;
                //$memcache = new Memcache();
                $api_call_md5 = md5(print_r($args, TRUE));
                $result = $memcache->get($api_call_md5);
                if (FB_PROFILE && $result) {
                    $fbapi_profiles[$fb_api_calls]['cached'] = "yes";
                } else {
                    $fbapi_profiles[$fb_api_calls]['cached'] = "no";
                }
            } else {
                $no_memcache = TRUE;
                $result = NULL;
                $fbapi_profiles[$fb_api_calls]['cached'] = "no used";
                $fbapi_profiles[$fb_api_calls]['skiped'] = ($skip_memcache) ? 'yes' : 'no';
            }

            if ($result == FALSE) {
                // Begin FB SDK Code
                if (is_array($args[0])) {
                    $result = $this->_restserver($args[0]);
                } else {
                    $result = call_user_func_array(array($this, '_graph'), $args);
                }
                if ($no_memcache !== TRUE) {
                    $memcache->set($api_call_md5, $result, FALSE, 120);
                }
                // End FB SDK Code
            }
            if (FB_PROFILE) {
                $fb_api_end_time = microtime(TRUE);
                $fbapi_profiles[$fb_api_calls]['api'] = print_r($args, TRUE);
                $fbapi_profiles[$fb_api_calls]['time'] = $fb_api_end_time - $fb_api_start_time;
            }
            return $result;
        }

    }

}