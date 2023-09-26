<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * K1 Lib loader
 *
 * PHP version 7.1
 *
 * LICENSE:  
 *
 * @author          Alejandro Trujillo J. <alejo@klan1.com>
 * @copyright       2015-2019 Klan1 Network SAS
 * @license         Apache 2.0
 * @version         1.5.0-ALPHA
 * @since           File available since Release 0.1
 */
/*
 * App run time vars
 */

namespace k1lib;

$called = 0;
create_dist_file('./src');

function create_dist_file($path_to_explore, &$called = 0) {
    $called++;
    $files_list = scandir($path_to_explore);
    $output_file_path = __DIR__ . '/dist/inc.all.php';

    if ($called === 1) {
        date_default_timezone_set('America/Bogota');
        $date = date('Y-m-d H:i:s');

        $output_file_content = <<<CODE
<?php

/** DIST ALL IN ONE CODE **/
                
// creation date: {$date}

CODE;

        file_put_contents($output_file_path, $output_file_content);
    }

    foreach ($files_list as $file) {
        if ((substr($file, 0, 1) == '.') || (substr($file, 0, 2) == '__') || ($file == 'index.php') || ($file == 'init.php')) {
            echo "Skiping: $file\n";
            continue;
        }
        $file_path = $path_to_explore . "/" . $file;
//        echo " [{$file_path}] \n";

        if (is_file($file_path) && (substr($file_path, -4) == ".php")) {
            echo "Reading: $file_path\n";
            $file_content = file_get_contents($file_path);
            $output_file_content = "\n// {$file_path}\n";
            $output_file_content .= str_replace('<?php', '', $file_content);
            echo "Writing: $file_path\n";
            file_put_contents($output_file_path, $output_file_content, FILE_APPEND);
//            require_once $file_path;
        } elseif (is_dir($file_path)) {
            /**
             * GOD BLESS function recursion !!
             */
            echo "Subdirectory: $file_path \n";
            create_dist_file($file_path, $called);
        }
        $output_file_content = '';
    }
}
