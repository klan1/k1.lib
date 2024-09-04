<?php

namespace k1lib\html;

/**
 * Holds all the tags created with the tag Class.
 */
class tag_catalog {

    /**
     * @var integer 
     */
    static protected $catalog = [];

    /**
     * @var array 
     */
    static protected $index = 0;

    /**
     * Gets the actual index position.
     * @return integer
     */
    static function get_index() {
        return self::$index;
    }

    /**
     * Get a tag Object form catalog using the ID to search on Catalog index
     * @param integer $index
     * @return tag|NULL
     */
    static function get_by_index($index) {
        if (self::index_exist($index)) {
            return self::$catalog[$index];
        } else {
            return NULL;
        }
    }

    /**
     * Checks if and index exist. If the tag have been decataloged wont be found.
     * @param integer $index
     * @return boolean
     */
    static function index_exist($index) {
        if (isset(self::$catalog[$index])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Increase the index value and returns the new index value.
     * @param \k1lib\html\tag $tag_object
     * @return integer
     */
    static function increase(tag $tag_object) {
        self::$index++;
        self::$catalog[self::$index] = $tag_object;
        return self::$index;
    }

    /**
     * Remove the tag Object from the Array catalog, this will disable the 
     * Object to be found or generated on chain actions.
     * @param integer|\k1lib\html\tag $tag_index
     */
    static function decatalog($tag_index) {
        if (is_object($tag_index) && method_exists($tag_index, "get_tag_id")) {
            $tag_index = $tag_index->get_tag_id();
        }
        if (isset(self::$catalog[$tag_index])) {
//            self::$catalog[$tag_index] = NULL;
            unset(self::$catalog[$tag_index]);
        }
    }

    /**
     * Returns all the tag Object Catalog Array
     * @return tag[]
     */
    static function get_catalog() {
        return self::$catalog;
    }

}

