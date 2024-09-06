<?php

namespace k1lib\html;

/**
 * This is the main object that will holds all the HTML document.
 * <html>  
 *  <head>
 *      <title></title>
 *  </head>
 *  <body>
 *  </body>
 * </html> 
 */
class html_document extends tag
{

    use append_shotcuts;

    /**
     * @var head
     */
    protected head $head;

    /**
     * @var body
     */
    protected body $body;

    function __construct($lang = "en", $custom_head = false, $custom_body = false)
    {
        parent::__construct("html", FALSE);

        $this->pre_code("<!DOCTYPE html>\n");
        $this->set_attrib("lang", $lang);

        if (!$custom_head) {
            $this->head = new head();
            $this->append_child($this->head);
        }
        if (!$custom_body) {
            $this->body = new body();
            $this->append_child($this->body);
        }
    }

    /**
     * @return head
     */
    function head()
    {
        return $this->head;
    }

    /**
     * @return body
     */
    function body()
    {
        return $this->body;
    }
}
