<?php

class net_klan1_dev_dbTableClass {

    public $db;
    private $tableName = "";
    private $tableConfigArray = "";
    private $table_label_field = "";
// Definitions
    private $mysql_max_length_defaults = array(
        'char' => 255,
        'varchar' => 255,
        'text' => 9999,
        'date' => 10,
        'time' => 8,
        'datetime' => 19,
        'timestamp' => 19,
        'tinyint' => 3,
        'smallint' => 5,
        'mediumint' => 7,
        'int' => 10,
        'bigint' => 19,
        'float' => 34,
        'double' => 64,
        'enum' => null,
    );
    private $mysql_default_validation = array(
        'char' => 'mixed-simbols',
        'varchar' => 'mixed-simbols',
        'text' => 'mixed-simbols',
        'date' => 'date',
        'time' => 'time',
        'datetime' => 'datetime',
        'timestamp' => 'datetime',
        'tinyint' => 'numbers',
        'smallint' => 'numbers',
        'mediumint' => 'numbers',
        'int' => 'numbers',
        'bigint' => 'numbers',
        'float' => 'decimals',
        'double' => 'numbers',
        'enum' => 'options',
    );
    private $show_array_attribs = Array(
        'show-table',
        'show-new',
        'show-edit',
        'show-view',
    );

    public function __construct(PDO $db, $tableName) {

// Check DB Type
        if (get_class($db) == "PDO") {
            $this->db = $db;
        } else {
            die("\$db is not a PDO object type - called from: " . __CLASS__);
        };

// check $tableName type
        if (is_string($tableName)) {
            $this->tableName = $tableName;
        } else {
            die("The table name has to be a String");
        }

        $this->tableConfigArray = $this->_getTableFieldConfig($tableName);
        $this->table_label_field = $this->_getTableLabel($this->tableConfigArray);
    }

    public function getTableLabel() {
        return $this->table_label_field;
    }

    private static function _getTableLabel(&$tableConfigArray) {
        return k1_get_table_label($tableConfigArray);
    }

    public function getTableFieldConfig() {
        return $this->tableConfigArray;
    }

    private function _getTableFieldConfig($table, $recursion = 1) {
        return k1_get_table_config($this->db, $table, $recursion);
    }

}
