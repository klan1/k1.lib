<?php

namespace k1lib\lang;

use Exception;
use Gettext\Translator;
use Gettext\TranslatorFunctions;

class t {

    private static $instance = null;
    private Translator $t;
    private $currentLocale;
    private $availableLocales = ['en_US', 'es_CO'];
    private $domains = [];
    private $currentDomain = 'k1lib'; // Default domain

    private function __construct() {
        $this->t = new Translator();

        // Register the __() function globally (uses default domain)
        TranslatorFunctions::register($this->t);

        // Register d__() function for domain-specific translations
        $this->registerDomainFunction();

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
     * Register custom function d__() for domain-specific translations
     */
    private function registerDomainFunction() {
        $t = $this;

        // Create global d__() function
        if (!function_exists('d__')) {

            function d__($domain, $text, $context = null) {
                return t::getInstance()->translateDomain($domain, $text, $context);
            }

        }

        // Create global dn__() for domain-specific plural translations
        if (!function_exists('dn__')) {

            function dn__($domain, $singular, $plural, $count) {
                return t::getInstance()->ngettextDomain($domain, $singular, $plural, $count);
            }

        }
    }

    /**
     * Load a translation domain
     * 
     * @param string $domain The translation domain name
     * @param string|null $locale The locale (defaults to current)
     * @param string|null $customPath Optional custom path to look for translation files
     * @param bool $mergeWithExisting If true, merges with existing translations for this domain
     * @return Translator The t instance
     */
    public function loadDomain($domain, $locale = null, $customPath = null, $mergeWithExisting = false) {
        $locale = $locale ?? $this->currentLocale;

        // Generate cache key based on domain, locale, and custom path
        $cacheKey = $domain . '_' . $locale;
        if ($customPath !== null) {
            $cacheKey .= '_' . md5($customPath);
        }

        // Check if domain is already loaded for this specific configuration
        if (!$mergeWithExisting && isset($this->domains[$cacheKey])) {
            return $this->domains[$cacheKey];
        }

        // Determine the translation file path
        $translationFile = null;

        if ($customPath !== null) {
            // Use custom path: {customPath}/{locale}/{domain}.php
            $translationFile = rtrim($customPath, '/') . "/{$locale}/{$domain}.php";
        } else {
            // Default framework path: __DIR__/../locales/{locale}/{domain}.php
            $translationFile = __DIR__ . "/../../locales/{$locale}/{$domain}.php";

            // Try alternative app path if not found
            if (!file_exists($translationFile)) {
                $altPath = __DIR__ . "/../../{$domain}/locales/{$locale}/{$domain}.php";
                if (file_exists($altPath)) {
                    $translationFile = $altPath;
                }
            }
        }

        // Check if file exists
        if (!file_exists($translationFile)) {
            if ($customPath !== null) {
                throw new Exception("Translation file not found for domain '$domain' in locale '$locale' at path: $translationFile");
            } else {
                throw new Exception("Translation file not found for domain '$domain' in locale '$locale' : $translationFile");
            }
        }

        // Load translations from file
        $translations = require $translationFile;

        // Create a new t instance or use existing
        $domainTranslator = null;

        if ($mergeWithExisting && isset($this->domains[$cacheKey])) {
            // Clone existing t to merge
            $domainTranslator = $this->domains[$cacheKey];
            $existingTranslations = $domainTranslator->getTranslations();
        } else {
            $domainTranslator = new Translator();
            $existingTranslations = [];
        }

        // Flatten translations if they have the nested structure
        $flatTranslations = [];
        if (isset($translations['messages'])) {
            foreach ($translations['messages'] as $context => $contextMessages) {
                foreach ($contextMessages as $original => $translated) {
                    $flatTranslations[$original] = $translated;
                }
            }
        } else {
            $flatTranslations = $translations;
        }

        // Merge with existing if needed
        if ($mergeWithExisting) {
            $flatTranslations = array_merge($existingTranslations, $flatTranslations);
        }

        $domainTranslator->loadTranslations($flatTranslations);

        // Store for later use
        $this->domains[$cacheKey] = $domainTranslator;

        // Also store under domain/locale for quick access if no custom path
        if ($customPath === null && !isset($this->domains[$domain][$locale])) {
            $this->domains[$domain][$locale] = $domainTranslator;
        }

        return $domainTranslator;
    }

    /**
     * Load multiple translation files for the same domain from different paths
     * 
     * @param string $domain The translation domain
     * @param array $paths Array of paths to load from (in order of priority)
     * @param string|null $locale The locale
     * @return Translator The merged t instance
     */
    public function loadDomainFromPaths($domain, array $paths, $locale = null) {
        $locale = $locale ?? $this->currentLocale;
        $mergedTranslator = new Translator();
        $allTranslations = [];

        foreach ($paths as $path) {
            try {
                $translationFile = rtrim($path, '/') . "/{$locale}/{$domain}.php";

                if (file_exists($translationFile)) {
                    $translations = require $translationFile;

                    // Flatten translations
                    $flatTranslations = [];
                    if (isset($translations['messages'])) {
                        foreach ($translations['messages'] as $context => $contextMessages) {
                            foreach ($contextMessages as $original => $translated) {
                                $flatTranslations[$original] = $translated;
                            }
                        }
                    } else {
                        $flatTranslations = $translations;
                    }

                    // Merge (later paths override earlier ones)
                    $allTranslations = array_merge($allTranslations, $flatTranslations);
                }
            } catch (Exception $e) {
                // Skip if file not found, continue with next path
                continue;
            }
        }

        $mergedTranslator->loadTranslations($allTranslations);

        // Store in domains with a special key
        $cacheKey = $domain . '_' . $locale . '_multi_' . md5(implode(',', $paths));
        $this->domains[$cacheKey] = $mergedTranslator;

        return $mergedTranslator;
    }

    /**
     * Change the current language/locale for all domains
     */
    public function setLocale($locale) {
        // Validate locale exists
        if (!in_array($locale, $this->availableLocales)) {
            throw new Exception("Locale '$locale' not available. Available: " . implode(', ', $this->availableLocales));
        }

        $this->currentLocale = $locale;

        // Reload all loaded domains with new locale
        foreach (array_keys($this->domains) as $key) {
            // Skip complex cache keys that don't follow domain/locale pattern
            if (strpos($key, '_') !== false && !isset($this->domains[$key][$locale])) {
                // Try to reload with original parameters if possible
                // For simplicity, we'll just clear and let them reload on demand
                unset($this->domains[$key]);
            } elseif (isset($this->domains[$key][$locale])) {
                $this->loadDomain($key, $locale);
            }
        }

        // Also load the default domain for the main t
        $this->loadDomain($this->currentDomain, $locale);

        // Load default translations into main t for __() function
        if (isset($this->domains[$this->currentDomain][$locale])) {
            $defaultTranslations = $this->domains[$this->currentDomain][$locale]->getTranslations();
            $this->t->loadTranslations($defaultTranslations);
        }

        return $this;
    }

    /**
     * Set the default domain for simple __() function
     */
    public function setDefaultDomain($domain) {
        $this->currentDomain = $domain;

        // Reload the default domain
        if ($this->currentLocale) {
            $this->loadDomain($domain, $this->currentLocale);
            if (isset($this->domains[$domain][$this->currentLocale])) {
                $translations = $this->domains[$domain][$this->currentLocale]->getTranslations();
                $this->t->loadTranslations($translations);
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
     * Add available locale
     */
    public function addAvailableLocale($locale) {
        if (!in_array($locale, $this->availableLocales)) {
            $this->availableLocales[] = $locale;
        }
        return $this;
    }

    /**
     * Simple translation with default domain
     */
    public function translate($text) {
        return __($text);
    }

    /**
     * Domain-specific translation
     */
    public function translateDomain($domain, $text, $context = null) {
        // Try to get domain t from standard location
        $tKey = null;

        // Check if domain exists in standard location
        if (isset($this->domains[$domain][$this->currentLocale])) {
            $tKey = $this->domains[$domain][$this->currentLocale];
        } else {
            // Try to load with default path
            $this->loadDomain($domain, $this->currentLocale);
            if (isset($this->domains[$domain][$this->currentLocale])) {
                $tKey = $this->domains[$domain][$this->currentLocale];
            }
        }

        if (!$tKey) {
            throw new Exception("Domain '$domain' not loaded");
        }

        if ($context) {
            return $tKey->pgettext($context, $text);
        }

        return $tKey->gettext($text);
    }

    /**
     * Translation with singular/plural support for default domain
     */
    public function ngettext($singular, $plural, $count) {
        return $this->t->ngettext($singular, $plural, $count);
    }

    /**
     * Domain-specific plural translation
     */
    public function ngettextDomain($domain, $singular, $plural, $count) {
        // Ensure domain is loaded
        if (!isset($this->domains[$domain][$this->currentLocale])) {
            $this->loadDomain($domain, $this->currentLocale);
        }

        $domainTranslator = $this->domains[$domain][$this->currentLocale];
        return $domainTranslator->ngettext($singular, $plural, $count);
    }

    /**
     * Context-aware translation for default domain
     */
    public function pgettext($context, $text) {
        return $this->t->pgettext($context, $text);
    }

    /**
     * Get a t instance for a specific domain (advanced usage)
     */
    public function getDomainTranslator($domain, $customPath = null) {
        if ($customPath !== null) {
            $cacheKey = $domain . '_' . $this->currentLocale . '_' . md5($customPath);
            if (!isset($this->domains[$cacheKey])) {
                $this->loadDomain($domain, $this->currentLocale, $customPath);
            }
            return $this->domains[$cacheKey];
        }

        if (!isset($this->domains[$domain][$this->currentLocale])) {
            $this->loadDomain($domain, $this->currentLocale);
        }

        return $this->domains[$domain][$this->currentLocale];
    }
}
