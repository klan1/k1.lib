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

    function __construct() {
        parent::__construct("body", FALSE);
    }
}
