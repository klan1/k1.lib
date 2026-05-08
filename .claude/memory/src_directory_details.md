---
name: src_directory_details
description: Detailed breakdown of the src/ directory and its sub-modules.
type: project
---

The `src/` directory contains the core application logic and is organized into several functional modules:

### Core Modules
- **`classes/k1lib/`**: The primary library, highly modularized:
    - **`api/`**: Handles CRUD and model logic (`crud.php`, `model.php`).
    - **`app/`**: Contains application controllers (`controller.php`).
    - **`crudlexs/`**: Extensive CRUD implementation, including `board/` management and `object/` helpers (reading, searching, updating).
    - **`db/`**: Database interaction layer (`handler.php`, `sql_defaults.php`).
    - **`html/`**: A comprehensive HTML generation engine:
        - `bootstrap/`: Bootstrap-specific components (modals, grids, etc.).
        - `_foundation/`: Base HTML component logic.
        - Individual element classes (e.g., `button.php`, `form.php`, `input.php`).
    - **`urlrewrite/`**: URL manipulation utilities.
    - **Core Utilities**: `K1MAGIC.php`, `PROFILER.php`, and `crypt.php`.
- **`functions/`**: Procedural helper functions for common tasks (`common.php`, `controllers.php`, `html-common.php`, `utils.php`, `xml.php`).
- **`lang/`**: Localization and language initialization (`init-lang.php`).
- **`error_handler/`**: Custom error handling logic (`error-handler.php`).

### Architecture Summary
The project uses a hybrid architecture: an object-oriented core (`k1lib`) for complex logic (HTML, DB, CRUD) paired with a procedural helper layer (`functions/`) for ease of use.
