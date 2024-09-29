<?php

namespace k1lib\crudlexs\board;

class board_base_strings
{

    static $alert_board = "Alerta";
    static $error_board = "Mensaje de error";
    static $error_board_disabled = "Este tablero esta deshabilitado o no estas habilitado para usarlo";
    static $error_mysql = "Error de Base de Datos";
    static $error_mysql_table_not_opened = "No se puede habrir la tabla";
    static $error_mysql_table_no_data = "Consulta vacia";
    static $error_url_keys_no_auth = "Las key no pueden estar vacias, no puedes continuar";
    static $error_url_keys_no_keys_text = "No puedes usar este tabler sin el correspondiente texto de keys";

}

class board_create_strings
{

    static $error_no_inserted = "Los datos no han sido insertados";
    static $error_form = "Por favor corrija los siguientes errores:";
    static $error_no_blank_data = "Los datos en blanco no pudieron ser creados";

}

class board_delete_strings
{

    static $data_deleted = "Dato eliminado";
    static $error_no_data_deleted = "El registro solicitado no puede ser borrado";
    static $error_no_data_deleted_hacker = "Interesante que intentes borrar un registro con el auth-code de lectura ;)";

}

class board_list_strings
{

    static $no_table_data = "Sin datos para mostrar";

    /**
     * BUTTON LABELS
     */
    static $button_new = "Nuevo";
    static $button_search = "Buscar";
    static $button_search_modify = "Modificar busqueda";
    static $button_search_cancel = "Cancelar busqueda";
    /**
     * FK tool
     */
    static $select_fk_tool_title = 'Selecciona el registro para usar en el formulario';
    static $select_fk_tool_subtitle = 'Puedes hacer una busqueda y hacer clic en la columna con link.';

}

class board_read_strings
{

    static $button_all_data = "Ver listado";
    static $button_back = "Volver";
    static $button_edit = "Editar";
    static $button_delete = "Borrar";

}

class board_update_strings
{

    static $button_submit = "Actualizar";
    static $error_no_inserted = "Daton sin modificar, los has dejado igual?";
    static $error_form = "Por favor corrija los siguientes errores:";
    static $error_no_blank_data = "Por favor corrija los siguientes errores:";

}
