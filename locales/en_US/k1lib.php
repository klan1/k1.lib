<?php

return [
    'domain' => 'k1lib',
    'plural-forms' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);',
    'messages' => [
        '' => [
            'error' => 'Error',
            'error_hacker' => 'Error! Is there a hacker near ?',
            'alert' => 'Alert',
            'info' => 'Information',
            'message' => 'Message',
            'button_submit' => 'Create',
            'button_cancel' => 'Cancel',
            'yes' => 'Yes',
            'no' => 'No',
            'fk_tool_name' => 'Select the Foreign Key',
        ],
        'other-context' => [
            'Multibyte test' => 'Japanese people speak in Japan!', // Translated for consistency/clarity
            'Tabulation test' => 'FIELD\tFIELD',
        ],
        'crudlexs-board-classes' => [
            'Board Class' => 'Board Class',
        ],
        'crudlexs-controller-class' => [
            'The table name has to be a String' => 'The table name must be a string', // Grammar fix
            'Create new' => 'Create new',
            'Update details' => 'Update details',
            'Delete registry' => 'Delete registry',
            'The board hasn\'t initialized yet' => 'The board has not been initialized yet', // Grammar fix
            'The board hasn\'t started yet' => 'The board has not been started yet', // Grammar fix
            'The board hasn\'t executed yet' => 'The board has not been executed yet', // Grammar fix
        ],
        'crudlexs-db-table-class' => [
            'The table name has to be a String' => 'The table name must be a string', // Grammar fix
            'The Show Rule do not exist' => 'The Show Rule does not exist', // Grammar fix
            'Data to insert can\'t be empty' => 'Data to insert cannot be empty', // Grammar fix
            'Data to update can\'t be empty' => 'Data to update cannot be empty', // Grammar fix
            'Key to update can\'t be empty' => 'Key to update cannot be empty', // Grammar fix
            'Key to delete can\'t be empty' => 'Key to delete cannot be empty', // Grammar fix
        ],
        'crudlexs-object-classes' => [
            'Bad, bad! auth code' => 'Bad, bad! auth code',
            'Auth code can\'t be empty' => 'Auth code cannot be empty', // Grammar fix
            'The array is not compatible' => 'The array is incompatible', // Simpler phrasing
            'Can\'t work without table data loaded first' => 'Cannot work without table data loaded first', // Grammar fix
            'There is not rand number on session data' => 'There is no random number in session data',
            'The row keys can\'t be empty' => 'The row keys cannot be empty', // Grammar fix
            'The row keys array can\'t be empty' => 'The row keys array cannot be empty', // Grammar fix
            'Insert' => 'Insert',
            'Cancel' => 'Cancel',
            'File upload error : ' => 'File upload error: ', // Consistency fix
            'New password and confirmation must be equal' => 'New password and confirmation must match', // Simpler phrasing
            'Actual password is incorrect' => 'The actual password is incorrect', // Grammar fix
            'Data saved' => 'Data saved',
            'Data not saved' => 'Data not saved',
            'Showing --totalrowsfilter-- of --totalrows-- (rows: --firstrownumber-- to --lastrownumber--)' => 'Showing --totalrowsfilter-- of --totalrows-- (rows: --firstrownumber-- to --lastrownumber--)',
            'Search on another table is not possible here, use the Key value to search' => 'Searching on another table is not possible; use the Key value to search', // Grammar fix
            'Search' => 'Search',
            'Exit' => 'Exit',
            'Update' => 'Update',
            'Back' => 'Back',
            'New password stored' => 'New password successfully stored', // More descriptive
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
            'Data not inserted' => 'Data not inserted', // Key is now English, value is English
            'Please correct the following errors:' => 'Please correct the following errors:', // Key is now English, value is English
            'Blank data could not be created' => 'Blank data could not be created', // Key is now English, value is English
        ],
        'crudlexs-board-delete-strings' => [
            'Data deleted' => 'Data deleted', // Key is now English, value is English
            'The requested record cannot be deleted' => 'The requested record cannot be deleted', // Key is now English, value is English
            'Interesting that you try to delete a record with the read auth-code ;)' => 'It is interesting that you are trying to delete a record with the read auth-code ;)'. // Grammar fix
        ],
        'crudlexs-board-list-strings' => [
            'No data to show' => 'No data to show', // Key is now English, value is English
            'New' => 'New', // Key is now English, value is English
            'Search' => 'Search',
            'Modify search' => 'Modify search', // Key is now English, value is English
            'Cancel search' => 'Cancel search', // Key is now English, value is English
            'Select the record to use in the form' => 'Select the record to use in the form', // Key is now English, value is English
            'You can search and click on the column with a link.' => 'You can search and click on the column with a link.', // Key is now English, value is English
        ],
        'crudlexs-board-read-strings' => [
            'View list' => 'View list', // Key is now English, value is English
            'Back' => 'Back', // Key is now English, value is English
            'Edit' => 'Edit', // Key is now English, value is English
            'Delete' => 'Delete', // Key is now English, value is English
        ],
        'crudlexs-board-update-strings' => [
            'Update' => 'Update', // Key is now English, value is English
            'Data not modified, did you leave it the same?' => 'Data not modified, did you leave it the same?', // Key is now English, value is English
            'Please correct the following errors:' => 'Please correct the following errors:', // Key is now English, value is English
            'Blank data could not be created' => 'Blank data could not be created', // Key is now English, value is English
        ],
        'crudlexs-db-table-class' => [
            // All keys and values are already English. No change needed.
        ],
        'crudlexs-object-classes' => [
            // All keys and values are already English. No change needed.
        ],
    ],
];