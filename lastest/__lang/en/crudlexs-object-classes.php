<?php

namespace k1lib\crudlexs;

class object_base_strings {

    /**
     * __construct()
     */
    static $error_bad_auth_code = "Bad, bad! auth code";
    static $alert_empty_auth_code = "Auth code can't be empty";
    //
    static $error_array_not_compatible = "The array is not compatible";
    //
    static $error_no_table_data = "Can't work without table data loaded first";
    //
    static $error_no_session_random = "There is not rand number on session data";
    //
    static $error_no_row_keys_text = "The row keys can't be empty";
    static $error_no_row_keys_array = "The row keys array can't be empty";

}

class creating_strings {

    static $button_submit = "Insert";
    static $button_cancel = "Cancel";
    static $error_file_upload = "File upload error : ";

}

class listing_strings {

    /**
     *
     * @var string You can use:  %total-rows-filter%, %total-rows%, %first-row-number%, %last-row-number%
     */
    static $stats_default_message = "Showing %total-rows-filter% of %total-rows% (rows: %first-row-number% to %last-row-number%)";
    //
    static $no_fk_search_here = "Search on another table is not possible here, use the Key value to search";

}

class search_helper_strings {

    static $button_submit = "Search";
    static $button_cancel = "Exit";

}

class updating_strings {

    static $button_submit = "Update";
    static $button_cancel = "Back";

}

class input_helper_strings {

    static $button_remove = "Remove %field-value%";
    static $select_choose_option = "Select an option...";
    static $input_date_placeholder = "Click here to pick a date";
    static $input_fk_placeholder = "Use the reference ID";

}
