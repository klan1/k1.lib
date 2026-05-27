<?php

/**
 * File uploads handling for forms
 *
 * @license Apache-2.0
 * @package k1lib
 *
 * @author      J0hnD03 <alejandro.trujillo@klan1.com>
 * @version     1.0
 */

namespace k1lib\forms;

/**
 * File upload management class
 *
 * @package k1lib\forms
 */
class file_uploads {

    const ERROR_FILE_ALREADY_EXIST = -1;
    const ERROR_FILE_NOT_MOVED_TO_UPLOAD_PATH = -2;

    /**
     * @var bool Enable state
     */
    static private $enabled = FALSE;
    /** @var bool */
    static private $overwrite_existent = TRUE;

    /**
     * @var string Uploads path to store files
     */
    static private $path_to_uploads;

    /**
     * @var string Uploads URL to where the files are
     */
    static private $uploads_url;

    /**
     * @var string|null
     */
    static private $last_error = NULL;

    /**
     * Enable the file upload engine
     *
     * @param string $path_to_upload Path to upload directory
     * @param string $uploads_url URL to uploads directory
     * @return void
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
     *
     * @param bool $show_error Show error if not enabled
     * @return bool
     */
    static public function is_enabled($show_error = false) {
        if ($show_error && (!self::$enabled)) {
            trigger_error("Files uploads are not enabled yet!", E_USER_ERROR);
        }
        return self::$enabled;
    }

    /**
     * Place an uploaded file in the uploads directory
     *
     * @param string $tmp_file Temporary file path
     * @param string $file_name Target file name
     * @param string|null $directory Subdirectory
     * @return string|false
     */
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

    /**
     * Get the filesystem path of an uploaded file
     *
     * @param string $file_name
     * @param string|null $directory
     * @return string|false
     */
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

    /**
     * Get the URL of an uploaded file
     *
     * @param string $file_name
     * @param string|null $directory
     * @return string|false
     */
    static function get_uploaded_file_url($file_name, $directory = NULL) {
//        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        d('uploaded: ' . $file_name);
        if (self::get_uploaded_file_path($file_name, $directory)) {
            $file_name_to_get = self::$uploads_url . $directory . '/' . base64_encode($file_name);
//        $file_name_to_get = self::$uploads_url . md5($file_name) . ".{$file_extension}";
        } else if (self::get_uploaded_file_path($file_name)) {
            $file_name_to_get = self::$uploads_url . base64_encode($file_name);
//        $file_name_to_get = self::$uploads_url . md5($file_name) . ".{$file_extension}";
        } else {
            return FALSE;
        }
        return $file_name_to_get;
    }

    /**
     * Delete an uploaded file
     *
     * @param string $file_name
     * @param string|null $directory
     * @return bool
     */
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

    /**
     * @param string|null $directory
     * @return string
     */
    static function get_uploads_url($directory = NULL) {
        if (!empty($directory)) {
            return self::$uploads_url . $directory . '/';
        } else {
            return self::$uploads_url;
        }
    }

    /**
     * @return bool
     */
    static function get_overwrite_existent() {
        return self::$overwrite_existent;
    }

    /**
     * @param bool $overwrite_existent
     * @return void
     */
    static function set_overwrite_existent($overwrite_existent) {
        self::$overwrite_existent = $overwrite_existent;
    }

    /**
     * @return string|null
     */
    static function get_last_error() {
        return self::$last_error;
    }
}
