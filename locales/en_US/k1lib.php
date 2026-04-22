<?php

return [
    'domain' => 'k1lib',
    'plural-forms' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);',
    'messages' => [
        '' => [
            'error' => 'Error',
            'error_hacker' => 'Error! Is there a hacker near ?', // Grammar fix
            'alert' => 'Alert',
            'info' => 'Information',
            'message' => 'Message', // Corrected typo from Menssage
            'button_submit' => 'Create', // Translated from Spanish
            'button_cancel' => 'Cancel',
            'yes' => 'Yes',
            'no' => 'No',
            'fk_tool_name' => 'Select the Foreign Key', // Corrected spelling
        ],
        'other-context' => [
            'Multibyte test' => '日本人は日本で話される言語です！',
            'Tabulation test' => 'FIELD\tFIELD',
        ],
        'crudlexs-board-classes' => [
            'Board Class' => 'Board Class',
        ],
        'crudlexs-controller-class' => [
            'The table name has to be a String' => 'The table name has to be a String',
            'Create new' => 'Create new',
            'Update details' => 'Update details',
            'Delete registry' => 'Delete registry',
            'The board hasn\'t inited yet' => 'The board hasn\'t initialized yet', // Grammar fix
            'The board hasn\'t started yet' => 'The board hasn\'t started yet',
            'The board hasn\'t executed yet' => 'The board hasn\'t executed yet',
        ],
        'crudlexs-db-table-class' => [
            'The table name has to be a String' => 'The table name has to be a String',
            'The Show Rule do not exist' => 'The Show Rule does not exist', // Grammar fix
            'Data to insert can\'t be empty' => 'Data to insert can\'t be empty',
            'Data to update can\'t be empty' => 'Data to update can\'t be empty',
            'Key to update can\'t be empty' => 'Key to update can\'t be empty',
            'Key to delete can\'t be empty' => 'Key to delete can\'t be empty',
        ],
        'crudlexs-object-classes' => [
            'Bad, bad! auth code' => 'Bad, bad! auth code',
            'Auth code can\'t be empty' => 'Auth code can\'t be empty',
            'The array is not compatible' => 'The array is not compatible',
            'Can\'t work without table data loaded first' => 'Can\'t work without table data loaded first',
            'There is not rand number on session data' => 'There is no random number in session data', // Improved phrasing
            'The row keys can\'t be empty' => 'The row keys can\'t be empty',
            'The row keys array can\'t be empty' => 'The row keys array can\'t be empty',
            'Insert' => 'Insert',
            'Cancel' => 'Cancel',
            'File upload error : ' => 'File upload error: ', // Consistency fix
            'New password and confirmation must be equal' => 'New password and confirmation must be equal',
            'Actual password is incorrect' => 'Actual password is incorrect',
            'Data saved' => 'Data saved',
            'Data not saved' => 'Data not saved',
            'Showing --totalrowsfilter-- of --totalrows-- (rows: --firstrownumber-- to --lastrownumber--)' => 'Showing --totalrowsfilter-- of --totalrows-- (rows: --firstrownumber-- to --lastrownumber--)',
            'Search on another table is not possible here, use the Key value to search' => 'Searching on another table is not possible here, use the Key value to search', // Improved phrasing
            'Search' => 'Search',
            'Exit' => 'Exit',
            'Update' => 'Update',
            'Back' => 'Back',
            'New password stored' => 'New password stored',
            'Data updated' => 'Data updated',
            'Data not updated' => 'Data not updated',
            'Remove --fieldvalue--' => 'Remove --fieldvalue--',
            'Select an option...' => 'Select an option...',
            'Click here to pick a date' => 'Click here to pick a date',
            'Use the reference ID' => 'Use the reference ID',
            'Current password' => 'Current password',
            'New password' => 'New password',
            'Confirm password' => 'Confirm password',
        ],
        'crudlexs-board-create-strings' => [
            'Los datos no han sido insertados' => 'Data not inserted', // Translated from Spanish
            'Por favor corrija los siguientes errores:' => 'Please correct the following errors:', // Translated from Spanish
            'Los datos en blanco no pudieron ser creados' => 'Blank data could not be created', // Translated from Spanish
        ],
        'crudlexs-board-delete-strings' => [
            'Dato eliminado' => 'Data deleted', // Translated from Spanish
            'El registro solicitado no puede ser borrado' => 'The requested record cannot be deleted', // Translated from Spanish
            'Interesante que intentes borrar un registro con el auth-code de lectura ;)' => 'Interesting that you try to delete a record with the read auth-code ;)'. // Translated from Spanish
        ],
        'crudlexs-board-list-strings' => [
            'Sin datos para mostrar' => 'No data to show', // Translated from Spanish
            'Nuevo' => 'New', // Translated from Spanish
            'Buscar' => 'Search',
            'Modificar busqueda' => 'Modify search', // Translated from Spanish
            'Cancelar busqueda' => 'Cancel search', // Translated from Spanish
            'Selecciona el registro para usar en el formulario' => 'Select the record to use in the form', // Translated from Spanish
            'Puedes hacer una busqueda y hacer clic en la columna con link.' => 'You can search and click on the column with a link.', // Translated from Spanish
        ],
        'crudlexs-board-read-strings' => [
            'Ver listado' => 'View list', // Translated from Spanish
            'Volver' => 'Back', // Translated from Spanish
            'Editar' => 'Edit', // Translated from Spanish
            'Borrar' => 'Delete', // Translated from Spanish
        ],
        'crudlexs-board-update-strings' => [
            'Actualizar' => 'Update', // Translated from Spanish
            'Daton sin modificar, los has dejado igual?' => 'Data not modified, did you leave it the same?', // Translated from Spanish
            'Por favor corrija los siguientes errores:' => 'Please correct the following errors:', // Translated from Spanish
            'Los datos en blanco no pudieron ser creados' => 'Blank data could not be created', // Translated from Spanish
        ],
        'crudlexs-db-table-class' => [
            // Key already exists, value is English. No change needed based on rules 1-3.
        ],
        'crudlexs-object-classes' => [
            // Keys already exist, values are English. No change needed based on rules 1-3.
        ],
    ],
];