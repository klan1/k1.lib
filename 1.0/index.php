<?php
error_reporting(E_ALL);
$start = microtime(true);
include 'k1lib-init.php';
$total_load_time = microtime(TRUE) - $start;
?>
<html>
    <head>
        <title>K1.lib <?php echo \k1lib\VERSION ?></title>
    </head>
    <body>
        <h3>Everything seems to be OK is there are no PHP messages.</h3>
        <p>Load time: <?php echo $total_load_time ?></p>
        <p><strong>Klan1</strong> Development Library Version <?php echo \k1lib\VERSION ?></p>
        <h4>DEBUG DATA</h4>
        <?php d($GLOBALS); ?>
    </body>
</html>