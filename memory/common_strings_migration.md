---
name: common_strings_migration
description: Migration of common_strings from src/lang/ to locales/
type: project
---

The migration of `common_strings` is being handled by injecting the English source strings as keys and their Spanish translations as values into `locales/en_US/k1lib.php` and `locales/es_CO/k1lib.php`.

**Why:** This follows the pattern established in your existing `es_CO/k1lib.php` where English strings serve as the lookup keys.

**How to apply:** After updating the locale files, we will refactor code using `common_strings::$property` to `$t->t('k1lib', '', 'Original English String')`.
