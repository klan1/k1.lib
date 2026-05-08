---
name: project_structure
description: Overview of the project's file structure and architecture.
type: project
---

The project is a PHP-based web application using Composer for dependency management.

### Core Structure
- **`src/`**: Main application logic, including `error_handler/`. Contains `.htaccess` for Apache configuration.
- **`vendor/`**: Third-party PHP libraries (e.g., `psr/cache`, `whichbrowser/parser`).
- **`router.php`**: Central routing file used for local PHP CLI server execution during development.
- **`index.html`**: Static entry point or landing page.
- **`start-server.sh`**: Shell script for starting a local development server.

### Architecture
The application follows a typical web pattern where requests are likely routed through `router.php` and managed via the files in `src/`.
