<?php

/**
 * Initalize the PHP output buffer system and setup the end flush fuction call, this should be call at the beginnig of the app
 */
function k1_start_app() {
    if (APP_MODE == "web") {
        ob_start("k1_flush_output");
    }
}

/**
 * End flush buffer function, this one will call the function for pase the template places
 * @param string $buffer
 * @return string 
 */
function k1_flush_output($buffer) {
    return k1_parse_template_places($buffer);
}

/**
 * The last function on the app must to be this one, will flush the output buffer and
 * @global type $app_init_time
 * @param type $show_stats 
 */
function k1_end_app($show_stats = false) {
    global $app_init_time;
    $app_run_time = round((microtime(true) - $app_init_time), 5);
    if (APP_MODE == "web") {
        if ($show_stats) {
            k1_set_place_value("footer", "Runtime: {$app_run_time} Seg - K1.lib V" . K1LIB_VER);
        }

        ob_end_flush();
    }
}
