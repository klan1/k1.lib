<?php

function k1_get_file_extension($file_name, $to_lower = false) {
    if (!is_string($file_name)) {
        k1_show_error("The file name to check only can be a string", __FUNCTION__, true);
    }
    $last_dot_pos = strrpos($file_name, ".");
    if ($last_dot_pos !== false) {
        //trim the ?query url
        $last_question_pos = strrpos($file_name, "?");
        if ($last_question_pos !== false) {
            $file_name = substr($file_name, 0, $last_question_pos);
        }
        //extension
        $file_extension = substr($file_name, $last_dot_pos + 1);
        if ($to_lower) {
            return strtolower($file_extension);
        } else {
            return $file_extension;
        }
    } else {
        return false;
    }
}


