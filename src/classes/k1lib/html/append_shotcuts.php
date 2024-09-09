<?php

namespace k1lib\html;

/**
 * Common tag Objects append operations
 */
trait append_shotcuts
{

    /**
     * 
     * @param string $class
     * @param string $id
     * @return div
     */
    function append_div($class = NULL, $id = NULL)
    {
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
    function append_span($class = NULL, $id = NULL)
    {
        $new = new span($class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $value
     * @param string $class
     * @param string $id
     * @return p
     */
    function append_p($value = NULL, $class = NULL, $id = NULL)
    {
        $new = new p($value, $class, $id);
        $this->append_child($new);
        return $new;
    }
    /**
     * @param string $class
     * @param string $id
     * @return ul
     */
    function append_ul($class = NULL, $id = NULL)
    {
        $new = new ul($class, $id);
        $this->append_child($new);
        return $new;
    }
    /**
     * @param string $value
     * @param string $class
     * @param string $id
     * @return li
     */
    function append_li($value = NULL, $class = NULL, $id = NULL)
    {
        $new = new li($value, $class, $id);
        $this->append_child($new);
        return $new;
    }
    /**
     * @param string $value
     * @param string $class
     * @param string $id
     * @return i
     */
    function append_i($value = NULL, $class = NULL, $id = NULL)
    {
        $new = new i($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return pre
     */
    function append_pre($value = NULL, $class = NULL, $id = NULL)
    {
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
    function append_a($href = NULL, $label = NULL, $target = NULL, $class = NULL, $id = NULL)
    {
        $new = new a($href, $label, $target, $class, $id);
        $this->append_child($new);
        return $new;
    }

    function append_img($src = NULL, $alt = 'Image', $class = NULL, $id = NULL): img
    {
        $new = new img($src, $alt, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return h1
     */
    function append_h1($value = NULL, $class = NULL, $id = NULL)
    {
        $new = new h1($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return h2
     */
    function append_h2($value = NULL, $class = NULL, $id = NULL)
    {
        $new = new h2($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return h3
     */
    function append_h3($value = NULL, $class = NULL, $id = NULL)
    {
        $new = new h3($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return h4
     */
    function append_h4($value = NULL, $class = NULL, $id = NULL)
    {
        $new = new h4($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return h5
     */
    function append_h5($value = NULL, $class = NULL, $id = NULL)
    {
        $new = new h5($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return h6
     */
    function append_h6($value = NULL, $class = NULL, $id = NULL)
    {
        $new = new h6($value, $class, $id);
        $this->append_child($new);
        return $new;
    }
}
