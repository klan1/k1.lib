<?php

namespace k1lib\output_buffer;

/**
 * Initalize the PHP output buffer system and setup the end flush fuction call, this should be call at the beginnig of the app
 */
function start_app() {
    if (\k1app\APP_MODE == "web") {
        \ob_start('\k1lib\output_buffer\flush_output');
    }
}

/**
 * End flush buffer function, this one will call the function for pase the template places
 * @param string $buffer
 * @return string 
 */
function flush_output($buffer) {
    return \k1lib\templates\parse_template_places($buffer);
}

/**
 * The last function on the app must to be this one, will flush the output buffer and
 * @global type $app_init_time
 * @param type $show_stats 
 */
function end_app($show_stats = FALSE) {
    global $app_init_time;
    $app_run_time = round((microtime(TRUE) - $app_init_time), 5);
    if (\k1app\APP_MODE == "web") {
        if ($show_stats) {
            \k1lib\templates\set_place_value("footer", "Runtime: {$app_run_time} Seg - K1.lib V" . \k1lib\VERSION);
        }

        \ob_end_flush();
    }
}
