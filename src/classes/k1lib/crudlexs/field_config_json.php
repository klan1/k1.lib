<?php

/**
 * 2025: JSON field configuration
 */

namespace k1lib\crudlexs;

/**
 * Description of table_config_json
 *
 * @author j0hnd003
 */
class field_config_json {

    const SCHEMA_VERSION = 1.3;
    const SCHEMA_TITLE = 'K1LIB-CRUDLEXS-CONFIG';

    private string|null $table;
    private string|null $field;
    private array $config;

    public function __construct(string|null $table = null, string|null $field = null) {
        $this->table = $table;
        $this->field = $field;
    }

    public function get_data(): array {
        return $this->config;
    }

    public function set_data(array $data): void {
        $this->config = $data;
    }

    public function get_json() {
        $json['schema-title'] = self::SCHEMA_TITLE;
        $json['schema-version'] = self::SCHEMA_VERSION;
        if (!empty($this->table)) {
            $json['table'] = $this->table;
        }
        if (!empty($this->field)) {
            $json['field'] = $this->field;
        }
        $json['config'] = $this->config;
        return json_encode($json, JSON_UNESCAPED_UNICODE);
    }
}
