<?php

/**
 * Spanish CRUDLexs board class strings
 *
 * @license Apache-2.0
 * @package k1lib
 */

namespace k1lib\crudlexs\board;

class board_base_strings {

    /** @var string */
    static $alert_board = "Alerta";
    /** @var string */
    static $error_board = "Mensaje de error";
    /** @var string */
    static $error_board_disabled = "Este tablero esta deshabilitado o no estas habilitado para usarlo";
    /** @var string */
    static $error_mysql = "Error de Base de Datos";
    /** @var string */
    static $error_mysql_table_not_opened = "No se puede habrir la tabla";
    /** @var string */
    static $error_mysql_table_no_data = "Consulta vacia";
    /** @var string */
    static $error_url_keys_no_auth = "Las key no pueden estar vacias, no puedes continuar";
    /** @var string */
    static $error_url_keys_no_keys_text = "No puedes usar este tabler sin el correspondiente texto de keys";
}

class board_create_strings {

    /** @var string */
    static $error_no_inserted = "Los datos no han sido insertados";
    /** @var string */
    static $error_form = "Por favor corrija los siguientes errores:";
    /** @var string */
    static $error_no_blank_data = "Los datos en blanco no pudieron ser creados";
}

class board_delete_strings {

    /** @var string */
    static $data_deleted = "Dato eliminado";
    /** @var string */
    static $error_no_data_deleted = "El registro solicitado no puede ser borrado";
    /** @var string */
    static $error_no_data_deleted_hacker = "Interesante que intentes borrar un registro con el auth-code de lectura ;)";
}

class board_list_strings {

    /** @var string */
    static $no_table_data = "Sin datos para mostrar";

    /**
     * BUTTON LABELS
     * @var string
     */
    static $button_new = "Nuevo";
    /** @var string */
    static $button_search = "Buscar";
    /** @var string */
    static $button_search_modify = "Modificar busqueda";
    /** @var string */
    static $button_search_cancel = "Cancelar busqueda";

    /**
     * FK tool
     * @var string
     */
    static $select_fk_tool_title = 'Selecciona el registro para usar en el formulario';
    /** @var string */
    static $select_fk_tool_subtitle = 'Puedes hacer una busqueda y hacer clic en la columna con link.';
}

class board_read_strings {

    /** @var string */
    static $button_all_data = "Ver listado";
    /** @var string */
    static $button_back = "Volver";
    /** @var string */
    static $button_edit = "Editar";
    /** @var string */
    static $button_delete = "Borrar";
}

class board_update_strings {

    /** @var string */
    static $button_submit = "Actualizar";
    /** @var string */
    static $error_no_inserted = "Daton sin modificar, los has dejado igual?";
    /** @var string */
    static $error_form = "Por favor corrija los siguientes errores:";
    /** @var string */
    static $error_no_blank_data = "Por favor corrija los siguientes errores:";
}