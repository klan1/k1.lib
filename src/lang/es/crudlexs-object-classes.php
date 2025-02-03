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

    static $button_submit = "Crear";
    static $button_cancel = "Cancelar";
    static $error_file_upload = "File upload error : ";
    static $error_new_password_not_match = "El nuevo password y la confirmacion deben ser iguales";
    static $error_actual_password_not_match = "Contraseña actual incorrecta";
    static $data_inserted = "Datos guardados";
    static $data_not_inserted = "Los datos no han sido ingresados";
}

class listing_strings {

    /**
     *
     * @var string You can use:  --totalrowsfilter--, --totalrows--, --firstrownumber--, --lastrownumber--
     */
    static $stats_default_message = "Mostrando --totalrowsfilter-- de --totalrows-- (filas: --firstrownumber-- to --lastrownumber--)";
    //
    static $no_fk_search_here = "Busquedas en otra tabla no es posible en esta seccion.";

}

class search_helper_strings {

    static $button_submit = "Buscar";
    static $button_cancel = "Salir";

}

class updating_strings {

    static $button_submit = "Actualizar";
    static $button_cancel = "Volver";
    static $password_set_successfully = "Nueva contraseña aceptada";
    static $data_updated = "Datos actualizados";
    static $data_not_updated = "Datos no actualizados";
}

class input_helper_strings {

    static $button_remove = "Borrar --fieldvalue--";
    static $select_choose_option = "Seleccione una opcion...";
    static $input_date_placeholder = "Haga clic para seleccionar una fecha";
    static $input_fk_placeholder = "Use el ID de referencia";

}
