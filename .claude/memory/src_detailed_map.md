---
name: src_detailed_map
description: Exhaustive file-by-file breakdown of the src/ directory and its sub-modules.
type: project
---

This file contains a complete mapping of the `src/` directory, discovered through recursive inspection.

### 1. `src/classes/k1lib/` (The Core Library)
- **`api/`**: Data access and modeling (`base.php`, `crud.php`, `model.php`).
- **`app/`**: Application lifecycle and controllers (`config.php`, `controller.php`, `controller_crud.php`).
- **`crudlexs/`**: The "CRUD-Logic" engine:
    - **`board/`**: Management of board entities (`board_base.php`, `board_interface.php`, `board_list.php`, `create.php`, `delete.php`, `read.php`, `search.php`, `update.php`).
    - **`controller/`**: Base controller logic for CRUD operations (`base.php`).
    - **`object/`**: Core object manipulation (`base.php`, `base_interface.php`, `base_with_data.php`, `creating.php`, `input_helper.php`, `listing.php`, `read-helper.php`, `reading.php`, `search_helper.php`, `updating.php`).
    - **`db_table.php` & `field_config_json.php`**: Table and field configuration.
- **`db/`**: Database abstraction layer:
    - `PDO_k1.php`, `handler.php`, `sql_defaults.php`.
    - **`security/`**: Security-related DB helpers (`db_table_aliases.php`).
- **`html/`**: A massive, granular HTML generation engine:
    - **`bootstrap/`**: Bootstrap-specific components (`accordion.php`, `bar.php`, `callout.php`, `grid.php`, `grid_cell.php`, `menu.php`, `modal.php`, `table_from_data.php`, `title_bar.php`, `top_bar.php`, `top_bar_.php`, `bootstrap_methods.php`, `grid_row.php`, `input_text_with_icon.php`, `label_value_row.php`).
    - **`_foundation/`**: Base foundation methods and components (`accordion.php`, `bar.php`, `callout.php`, `foundation_methods.php`, `grid.php`, `grid_cell.php`, `grid_row.php`, `label_value_row.php`, `menu.php`, `off_canvas.php`, `table_from_data.php`, `title_bar.php`, `top_bar.php`, `top_bar_.php`).
    - **Component Classes**: Individual files for every HTML tag/element (`a.php`, `body.php`, `button.php`, `div.php`, `fieldset.php`, `form.php`, `h1.php` through `h6.php`, `head.php`, `html_document.php`, `i.php`, `iframe.php`, `img.php`, `input.php`, `label.php`, `legend.php`, `li.php`, `link.php`, `meta.php`, `nav.php`, `ol.php`, `option.php`, `p.php`, `pre.php`, `script.php`, `section.php`, `select.php`, `small.php`, `span.php`, `strong.php`, `style.php`, `table.php`, `tag.php`, `tag_log.php`, `tbody.php`, `td.php`, `th.php`, `thead.php`, `title.php`, `tr.php`, `ul.php`, `textarea.php`).
    - **`notifications/`**: DOM-based notification logic (`common_code.php`, `on_DOM.php`).
    - **Utility Classes**: `DOM.php`, `_temply.php`, `tag_catalog.php`.
- **`urlrewrite/`**: URL manipulation (`url.php`).
- **Core Utilities**: `K1MAGIC.php`, `PROFILER.php`, `crypt.php`.

### 2. `src/functions/` (Procedural Helpers)
- A collection of global helper functions: `common.php`, `controllers.php`, `forms.php`, `global.php`, `html-common.php`, `html-tag-aplications.php`, `urlrewrite.php`, `utils.php`, and `xml.php`.

### 3. `src/lang/` (Localization)
- **`init-lang.php`**: Language initialization.
- **Language Folders (`en/`, `es/`)**: Translation files for core components (board, controller, db_table, object classes).

### 4. `src/error_handler/`
- Custom error handling logic (`error-handler.php`).

### Architecture Summary
The project uses a hybrid architecture: an object-oriented core (`k1lib`) for complex logic (HTML, DB, CRUD) paired with a procedural helper layer (`functions/`) for ease of use.
