---
name: api_model_details
description: Detailed analysis of the k1lib\api\model class.
type: project
---

The `k1lib\api\model` class acts as the data-mapping layer between the API controllers and the database table abstraction (`db_table`). It handles the translation of input data into object properties and executes CRUD operations via the `db_table` instance.

### Core Responsibilities
- **Data Mapping**: 
    - `assing_data_to_properties()`: Maps input data (from arrays or objects) to the class's own properties. This allows the model to act as a data container for the specific table it represents.
    - `get_data_from_params()`: Extracts relevant data from the object's properties based on the database table's configuration, ensuring only valid fields are sent to the DB.
- **CRUD Operations**:
    - `get_data(array $custom_key_array)`: Retrieves a single record. It uses the provided keys (or extracts them from input) to set filters on the `db_table` and then fetches the data.
    - `get_all_data(...)`: Handles paginated list retrieval, applying offsets, limits, filters, and ordering.
    - `insert_data()`: Uses the current object properties to perform a database insertion via `db_table`.
    - `update_data($keyfields)`: Updates existing records using the provided key fields and the data currently held in the object's properties.
    - `delete_data($keyfields)`: Deletes records based on the provided key fields.
- **Error Handling**: Maintains an internal `$errors` state, which is populated by `db_table` operations and can be retrieved via `get_errors()`.

### Key Logic & Workflows
- **The `db_table` Dependency**: The class is tightly coupled with an instance of `k1lib\crudlexs\db_table`. All database-level logic (SQL generation, execution) is delegated to this object.
- **Dynamic Property Assignment**: The model uses PHP's dynamic property access (`$this->{$field}`) to map incoming API data directly to class properties, facilitating a "magic" mapping between JSON input and database columns.
- **Pagination Logic**: `get_all_data` calculates the SQL offset based on the provided page number and page size.

### Summary of Workflow
1. **Controller** (e.g., `crud`) instantiates the `model` with a `db_table`.
2. **Controller** calls an operation (e.g., `update_data`).
3. **Model** extracts valid data from its own properties via `get_data_from_params()`.
4. **Model** delegates the SQL execution to `db_table` using that data.
5. **Model** returns the result or error state to the Controller.
