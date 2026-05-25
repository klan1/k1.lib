<?php

namespace k1lib\lang;

use Exception;
use Gettext\Translator as GettextTranslator;
use Gettext\TranslatorFunctions;
use function __;
use const k1lib\K1LIB_ROOT;

class translator {

    /**
     * 
     * @var translator
     */
    private static $instance = null;
    private GettextTranslator $translator;
    private $currentLocale;
    private $availableLocales = ['en_US', 'es_CO'];
    private $domain = 'k1lib';
    private $context = '';

    private function __construct($locale) {
        $this->translator = new GettextTranslator();

        // Register the __() function globally
        TranslatorFunctions::register($this->translator);
//        $this->translator->
        // Set default locale
        $this->setLocale($locale);
    }

    /**
     * 
     * @param string $locale
     * @return translator
     */
    public static function getInstance($locale = 'es_CO'): translator {
        if (self::$instance === null) {
            self::$instance = new self($locale);
        }
        return self::$instance;
    }

    /**
     * Change the current language/locale
     */
    public function setLocale($locale, $domain = 'k1lib', $domainLocalesPath = null) {
        // Validate locale exists
        if (!in_array($locale, $this->availableLocales)) {
            throw new Exception("Locale '$locale' not available. Available: " . implode(', ', $this->availableLocales));
        }

        $this->currentLocale = $locale;

        if (!empty($domainLocalesPath) && file_exists($domainLocalesPath)) {
            $this->domain = $domain;
            $translationFile = $domainLocalesPath . "/{$locale}/{$domain}.php";

            if (file_exists($translationFile)) {
//            $translations = require $translationFile;
//            print_r($translations);
                $this->translator->loadTranslations($translationFile);
            } else {
                throw new Exception("Translation file not found for locale: $locale");
            }
        } else {
            // Load the translation file for this locale
            $translationFile = K1LIB_ROOT . "/../src/locales/{$locale}/{$this->domain}.php";

            if (file_exists($translationFile)) {
//            $translations = require $translationFile;
//            print_r($translations);
                $this->translator->loadTranslations($translationFile);
            } else {
                throw new Exception("Translation file not found for locale: $locale");
            }
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
    public function translate(string $domain, string $context, string $original) {
        if ($context) {
            return $this->translator->dpgettext($domain, $context, $original);
        }
        return __($original);
    }

    /**
     * Simple translation with context
     */
    public function t(string $original) {
        if ($this->domain && $this->context) {
            return $this->translator->dpgettext($this->domain, $this->context, $original);
        }
        return __($original);
    }

    public function d(string $domain, string $original) {
        if ($domain) {
            return $this->translator->dgettext($domain, $original);
        }
        return __($original);
    }

    public function c(string $context, string $original) {
        if ($context) {
            return $this->translator->pgettext($context, $original);
        }
        return __($original);
    }

    /**
     * Translation with singular/plural support
     */
    public function ngettext($singular, $plural, $count) {
        return $this->translator->ngettext($singular, $plural, $count);
    }
}
