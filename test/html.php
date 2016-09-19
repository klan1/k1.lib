<?php

include_once '../src/init.php';

$html = new k1lib\html\html_document_tag("es");
$html->head()->set_title("HTML TEST");

$html->body()->append_child_tail(new \k1lib\html\script_tag("https://code.jquery.com/jquery-3.1.0.min.js"));

$html->body()->append_p("Hello world");

$html->body()->append_div("new-div")->append_p("An P element inside the DIV element");

$html->generate_tag(TRUE);
