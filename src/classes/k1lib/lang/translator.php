<?php

namespace k1lib\lang;

use Exception;
use Gettext\Translator as GettextTranslator;
use Gettext\TranslatorFunctions;

class translator {

    private static $instance = null;
    private $translator;
    private $currentLocale;
    private $availableLocales = ['en_US', 'es_CO'];

    private function __construct() {
        $this->translator = new GettextTranslator();

        // Register the __() function globally
        TranslatorFunctions::register($this->translator);

        // Set default locale
        $this->setLocale('es_CO');
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Change the current language/locale
     */
    public function setLocale($locale) {
        // Validate locale exists
        if (!in_array($locale, $this->availableLocales)) {
            throw new Exception("Locale '$locale' not available. Available: " . implode(', ', $this->availableLocales));
        }

        $this->currentLocale = $locale;

        // Load the translation file for this locale
        $translationFile = __DIR__ . "/locales/{$locale}.php";

        if (file_exists($translationFile)) {
            $translations = require $translationFile;
            $this->translator->loadTranslations($translations);
        } else {
            throw new Exception("Translation file not found for locale: $locale");
        }

        return $this;
    }

    /**
     * Get current locale
     */
    public function getCurrentLocale() {
        return $this->currentLocale;
    }

    /**
     * Get all available locales
     */
    public function getAvailableLocales() {
        return $this->availableLocales;
    }

    /**
     * Simple translation with context
     */
    public function translate($text, $context = null) {
        if ($context) {
            return $this->translator->gettext($text, $context);
        }
        return __($text);
    }

    /**
     * Translation with singular/plural support
     */
    public function ngettext($singular, $plural, $count) {
        return $this->translator->ngettext($singular, $plural, $count);
    }
}
