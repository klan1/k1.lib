<?php

namespace k1lib\html\foundation;

/**
 * 
 */
class accordion extends \k1lib\html\ul {

    use foundation_methods;

    /**
     *
     * @var array($tab_id, $tab_label) 
     */
    private $config_array = [];

    /**
     * @var \k1lib\html\div
     */
    private $tabs_content_container;

    /**
     * @var \k1lib\html\div
     */
    private $tabs_content = [];
    private $active_tab = 1;
    private $tab_count = 0;

    /**
     * generates an Foundation accordion
     * @param type $class
     * @param type $id
     */
    function __construct($class = null, $id = 'accordion', $class_config = 'small-accordion medium-tabs large-tabs') {

        parent::__construct('tabs', 'accordion-' . $id);
        $this->set_attrib('data-responsive-accordion-tabs', 'tabs ' . $class_config);
//        $this->append_to($content);
        $this->tabs_content_container = new \k1lib\html\div('tabs-content ' . $class, 'tabs-content-' . $id);
        $this->tabs_content_container->set_attrib('data-tabs-content', 'accordion-' . $id);
    }

    function set_config($confing_array) {
        $this->config_array = $confing_array;
        $c = 0;
        foreach ($this->config_array as $tab_id => $tab_label) {
            $c++;
            $this->new_li_tab($tab_id, $tab_label, $c);
//            $this->new_content_tab($tab_id, $c);
        }
    }

    /**
     * 
     * @param string $tab_id
     * @param string $value
     * @param int $c
     * @return \k1lib\html\div
     */
    function new_li_tab($tab_id, $value, $c = 0) {
        $this->tab_count++;
        if ($c == 0) {
            $c = $this->tab_count;
        }
        $this->append_li(null, ($c == $this->active_tab) ? 'tabs-title is-active' : 'tabs-title', 'acordeon-li-' . $tab_id)
                ->append_a('#' . 'accordion-content-' . urlencode($tab_id), strtoupper($value));
        return $this->new_content_tab($tab_id, $c);
    }

    /**
     * 
     * @param string $tab_id
     * @param int $c
     * @return \k1lib\html\div
     */
    private function new_content_tab($tab_id, $c = 0) {
        if ($c == 0) {
            $c = $this->tab_count;
        }
        $this->tabs_content[$tab_id] = new \k1lib\html\div(($c == $this->active_tab) ? 'tabs-panel is-active' : 'tabs-panel', 'accordion-content-' . urlencode($tab_id));
        $this->tabs_content[$tab_id]->append_to($this->tabs_content_container);
        return $this->tabs_content[$tab_id];
    }

    /**
     * @param string $tab_id
     * @return \k1lib\html\div
     */
    function tab_content($tab_id) {
        return $this->tabs_content[$tab_id];
    }

    /**
     * @return \k1lib\html\div
     */
    function tabs_content_container() {
        return $this->tabs_content_container;
    }

    function set_active_tab($active_tab): void {
        $this->active_tab = $active_tab;
    }

    /**
     * @param \k1lib\html
     * @return accordion
     */
    function append_to($html_object) {
        parent::append_to($html_object);
        $this->tabs_content_container->append_to($html_object);
        return $this;
    }
}
