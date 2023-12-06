<?php

namespace k1lib\html;

/**
 * Common tag Objects append operations
 */
trait append_shotcuts {

    /**
     * 
     * @param string $class
     * @param string $id
     * @return div
     */
    function append_div($class = NULL, $id = NULL) {
        $new = new div($class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * 
     * @param string $class
     * @param string $id
     * @return span
     */
    function append_span($class = NULL, $id = NULL) {
        $new = new span($class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return p
     */
    function append_p($value = NULL, $class = NULL, $id = NULL) {
        $new = new p($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return pre
     */
    function append_pre($value = NULL, $class = NULL, $id = NULL) {
        $new = new pre($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * 
     * @param string $href
     * @param string $label
     * @param string $target
     * @param string $alt
     * @param string $class
     * @param string $id
     * @return a
     */
    function append_a($href = NULL, $label = NULL, $target = NULL, $class = NULL, $id = NULL) {
        $new = new a($href, $label, $target, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return p
     */
    function append_h1($value = NULL, $class = NULL, $id = NULL) {
        $new = new h1($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return p
     */
    function append_h2($value = NULL, $class = NULL, $id = NULL) {
        $new = new h2($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return p
     */
    function append_h3($value = NULL, $class = NULL, $id = NULL) {
        $new = new h3($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return p
     */
    function append_h4($value = NULL, $class = NULL, $id = NULL) {
        $new = new h4($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return p
     */
    function append_h5($value = NULL, $class = NULL, $id = NULL) {
        $new = new h5($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return p
     */
    function append_h6($value = NULL, $class = NULL, $id = NULL) {
        $new = new h6($value, $class, $id);
        $this->append_child($new);
        return $new;
    }
}

require_once __DIR__ . '/DOM.php';

require_once __DIR__ . '/html.php';

require_once __DIR__ . '/head.php';

require_once __DIR__ . '/title.php';

require_once __DIR__ . '/section.php';

require_once __DIR__ . '/meta.php';

require_once __DIR__ . '/body.php';

require_once __DIR__ . '/a.php';

require_once __DIR__ . '/img.php';

require_once __DIR__ . '/div.php';

require_once __DIR__ . '/ul.php';

require_once __DIR__ . '/ol.php';

require_once __DIR__ . '/link.php';

require_once __DIR__ . '/li.php';

require_once __DIR__ . '/script.php';

require_once __DIR__ . '/style.php';

require_once __DIR__ . '/small.php';

require_once __DIR__ . '/span.php';

require_once __DIR__ . '/strong.php';

require_once __DIR__ . '/table.php';

require_once __DIR__ . '/thead.php';

require_once __DIR__ . '/tbody.php';

require_once __DIR__ . '/tr.php';

require_once __DIR__ . '/th.php';

require_once __DIR__ . '/td.php';

require_once __DIR__ . '/input.php';

require_once __DIR__ . '/textarea.php';

require_once __DIR__ . '/label.php';

require_once __DIR__ . '/select.php';

require_once __DIR__ . '/option.php';

require_once __DIR__ . '/form.php';

require_once __DIR__ . '/p.php';

require_once __DIR__ . '/h1.php';

require_once __DIR__ . '/h2.php';

require_once __DIR__ . '/h3.php';

require_once __DIR__ . '/h4.php';

require_once __DIR__ . '/h5.php';

require_once __DIR__ . '/h6.php';

require_once __DIR__ . '/fieldset.php';

require_once __DIR__ . '/legend.php';

require_once __DIR__ . '/pre.php';

require_once __DIR__ . '/iframe.php';

require_once __DIR__ . '/button.php';

