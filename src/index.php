<?php

namespace k1lib;

$mem_usage['init'] = memory_get_usage() / 1024;
$start = microtime(TRUE);

include_once 'init.php';

// OVER RIDE THE DEFAULT k1LIB error reporting setting
error_reporting(E_ALL);

/**
 * Includes ALL files on a directory.
 */
init_k1lib_include_files(K1LIB_BASE_PATH . "/lang/en");
init_k1lib_include_files(K1LIB_BASE_PATH . '/functions');
// init_k1lib_include_files(K1LIB_BASE_PATH . '/classes');

/**
 * Use this function to inlude ONLY CLASSES and functions, if there are normal 
 * variables they will be on the function scope and you NEVER will reach them.
 * @param string $path_to_explore
 * @param array $prefix_to_exclude
 */
function init_k1lib_include_files($path_to_explore, array $prefix_to_exclude = ['.', '..', '__']) {
    $files_list = scandir($path_to_explore);

    foreach ($files_list as $file) {
        if ((substr($file, 0, 1) == '.') || (substr($file, 0, 2) == '__') || ($file == 'index.php')) {
            continue;
        }
        $file_path = $path_to_explore . "/" . $file;

        if (is_file($file_path) && (substr($file_path, -4) == ".php")) {
            require_once $file_path;
        } elseif (is_dir($file_path)) {
            /**
             * GOD BLESS function recursion !!
             */
            init_k1lib_include_files($file_path, $prefix_to_exclude);
        }
    }
}

$total_load_time = microtime(TRUE) - $start;
unset($start);

$mem_usage['peak'] = memory_get_peak_usage() / 1024;
$mem_usage['loaded'] = memory_get_usage() / 1024;
$mem_usage['k1lib'] = $mem_usage['loaded'] - $mem_usage['init'];
?>
<html>

    <head>
        <title>K1.lib <?php echo \k1lib\VERSION ?></title>
    </head>

    <body>
        <h3><strong>Klan1</strong> Development Library Version <?php echo \k1lib\VERSION ?></h3>
        <p>Everything seems to be OK is there are no PHP messages.</p>
        <p>Current PHP version:<?php echo phpversion(); ?></p>
        <p>Load time: <?php echo round($total_load_time, 7) ?></p>
        <p>Memory usage: <?php d($mem_usage, FALSE, FALSE) ?></p>
        <h4>CLASSES DECLARED</h4>
        <pre>
            <?php
            $k1_classes = array();
            foreach (get_declared_classes() as $class) {
                if (strstr($class, "k1") !== false) {
                    $k1_classes[] = $class;
                }
            }
            d($k1_classes, FALSE, FALSE);
            unset($class);
            unset($k1_classes);
            ?>
        </pre>
        <h4>FUNCTIONS DEFINED</h4>
        <pre>
            <?php
            $k1_functions = get_defined_functions();
            d($k1_functions['user'], FALSE, FALSE);
            unset($k1_functions);
            ?>
        </pre>
        <h4>$_GLOBALS DATA</h4>
        <pre>
            <?php
            unset($mem_usage);
            unset($total_load_time);
            d($GLOBALS, FALSE, FALSE);
            ?>
        </pre>
    </body>

</html>