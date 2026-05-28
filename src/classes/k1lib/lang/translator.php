<?php

/**
 * @license Apache-2.0
 * @package k1lib
 * @subpackage lang
 * Internationalization and localization support using Gettext for multilingual applications.
 */

namespace k1lib\lang;

use Exception;
use Gettext\Translator as GettextTranslator;
use Gettext\TranslatorFunctions;
use function __;
use const k1lib\K1LIB_ROOT;

/**
 * Translator class providing internationalization support.
 * Implements singleton pattern for Gettext-based translations.
 *
 * @package k1lib\lang
 */
class translator {

    /**
     * Singleton instance of the translator.
     * @var translator
     */
    private static $instance = null;

    /**
     * Gettext translator instance.
     * @var GettextTranslator
     */
    private GettextTranslator $translator;

    /**
     * Current active locale identifier.
     * @var string
     */
    private $currentLocale;

    /**
     * List of available locales for the application.
     * @var array
     */
    private $availableLocales = ['en_US', 'es_CO'];

    /**
     * Translation domain for text retrieval.
     * @var string
     */
    private $domain = 'k1lib';

    /**
     * Translation context for disambiguation.
     * @var string
     */
    private $context = '';

    /**
     * Creates a new translator instance for the specified locale.
     *
     * @param string $locale The locale identifier (e.g., 'es_CO', 'en_US')
     */
    private function __construct($locale) {
        $this->translator = new GettextTranslator();

        // Register the __() function globally
        TranslatorFunctions::register($this->translator);
//        $this->translator->
        // Set default locale
        $this->setLocale($locale);
    }

    /**
     * Gets the singleton translator instance.
     *
     * @param string $locale Optional locale override (defaults to 'es_CO')
     * @return translator The singleton translator instance
     */
    public static function getInstance($locale = 'es_CO'): translator {
        if (self::$instance === null) {
            self::$instance = new self($locale);
        }
        return self::$instance;
    }

    /**
     * Changes the current language/locale for translations.
     *
     * @param string $locale The locale identifier to activate
     * @param string $domain Optional translation domain
     * @param string|null $domainLocalesPath Custom path to locale files
     * @return $this
     * @throws Exception If locale or translation file is not found
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
     * Gets the current active locale identifier.
     *
     * @return string The current locale
     */
    public function getCurrentLocale(): string {
        return $this->currentLocale;
    }

    /**
     * Gets all available locales for translation.
     *
     * @return array Array of available locale identifiers
     */
    public function getAvailableLocales() {
        return $this->availableLocales;
    }

    /**
     * Translates text with domain and context support.
     *
     * @param string $domain Translation domain
     * @param string $context Translation context for disambiguation
     * @param string $original The original text to translate
     * @return string Translated text
     */
    public function translate(string $domain, string $context, string $original) {
        if ($context) {
            return $this->translator->dpgettext($domain, $context, $original);
        }
        return __($original);
    }

    /**
     * Translates text using default domain and context.
     *
     * @param string $original The original text to translate
     * @return string Translated text
     */
    public function t(string $original) {
        if ($this->domain && $this->context) {
            return $this->translator->dpgettext($this->domain, $this->context, $original);
        }
        return __($original);
    }

    /**
     * Translates text using a specific domain.
     *
     * @param string $domain Translation domain
     * @param string $original The original text to translate
     * @return string Translated text
     */
    public function d(string $domain, string $original) {
        if ($domain) {
            return $this->translator->dgettext($domain, $original);
        }
        return __($original);
    }

    /**
     * Translates text with context using pgettext.
     *
     * @param string $context Translation context
     * @param string $original The original text to translate
     * @return string Translated text
     */
    public function c(string $context, string $original) {
        if ($context) {
            return $this->translator->pgettext($context, $original);
        }
        return __($original);
    }

    /**
     * Translates text with singular/plural forms support.
     *
     * @param string $singular Singular form of the text
     * @param string $plural Plural form of the text
     * @param int $count The count to determine singular or plural
     * @return string Translated text based on count
     */
    public function ngettext($singular, $plural, $count) {
        return $this->translator->ngettext($singular, $plural, $count);
    }
}
