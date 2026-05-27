<?php

/**
 * English CRUDLexs board class strings
 *
 * @license Apache-2.0
 * @package k1lib
 */

namespace k1lib\crudlexs\board;

class board_base_strings {

    /** @var string */
    static $alert_board = "Alert";
    /** @var string */
    static $error_board = "Error message";
    /** @var string */
    static $error_board_disabled = "This board is disabled or you are not allowed to use it";
    /** @var string */
    static $error_mysql = "DB error";
    /** @var string */
    static $error_mysql_table_not_opened = "Can not open the table.";
    /** @var string */
    static $error_mysql_table_no_data = "Empty query";
    /** @var string */
    static $error_url_keys_no_auth = "Keys are not valid, so, you can't continue";
    /** @var string */
    static $error_url_keys_no_keys_text = "You can't use this board without the right url key text";
}

class board_create_strings {

    /** @var string */
    static $error_no_inserted = "Data hasn't been inserted.";
    /** @var string */
    static $error_form = "Please correct the marked errors:";
    /** @var string */
    static $error_no_blank_data = "The blank data couldn't be created.";
}

class board_delete_strings {

    /** @var string */
    static $data_deleted = "Data deleted";
    /** @var string */
    static $error_no_data_deleted = "The record to delete can't be deleted";
    /** @var string */
    static $error_no_data_deleted_hacker = "Too genius of you trying to delete something with a normal auth-code ;)";
}

class board_list_strings {

    /** @var string */
    static $no_table_data = "No data to show";

    /**
     * BUTTON LABELS
     * @var string
     */
    static $button_new = "Add new";
    /** @var string */
    static $button_search = "Search";
    /** @var string */
    static $button_search_modify = "Modify search";
    /** @var string */
    static $button_search_cancel = "Cancel search";

    /**
     * FK tool
     * @var string
     */
    static $select_fk_tool_title = 'Select record to use on Form';
    /** @var string */
    static $select_fk_tool_subtitle = 'You can search and do click on the link column.';
}

class board_read_strings {

    /** @var string */
    static $button_all_data = "All data";
    /** @var string */
    static $button_back = "Back";
    /** @var string */
    static $button_edit = "Edit";
    /** @var string */
    static $button_delete = "Delete";
}

class board_update_strings {

    /** @var string */
    static $button_submit = "Update";
    /** @var string */
    static $error_no_inserted = "Data hasn't benn updated. Did you leave all unchaged ?";
    /** @var string */
    static $error_form = "Please correct the marked errors:";
    /** @var string */
    static $error_no_blank_data = "Please correct the marked errors:";
}