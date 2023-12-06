<?php

/**
 * Forms related functions, K1.lib.
 * 
 * Common needed actions on forms and special ideas implemented with this lib.
 * @author J0hnD03 <alejandro.trujillo@klan1.com>
 * @version 1.0
 * @package forms
 */

namespace k1lib\forms;

class file_uploads {

    const ERROR_FILE_ALREADY_EXIST = -1;
    const ERROR_FILE_NOT_MOVED_TO_UPLOAD_PATH = -2;

    /**
     * Enable state
     * @var Boolean 
     */
    static private $enabled = FALSE;
    static private $overwrite_existent = TRUE;

    /**
     * Uploads path to store files 
     * @var char
     */
    static private $path_to_uploads;

    /**
     * Uploads URL to where the files are
     * @var char
     */
    static private $uploads_url;

    /**
     *
     * @var string
     */
    static private $last_error = NULL;

    /**
     * Enable the engenie
     */
    static public function enable($path_to_upload, $uploads_url) {
        self::$enabled = TRUE;
        if (file_exists($path_to_upload)) {
            self::$path_to_uploads = $path_to_upload;
            self::$uploads_url = $uploads_url;
        } else {
            trigger_error("The upload path [{$path_to_upload}] do not exists. Uploads will fail!", E_USER_WARNING);
        }
    }

    /**
     * Query the enabled state
     * @return Boolean
     */
    static public function is_enabled($show_error = false) {
        if ($show_error && (!self::$enabled)) {
            trigger_error("Files uploads are not enabled yet!", E_USER_ERROR);
        }
        return self::$enabled;
    }

    static function place_upload_file($tmp_file, $file_name, $directory = NULL) {
//        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!empty($directory)) {
            if (!file_exists(self::$path_to_uploads . $directory)) {
                mkdir(self::$path_to_uploads . $directory);
            }
            $file_name_to_save = self::$path_to_uploads . $directory . '/' . $file_name;
        } else {
            $file_name_to_save = self::$path_to_uploads . $file_name;
        }
//        $file_name_to_save = self::$path_to_uploads . md5($file_name) . ".{$file_extension}";

        if (file_exists($file_name_to_save) && !self::$overwrite_existent) {
            self::$last_error = "File name aready exist and won't be overwriten";
            trigger_error(self::$last_error, E_USER_NOTICE);
            return FALSE;
        } else {
            if (is_uploaded_file($tmp_file)) {
                if (move_uploaded_file($tmp_file, $file_name_to_save) === TRUE) {
                    return $file_name_to_save;
                } else {
                    self::$last_error = "File couldn't be moved to the uplaod directory, check file permissions.";
                    trigger_error(self::$last_error, E_USER_NOTICE);
                    return FALSE;
                }
            } else {
                trigger_error("Possible hack attemp", E_USER_NOTICE);
            }
        }
    }

    static function get_uploaded_file_path($file_name, $directory = NULL) {
        if (!empty($directory)) {
//        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $file_name_to_get_directory = self::$path_to_uploads . $directory . '/' . $file_name;
        } else {
            $file_name_to_get_directory = NULL;
        }
        $file_name_to_get = self::$path_to_uploads . $file_name;
//        $file_name_to_get = self::$path_to_uploads . md5($file_name) . ".{$file_extension}";
        if (file_exists($file_name_to_get)) {
            return $file_name_to_get;
        } elseif (!empty($file_name_to_get_directory) || file_exists($file_name_to_get_directory)) {
            return $file_name_to_get_directory;
        } else {
            return FALSE;
        }
    }

    static function get_uploaded_file_url($file_name, $directory = NULL) {
//        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (self::get_uploaded_file_path($file_name, $directory)) {
            $file_name_to_get = self::$uploads_url . $directory . '/' . $file_name;
//        $file_name_to_get = self::$uploads_url . md5($file_name) . ".{$file_extension}";
        } else if (self::get_uploaded_file_path($file_name)) {
            $file_name_to_get = self::$uploads_url . $file_name;
//        $file_name_to_get = self::$uploads_url . md5($file_name) . ".{$file_extension}";
        } else {
            return FALSE;
        }
        return $file_name_to_get;
    }

    static function unlink_uploaded_file($file_name, $directory = NULL) {
//        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $file_name_to_get = self::$path_to_uploads . $file_name;
        $file_name_to_get_with_directory = self::$path_to_uploads . $directory . '/' . $file_name;
//        $file_name_to_get = self::$path_to_uploads . md5($file_name) . ".{$file_extension}";
        if (file_exists($file_name_to_get)) {
            return unlink($file_name_to_get);
        } elseif (file_exists($file_name_to_get_with_directory)) {
            return unlink($file_name_to_get_with_directory);
        } else {
            return FALSE;
        }
    }

    static function get_uploads_url($directory = NULL) {
        if (!empty($directory)) {
            return self::$uploads_url . $directory . '/';
        } else {
            return self::$uploads_url;
        }
    }

    static function get_overwrite_existent() {
        return self::$overwrite_existent;
    }

    static function set_overwrite_existent($overwrite_existent) {
        self::$overwrite_existent = $overwrite_existent;
    }

    static function get_last_error() {
        return self::$last_error;
    }

}
