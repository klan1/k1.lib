<?php

/**
 * English CRUDLexs object class strings
 *
 * @license Apache-2.0
 * @package k1lib
 */

namespace k1lib\crudlexs\object;

class object_base_strings {

    /** @var string */
    static $error_bad_auth_code = "Bad, bad! auth code";
    /** @var string */
    static $alert_empty_auth_code = "Auth code can't be empty";
    /** @var string */
    static $error_array_not_compatible = "The array is not compatible";
    /** @var string */
    static $error_no_table_data = "Can't work without table data loaded first";
    /** @var string */
    static $error_no_session_random = "There is not rand number on session data";
    /** @var string */
    static $error_no_row_keys_text = "The row keys can't be empty";
    /** @var string */
    static $error_no_row_keys_array = "The row keys array can't be empty";
}

class creating_strings {

    /** @var string */
    static $button_submit = "Insert";
    /** @var string */
    static $button_cancel = "Cancel";
    /** @var string */
    static $error_file_upload = "File upload error : ";
    /** @var string */
    static $error_new_password_not_match = "New password and confirmation must be equal";
    /** @var string */
    static $error_actual_password_not_match = "Actual password is incorrect";
    /** @var string */
    static $data_inserted = "Data saved";
    /** @var string */
    static $data_not_inserted = "Data not saved";
}

class listing_strings {

    /**
     * @var string You can use: --totalrowsfilter--, --totalrows--, --firstrownumber--, --lastrownumber--
     */
    static $stats_default_message = "Showing --totalrowsfilter-- of --totalrows-- (rows: --firstrownumber-- to --lastrownumber--)";
    /** @var string */
    static $no_fk_search_here = "Search on another table is not possible here, use the Key value to search";
}

class search_helper_strings {

    /** @var string */
    static $button_submit = "Search";
    /** @var string */
    static $button_cancel = "Exit";
}

class updating_strings {

    /** @var string */
    static $button_submit = "Update";
    /** @var string */
    static $button_cancel = "Back";
    /** @var string */
    static $password_set_successfully = "New password stored";
    /** @var string */
    static $data_updated = "Data updated";
    /** @var string */
    static $data_not_updated = "Data not updated";
}

class input_helper_strings {

    /** @var string */
    static $button_remove = "Remove --fieldvalue--";
    /** @var string */
    static $select_choose_option = "Select an option...";
    /** @var string */
    static $input_date_placeholder = "Click here to pick a date";
    /** @var string */
    static $input_fk_placeholder = "Use the reference ID";
    /** @var string */
    static $password_current = 'Current password';
    /** @var string */
    static $password_new = 'New password';
    /** @var string */
    static $password_confirm = 'Confirm password';
}