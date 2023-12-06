<?php

namespace k1lib;

class PROFILER {

    static private $init = 0;
    static private $finish = 0;
    static private $run_time = 0;

    static function start() {
        self::$init = microtime(TRUE);
    }

    static function end() {
        self::$finish = microtime(TRUE);
        self::$run_time = round((microtime(TRUE) - self::$init), 5);
        return self::$run_time;
    }
}