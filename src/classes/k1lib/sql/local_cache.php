<?php

/**
 * @license Apache-2.0
 * @package k1lib
 * @subpackage sql
 * Local and Memcached SQL query caching functionality for performance optimization.
 */

namespace k1lib\sql;

/**
 * SQL local cache manager.
 * Provides caching via Memcached or local array storage.
 *
 * @package k1lib\sql
 */
class local_cache {

    use common;

    /**
     * Flag to use Memcached instead of local cache.
     * @var bool
     */
    static protected bool $use_memcached = false;

    /**
     * Memcached connection object.
     * @var \Memcached
     */
    static object $memcached;

    /**
     * Memcached server hostname.
     * @var string
     */
    static protected string $memcached_server = '127.0.0.1';

    /**
     * Memcached server port.
     * @var int
     */
    static protected int $memcached_port = 11211;

    /**
     * Time-to-live for cached items in seconds.
     * @var int
     */
    static protected int $memcached_ttl = 300;

    /**
     * SQL terms to exclude from caching.
     * @var array
     */
    static protected array $exclude_sql_terms = ['INFORMATION_SCHEMA', 'SHOW FULL COLUMNS', 'UPDATE', 'INSERT', 'DELETE'];

    /**
     * Connects to Memcached server.
     */
    static protected function connect_memcached() {
        self::$memcached = new \Memcached();
        self::$memcached->addServer(self::$memcached_server, self::$memcached_port);
    }

    /**
     * Stores a SQL query result in the cache.
     *
     * @param string $sql_query The SQL query
     * @param mixed $sql_result The result to cache
     * @return bool TRUE on success, FALSE otherwise
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
     * Checks if a SQL query result is cached.
     *
     * @param string $sql_query The SQL query to check
     * @return bool TRUE if cached, FALSE otherwise
     */
    static public function is_cached($sql_query) {
        if (self::is_enabled()) {
            return key_exists(self::$data, md5($sql_query));
        } else {
            return false;
        }
    }

    /**
     * Retrieves a cached SQL query result.
     *
     * @param string $sql_query The SQL query
     * @return array|false The cached result or FALSE if not found
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

    /**
     * Checks if a SQL query contains excluded terms.
     *
     * @param string $sql_query The SQL query to check
     * @return bool TRUE if contains excluded terms, FALSE otherwise
     */
    static protected function check_exlusion($sql_query) {
        foreach (self::$exclude_sql_terms as $term_to_exclude) {
            if (strstr(strtolower($sql_query), strtolower($term_to_exclude)) !== FALSE) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets the SQL terms excluded from caching.
     *
     * @return string Array of excluded terms
     */
    static public function get_exclude_sql_terms(): string {
        return self::$exclude_sql_terms;
    }

    /**
     * Sets the SQL terms excluded from caching.
     *
     * @param string $exclude_sql_terms Array of terms to exclude
     */
    static public function set_exclude_sql_terms(string $exclude_sql_terms): void {
        self::$exclude_sql_terms = $exclude_sql_terms;
    }

    /**
     * Enables Memcached mode for caching.
     */
    static public function use_memcached() {
        self::$use_memcached = true;
        self::connect_memcached();
    }

    /**
     * Enables local array caching mode.
     */
    static public function use_localcache() {
        self::$use_memcached = false;
    }

    /**
     * Sets the cache mode.
     *
     * @param string $mode The mode to set
     */
    static public function set_mode($mode): void {
        self::$mode = $mode;
    }

    /**
     * Gets the Memcached server hostname.
     *
     * @return string The server address
     */
    static public function get_memcached_server() {
        return self::$memcached_server;
    }

    /**
     * Gets the Memcached server port.
     *
     * @return int The port number
     */
    static public function get_memcached_port() {
        return self::$memcached_port;
    }

    /**
     * Gets the Memcached TTL setting.
     *
     * @return int The TTL in seconds
     */
    static public function get_memcached_ttl() {
        return self::$memcached_ttl;
    }

    /**
     * Sets the Memcached server hostname.
     *
     * @param string $memcached_server The server address
     */
    static public function set_memcached_server($memcached_server): void {
        self::$memcached_server = $memcached_server;
    }

    /**
     * Sets the Memcached server port.
     *
     * @param int $memcached_port The port number
     */
    static public function set_memcached_port($memcached_port): void {
        self::$memcached_port = $memcached_port;
    }

    /**
     * Sets the Memcached TTL.
     *
     * @param int $memcached_ttl The TTL in seconds
     */
    static public function set_memcached_ttl($memcached_ttl): void {
        self::$memcached_ttl = $memcached_ttl;
    }
}
