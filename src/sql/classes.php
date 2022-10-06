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
     * Disable the engenie
     */
    static public function disable() {
        self::$enabled = FALSE;
    }

    /**
     * Query the enabled state
     * @return Boolean
     */
    static public function is_enabled($show_error = false) {
        if ($show_error && (!self::$enabled)) {
            trigger_error("SQL Profile system is not enabled yet", E_USER_WARNING);
        }
        return self::$enabled;
    }

    static public function get_data() {
        self::is_enabled(true);
        return self::$data;
    }

    public static function get_data_count(): int {
        return self::$data_count;
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
     * @param Int $sql_md5 Profile ID
     */
    static public function start_time_count($sql_md5) {
        self::is_enabled(true);
        self::$data[$sql_md5]['start_time'] = microtime(TRUE);
    }

    /**
     * Stop the time count
     * @param Int $sql_md5 Profile ID
     */
    static public function stop_time_count($sql_md5) {
        self::is_enabled(true);
        self::$data[$sql_md5]['stop_time'] = microtime(TRUE);
        self::$data[$sql_md5]['total_time'] = self::$data[self::$data_count]['stop_time'] - self::$data[self::$data_count]['start_time'];
    }

    /**
     * Keep record of cache use of the current query
     * @param Int $sql_md5 Profile ID
     * @param Boolean $is_cached 
     */
    static public function set_is_cached($sql_md5, $is_cached) {
        self::is_enabled(true);
        if (self::is_enabled()) {
            self::$data[$sql_md5]['cache'] = $is_cached;
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

    /**
     * Return the total execution time
     * @return float
     */
    static public function get_total_time() {
        $total_time = 0;
        foreach (self::$data as $profile_data) {
            $total_time += $profile_data['total_time'];
        }
        return $total_time;
    }

}

class local_cache extends common_code {

//    use common_code;
    static protected bool $use_memcached = false;

    /**
     * 
     * @var \Memcached
     */
    static object $memcached;
    static protected string $memcached_server = '127.0.0.1';
    static protected int $memcached_port = 11211;
    static protected int $memcached_ttl = 300;
    static protected array $exclude_sql_terms = ['INFORMATION_SCHEMA', 'SHOW FULL COLUMNS', 'UPDATE', 'INSERT', 'DELETE'];

    static protected function connect_memcached() {
        self::$memcached = new \Memcached();
        self::$memcached->addServer(self::$memcached_server, self::$memcached_port);
    }

    /**
     * Put a SQL_RESULT on the LOCAL CACHE
     * @param type $sql_query
     * @param type $sql_result
     */
    static public function add($sql_query, $sql_result) {
        if (self::$use_memcached) {
            if (!self::check_exlusion($sql_query)) {
                return self::$memcached->set(md5($sql_query), $sql_result, self::$memcached_ttl);
            } else {
                return FALSE;
            }
        } else {
            self::is_enabled(true);
            self::$data[md5($sql_query)] = $sql_result;
        }
        self::$data_count++;
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
        if (self::$use_memcached) {
            if (!self::check_exlusion($sql_query)) {
                $return = self::$memcached->get(md5($sql_query));
                return $return;
            } else {
                return FALSE;
            }
        } else {
            if (isset(self::$data[md5($sql_query)])) {
                return (self::$data[md5($sql_query)]);
            } else {
                return FALSE;
            }
        }
    }

    static protected function check_exlusion($sql_query) {
        foreach (self::$exclude_sql_terms as $term_to_exclude) {
            if (strstr(strtolower($sql_query), strtolower($term_to_exclude)) !== FALSE) {
                return true;
            }
        }
        return false;
    }

    static public function get_exclude_sql_terms(): string {
        return self::$exclude_sql_terms;
    }

    static public function set_exclude_sql_terms(string $exclude_sql_terms): void {
        self::$exclude_sql_terms = $exclude_sql_terms;
    }

    static public function use_memcached() {
        self::$use_memcached = true;
        self::connect_memcached();
    }

    static public function use_localcache() {
        self::$use_memcached = false;
//        self::connect_memcached();
    }

    static public function set_mode($mode): void {
        self::$mode = $mode;
    }

    static public function get_memcached_server() {
        return self::$memcached_server;
    }

    static public function get_memcached_port() {
        return self::$memcached_port;
    }

    static public function get_memcached_ttl() {
        return self::$memcached_ttl;
    }

    static public function set_memcached_server($memcached_server): void {
        self::$memcached_server = $memcached_server;
    }

    static public function set_memcached_port($memcached_port): void {
        self::$memcached_port = $memcached_port;
    }

    static public function set_memcached_ttl($memcached_ttl): void {
        self::$memcached_ttl = $memcached_ttl;
    }

}
