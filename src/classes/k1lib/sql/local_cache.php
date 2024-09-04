<?php

namespace k1lib\sql;

class local_cache extends common {

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
