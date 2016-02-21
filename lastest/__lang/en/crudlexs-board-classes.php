<?php

namespace k1lib\crudlexs;

class board_base_strings {

    static $alert_board = "Alert";
    static $error_board = "Error message";
    static $error_board_disabled = "This board is disabled or you are not allowed to use it";
    static $error_mysql = "DB error";
    static $error_mysql_table_not_opened = "Can not open the table.";
    static $error_mysql_table_no_data = "Empty query";
    static $error_url_keys_no_auth = "Keys are not valid, so, you can't continue";
    static $error_url_keys_no_keys_text = "You can't use this board without the right url key text";

}

class board_create_strings {

    static $error_no_inserted = "Data hasn't been inserted.";
    static $error_form = "Please correct the marked errors.";
    static $error_no_blank_data = "The blank data couldn't be created.";

}

class board_delete_strings {

    static $error_no_data_deleted = "The record to delete can't be deleted";
    static $error_no_data_deleted_hacker = "Too genius of you trying to delete something with a normal auth-code ;)";

}

class board_list_strings {

    /**
     * BUTTON LABELS
     */
    static $button_new = "Add new";
    static $button_search = "Search";
    static $button_search_modify = "Modify search";
    static $button_search_cancel = "Cancel search";

}

class board_read_strings {

    static $button_all_data = "All data";
    static $button_back = "Back";
    static $button_edit = "Edit";
    static $button_delete = "Delete";

}

class board_update_strings {

    static $button_submit = "Update";
    static $error_no_inserted = "Data hasn't benn updated. Did you leave all unchaged ?";
    static $error_form = "Please correct the marked errors.";
    static $error_no_blank_data = "Please correct the marked errors.";

}
