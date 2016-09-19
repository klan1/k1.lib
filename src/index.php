<?php
$mem_usage['init'] = memory_get_usage() / 1024;
$start = microtime(TRUE);

include_once 'init.php';

// OVER RIDE THE DEFAULT k1LIB error reporting setting
error_reporting(E_ALL);
$mem_usage['peak'] = memory_get_peak_usage() / 1024;
$mem_usage['loaded'] = memory_get_usage() / 1024;
$mem_usage['k1lib'] = $mem_usage['loaded'] - $mem_usage['init'];

$total_load_time = microtime(TRUE) - $start;
unset($start);
?>
<html>
    <head>
        <title>K1.lib <?php echo \k1lib\VERSION ?></title>
    </head>
    <body>
        <h3><strong>Klan1</strong> Development Library Version <?php echo \k1lib\VERSION ?></h3>
        <p>Everything seems to be OK is there are no PHP messages.</p>
        <p>Load time: <?php echo $total_load_time ?></p>
        <p>Memory usage: <?php d($mem_usage, FALSE, FALSE) ?></p>
        <h4>CLASSES DECLARED</h4>
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
        <h4>FUNCTIONS DEFINED</h4>
        <?php
        $k1_functions = get_defined_functions();
        d($k1_functions['user'], FALSE, FALSE);
        unset($k1_functions);
        ?>
        <h4>$_GLOBALS DATA</h4>
        <?php
        unset($mem_usage);
        unset($total_load_time);
        d($GLOBALS, FALSE, FALSE);
        ?>
    </body>
</html>