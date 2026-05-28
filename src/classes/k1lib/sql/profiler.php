<?php

/**
 * @license Apache-2.0
 * @package k1lib
 * @subpackage sql
 * SQL query profiling and performance monitoring functionality.
 */

namespace k1lib\sql;

/**
 * SQL query profiler.
 * Tracks SQL execution time, caching, and provides profiling data retrieval.
 *
 * @package k1lib\sql
 */
class profiler {

    use common;

    /**
     * Adds a SQL query to the profiler and returns a profile ID.
     *
     * @param string $sql_query The SQL query to profile
     * @return int Profile ID for later time tracking
     */
    static public function add(string $sql_query): int {
        self::is_enabled(true);
        $sql_md5 = md5($sql_query);
        self::$data_count++;
        self::$data[self::$data_count]['md5'] = $sql_md5;
        self::$data[self::$data_count]['sql'] = $sql_query;
        return self::$data_count;
    }

    /**
     * Starts the execution time counter for a profile.
     *
     * @param int $profile_id The profile ID from add()
     */
    static public function start_time_count(int $profile_id): void {
        self::is_enabled(true);
        self::$data[$profile_id]['start_time'] = microtime(TRUE);
    }

    /**
     * Stop the time count
     * @param int $profile_id Profile ID
     */
    static public function stop_time_count(int $profile_id): void {
        self::is_enabled(true);
        self::$data[$profile_id]['stop_time'] = microtime(TRUE);
        self::$data[$profile_id]['total_time'] = self::$data[$profile_id]['stop_time'] - self::$data[$profile_id]['start_time'];
    }

    /**
     * Records whether a query result was served from cache.
     *
     * @param int $profile_id The profile ID
     * @param bool $is_cached TRUE if cached, FALSE otherwise
     */
    static public function set_is_cached(int $profile_id, bool $is_cached): void {
        self::is_enabled(true);
        if (self::is_enabled()) {
            self::$data[$profile_id]['cache'] = $is_cached;
        }
    }

    /**
     * Retrieves profile data filtered by MD5 hash.
     *
     * @param string $md5 The MD5 hash to filter by
     * @return array Array of profile data matching the MD5
     */
    static public function get_by_md5($md5): array {
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
     * Calculates and returns the total execution time of all profiled queries.
     *
     * @return float Total time in seconds
     */
    static public function get_total_time(): float {
        $total_time = 0;
        foreach (self::$data as $profile_data) {
            $total_time += $profile_data['total_time'];
        }
        return $total_time;
    }
}
