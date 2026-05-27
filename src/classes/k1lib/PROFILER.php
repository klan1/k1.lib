<?php

/**
 * k1.lib Performance Profiler
 *
 * Simple execution time profiling utility for measuring script performance.
 *
 * @license Apache-2.0
 * @package k1lib
 */

namespace k1lib;

/**
 * Performance profiler for measuring script execution time.
 *
 * Provides start/end timing functionality to measure and retrieve
 * script execution duration in seconds.
 */
class PROFILER {

    /** @var float Microtime timestamp when profiling started */
    static private $init = 0;

    /** @var float Microtime timestamp when profiling ended */
    static private $finish = 0;

    /** @var float Total execution time in seconds */
    static private $run_time = 0;

    /**
     * Start the profiler timer.
     *
     * @return void
     */
    static function start() {
        self::$init = microtime(TRUE);
    }

    /**
     * End the profiler timer and calculate execution time.
     *
     * @return float The total execution time in seconds (5 decimal precision)
     */
    static function end() {
        self::$finish = microtime(TRUE);
        self::$run_time = round((microtime(TRUE) - self::$init), 5);
        return self::$run_time;
    }
}
