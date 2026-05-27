<?php

/**
 * Spanish CRUDLexs object class strings
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
    static $button_submit = "Crear";
    /** @var string */
    static $button_cancel = "Cancelar";
    /** @var string */
    static $error_file_upload = "File upload error : ";
    /** @var string */
    static $error_new_password_not_match = "El nuevo password y la confirmacion deben ser iguales";
    /** @var string */
    static $error_actual_password_not_match = "Contraseña actual incorrecta";
    /** @var string */
    static $data_inserted = "Datos guardados";
    /** @var string */
    static $data_not_inserted = "Los datos no han sido ingresados";
}

class listing_strings {

    /**
     * @var string You can use: --totalrowsfilter--, --totalrows--, --firstrownumber--, --lastrownumber--
     */
    static $stats_default_message = "Mostrando --totalrowsfilter-- de --totalrows-- (filas: --firstrownumber-- to --lastrownumber--)";
    /** @var string */
    static $no_fk_search_here = "Busquedas en otra tabla no es posible en esta seccion.";
}

class search_helper_strings {

    /** @var string */
    static $button_submit = "Buscar";
    /** @var string */
    static $button_cancel = "Salir";
}

class updating_strings {

    /** @var string */
    static $button_submit = "Actualizar";
    /** @var string */
    static $button_cancel = "Volver";
    /** @var string */
    static $password_set_successfully = "Nueva contraseña aceptada";
    /** @var string */
    static $data_updated = "Datos actualizados";
    /** @var string */
    static $data_not_updated = "Datos no actualizados";
}

class input_helper_strings {

    /** @var string */
    static $button_remove = "Borrar --fieldvalue--";
    /** @var string */
    static $select_choose_option = "Seleccione una opcion...";
    /** @var string */
    static $input_date_placeholder = "Haga clic para seleccionar una fecha";
    /** @var string */
    static $input_fk_placeholder = "Use el ID de referencia";
    /** @var string */
    static $password_current = 'Contraseña actual';
    /** @var string */
    static $password_new = 'Nueva contraseña';
    /** @var string */
    static $password_confirm = 'Confirma la contraseña';
}