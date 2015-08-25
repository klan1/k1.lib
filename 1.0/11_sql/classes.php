<?php

namespace k1lib\sql;

class common_code {

    /**
     * Enable state
     * @var Boolean 
     */
    static protected $enabled = FALSE;

    /**
     *
     * @var Int 
     */
    static protected $data_count = 0;

    /**
     * Stores the SQL data
     * @var Array
     */
    static protected $data = array();

    /**
     * Enable the engenie
     */
    static public function enable() {
        self::$enabled = TRUE;
    }

    /**
     * Query the enabled state
     * @return Boolean
     */
    static public function is_enabled($show_error = false) {
        if ($show_error && (!self::$enabled)) {
            trigger_error("SQL Profile system is not enabled yet", E_USER_ERROR);
        }
        return self::$enabled;
    }

    static public function get_data() {
        self::is_enabled(true);
        return self::$data;
    }

}

class profiler extends common_code {
//    use common_code;

    /**
     * Begin a SQL Profile with a SQL query code 
     * @param String $sql_query
     * @return Int Profile ID
     */
    static public function add($sql_query) {
        self::is_enabled(true);
        $sql_md5 = md5($sql_query);
        self::$data_count++;
        self::$data[self::$data_count]['md5'] = $sql_md5;
        self::$data[self::$data_count]['sql'] = $sql_query;
        return self::$data_count;
    }

    /**
     * Begin the time count
     * @param Int $profile_id Profile ID
     */
    static public function start_time_count($profile_id) {
        self::is_enabled(true);
        self::$data[$profile_id]['start_time'] = microtime(TRUE);
    }

    /**
     * Stop the time count
     * @param Int $profile_id Profile ID
     */
    static public function stop_time_count($profile_id) {
        self::is_enabled(true);
        self::$data[$profile_id]['stop_time'] = microtime(TRUE);
        self::$data[$profile_id]['total_time'] = self::$data[self::$data_count]['stop_time'] - self::$data[self::$data_count]['start_time'];
    }

    /**
     * Keep record of cache use of the current query
     * @param Int $profile_id Profile ID
     * @param Boolean $is_cached 
     */
    static public function set_is_cached($profile_id, $is_cached) {
        self::is_enabled(true);
        if (self::is_enabled()) {
            self::$data[$profile_id]['cache'] = $is_cached;
        }
    }

    /**
     * Filter the data by MD5
     * @param String $md5
     * @return Array
     */
    static public function get_by_md5($md5) {
        self::is_enabled(true);
        $data_filtered = array();
        foreach (self::$data as $id => $profile_data) {
            if (isset($profile_data['md5']) && ($profile_data['md5'] == $md5)) {
                $data_filtered[] = $profile_data;
            }
        }
        return $data_filtered;
    }

}

class local_cache extends common_code {
//    use common_code;

    /**
     * Put a SQL_RESULT on the LOCAL CACHE
     * @param type $sql_query
     * @param type $sql_result
     */
    static public function add($sql_query, $sql_result) {
        self::is_enabled(true);
        $sql_md5 = md5($sql_query);
        self::$data_count++;
        self::$data[$sql_md5] = $sql_result;
    }

    /**
     * Return if the SQL QUERY is on cache or not
     * @param String $sql_query
     * @return Boolean
     */
    static public function is_cached($sql_query) {
        self::is_enabled(true);
        return isset(self::$data[md5($sql_query)]);
    }

    /**
     * Returns a previusly STORED SQL RESULT by SQL QUERY if exist
     * @param String $sql_query
     * @return Array returns FALSE if not exist
     */
    static public function get_result($sql_query) {
        self::is_enabled(true);
        if (isset(self::$data[md5($sql_query)])) {
            return (self::$data[md5($sql_query)]);
        } else {
            return FALSE;
        }
    }

}
