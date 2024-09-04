<?php

namespace k1lib\crudlexs\object;

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
    static $error_new_password_not_match = "New password and confirmation must be equal";
    static $error_actual_password_not_match = "Actual password is incorrect";
    static $data_inserted = "Data saved";
    static $data_not_inserted = "Data not saved";

}

class listing_strings {

    /**
     *
     * @var string You can use:  --totalrowsfilter--, --totalrows--, --firstrownumber--, --lastrownumber--
     */
    static $stats_default_message = "Showing --totalrowsfilter-- of --totalrows-- (rows: --firstrownumber-- to --lastrownumber--)";
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
    static $password_set_successfully = "New password stored";
    static $data_updated = "Data updated";
    static $data_not_updated = "Data not updated";

}

class input_helper_strings {

    static $button_remove = "Remove --fieldvalue--";
    static $select_choose_option = "Select an option...";
    static $input_date_placeholder = "Click here to pick a date";
    static $input_fk_placeholder = "Use the reference ID";

}
