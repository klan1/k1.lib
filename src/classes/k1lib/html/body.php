<?php

namespace k1lib\html;

/**
 * This is the body of HTML document 
 *  <body>
 *      <section id='k1lib-header'></section>
 *      <section id='k1lib-content'></section>
 *      <section id='k1lib-footer'></section>
 *  </body>
 */
class body extends tag {

    use append_shotcuts;

    /**
     * @var section
     */
    protected $section_header = NULL;

    /**
     * @var section
     */
    protected $section_content = NULL;

    /**
     * @var section
     */
    protected $section_footer = NULL;

    function __construct() {
        parent::__construct("body", FALSE);
    }

    function init_sections(tag $where = NULL) {
        $this->section_header = new section("k1lib-header", "hide-for-print");
        $this->section_content = new section("k1lib-content");
        $this->section_content->set_attrib("style", "overflow-x: auto;");
        $this->section_footer = new section("k1lib-footer", "hide-for-print");
        if (empty($where)) {
            $this->section_header->append_to($this);
            $this->section_content->append_to($this);
            $this->section_footer->append_to($this);
        } else {
            $this->section_header->append_to($where);
            $this->section_content->append_to($where);
            $this->section_footer->append_to($where);
        }
    }

    function disable_sections() {
        $this->section_header->decatalog();
        $this->section_content->decatalog();
        $this->section_footer->decatalog();
    }

    /**
     * return section|body
     */
    function header() {
        if (!empty($this->section_header)) {
            return $this->section_header;
        } else {
            return $this;
        }
    }

    /**
     * return section|body
     */
    function content() {
        if (!empty($this->section_content)) {
            return $this->section_content;
        } else {
            return $this;
        }
    }

    /**
     * return section|body
     */
    function footer() {
        if (!empty($this->section_footer)) {
            return $this->section_footer;
        } else {
            return $this;
        }
    }

}

