<?php

namespace k1lib\crudlexs;

use k1lib\templates\temply as temply;

class board_base {

    /**
     * DB table main object
     * @var \k1lib\crudlexs\controller_base 
     */
    protected $controller_object;

    /**
     *
     * @var \k1lib\html\div_tag;
     */
    public $board_content_div;

    /**
     *
     * @var boolean
     */
    protected $data_loaded = FALSE;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object) {
        $this->controller_object = $controller_object;
    }

    public function set_board_name($board_name) {
        temply::set_place_value($this->controller_object->get_template_place_name_html_title(), " - {$board_name}");
        temply::set_place_value($this->controller_object->get_template_place_name_controller_name(), " - {$board_name}");
    }

}

class board_base_strings {

    static $alert_board = "Alert";
    static $error_board = "Error message";
    static $error_mysql = "DB error";
    static $error_mysql_table_not_opened = "Can not open the table.";
    static $error_mysql_table_no_data = "Empty query";

}
