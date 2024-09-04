<?php

namespace k1lib;

/*
  /**
 * Includes ALL files on a directory.
 */
if (!defined("K1LIB_LANG")) {
    define("K1LIB_LANG", "en");
}
k1lib_include_files(K1LIB_BASE_PATH . "/lang/" . K1LIB_LANG);
