<?php

namespace k1lib\sql;

class profiler extends common {

    /**
     * Begin a SQL Profile with a SQL query code 
     * @param string $sql_query
     * @return Int Profile ID
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
     * Begin the time count
     * @param int $profile_id Profile ID
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
     * Keep record of cache use of the current query
     * @param int $sql_md5 Profile ID
     * @param bool $is_cached 
     */
    static public function set_is_cached(int $profile_id, bool $is_cached): void {
        self::is_enabled(true);
        if (self::is_enabled()) {
            self::$data[$profile_id]['cache'] = $is_cached;
        }
    }

    /**
     * Filter the data by MD5
     * @param string $md5
     * @return Array
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
